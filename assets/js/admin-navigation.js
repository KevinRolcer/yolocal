(function () {
  if (window.__adminPartialNavigationInitialized) {
    return;
  }
  window.__adminPartialNavigationInitialized = true;

  var DYNAMIC_SCRIPT_ATTR = "data-admin-dynamic-script";
  var activeRequestId = 0;

  function isAdminLayout() {
    return Boolean(
      document.querySelector(".navigation.admin-sidebar") &&
        document.querySelector(".main")
    );
  }

  function getMainElements() {
    var main = document.querySelector(".main");
    if (!main) {
      return null;
    }
    var topbar = main.querySelector(":scope > .topbar") || null;
    return { main: main, topbar: topbar };
  }

  function getMenuLinks() {
    return Array.prototype.slice.call(
      document.querySelectorAll(".navigation.admin-sidebar .main-menu a")
    );
  }

  function sameOriginAdminUrl(rawHref) {
    try {
      var url = new URL(rawHref, window.location.href);
      if (url.origin !== window.location.origin) {
        return null;
      }
      if (!url.searchParams.get("pag")) {
        return null;
      }
      return url;
    } catch (_error) {
      return null;
    }
  }

  function updateActiveMenu(targetUrl) {
    var links = getMenuLinks();
    var targetPag = targetUrl.searchParams.get("pag") || "home";

    links.forEach(function (link) {
      var li = link.closest("li");
      if (!li) {
        return;
      }
      li.classList.remove("active");

      var linkUrl = sameOriginAdminUrl(link.getAttribute("href"));
      if (!linkUrl) {
        return;
      }
      var linkPag = linkUrl.searchParams.get("pag") || "home";

      var samePage = linkPag === targetPag;
      var sharedGroup =
        linkPag === "bolsa_trabajo" &&
        (targetPag === "bolsa_trabajo" || targetPag === "eventos");

      if (samePage || sharedGroup) {
        li.classList.add("active");
      }
    });
  }

  function extractContentNodes(main) {
    var children = Array.prototype.slice.call(main.children);
    var topbarIndex = children.findIndex(function (node) {
      return node.classList && node.classList.contains("topbar");
    });

    if (topbarIndex === -1) {
      return children;
    }

    return children.slice(topbarIndex + 1);
  }

  function clearMainContent(main, topbar) {
    var children = Array.prototype.slice.call(main.children);
    children.forEach(function (child) {
      if (topbar && child === topbar) {
        return;
      }
      child.remove();
    });
  }

  function shouldSkipDynamicScript(src) {
    var normalized = (src || "").toLowerCase();
    return (
      normalized.indexOf("bootstrap") !== -1 ||
      normalized.indexOf("sweetalert") !== -1 ||
      normalized.indexOf("notificaciones.js") !== -1 ||
      normalized.indexOf("admin-navigation.js") !== -1 ||
      normalized.indexOf("main.js") !== -1
    );
  }

  function cleanupDynamicScripts() {
    var old = document.querySelectorAll("script[" + DYNAMIC_SCRIPT_ATTR + "]");
    old.forEach(function (script) {
      script.remove();
    });
  }

  function syncAdminStylesheets(doc) {
    var head = document.head;
    var currentPageStyles = Array.prototype.slice.call(
      head.querySelectorAll('link[rel="stylesheet"][data-admin-css="page"]')
    );
    var incomingPageStyles = Array.prototype.slice.call(
      doc.querySelectorAll('link[rel="stylesheet"][data-admin-css="page"]')
    );

    var incomingHrefs = incomingPageStyles.map(function (link) {
      return new URL(link.getAttribute("href"), window.location.href).toString();
    });

    currentPageStyles.forEach(function (link) {
      var href = new URL(link.getAttribute("href"), window.location.href).toString();
      if (incomingHrefs.indexOf(href) === -1) {
        link.remove();
      }
    });

    incomingPageStyles.forEach(function (link) {
      var absoluteHref = new URL(link.getAttribute("href"), window.location.href).toString();
      var existing = currentPageStyles.find(function (styleLink) {
        return new URL(styleLink.getAttribute("href"), window.location.href).toString() === absoluteHref;
      });

      if (existing) {
        return;
      }

      var styleLink = document.createElement("link");
      styleLink.rel = "stylesheet";
      styleLink.href = absoluteHref;
      styleLink.setAttribute("data-admin-css", "page");
      head.appendChild(styleLink);
    });
  }

  function loadScriptsFromDocument(doc) {
    cleanupDynamicScripts();

    var scripts = Array.prototype.slice.call(doc.querySelectorAll("script"));
    var sequence = Promise.resolve();

    scripts.forEach(function (scriptNode) {
      var src = scriptNode.getAttribute("src");

      if (src) {
        if (shouldSkipDynamicScript(src)) {
          return;
        }

        sequence = sequence.then(function () {
          return new Promise(function (resolve) {
            var script = document.createElement("script");
            var absoluteSrc = new URL(src, window.location.href);
            absoluteSrc.searchParams.set("pjax", String(Date.now()));
            script.src = absoluteSrc.toString();
            script.async = false;
            if (scriptNode.type) {
              script.type = scriptNode.type;
            }
            script.setAttribute(DYNAMIC_SCRIPT_ATTR, "1");
            script.onload = function () {
              resolve();
            };
            script.onerror = function () {
              resolve();
            };
            document.body.appendChild(script);
          });
        });
        return;
      }

      var inlineCode = (scriptNode.textContent || "").trim();
      if (!inlineCode) {
        return;
      }

      sequence = sequence.then(function () {
        var script = document.createElement("script");
        if (scriptNode.type) {
          script.type = scriptNode.type;
        }
        script.setAttribute(DYNAMIC_SCRIPT_ATTR, "1");
        script.textContent = inlineCode;
        document.body.appendChild(script);
      });
    });

    return sequence;
  }

  function replaceMainContentFromDocument(doc) {
    var current = getMainElements();
    var incomingMain = doc.querySelector(".main");
    if (!current || !incomingMain) {
      return false;
    }

    var incomingNodes = extractContentNodes(incomingMain);

    clearMainContent(current.main, current.topbar);
    var fragment = document.createDocumentFragment();
    incomingNodes.forEach(function (node) {
      fragment.appendChild(node.cloneNode(true));
    });
    current.main.appendChild(fragment);

    return true;
  }

  function syncBodyState(doc) {
    var incomingBody = doc.body;
    if (!incomingBody) {
      return;
    }

    document.body.className = incomingBody.className || "";
  }

  function fetchAndSwap(url, pushState) {
    var requestId = ++activeRequestId;

    return fetch(url.toString(), {
      method: "GET",
      headers: {
        "X-Requested-With": "XMLHttpRequest"
      }
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error("No se pudo cargar la seccion");
        }
        return response.text();
      })
      .then(function (html) {
        if (requestId !== activeRequestId) {
          return;
        }

        var parser = new DOMParser();
        var doc = parser.parseFromString(html, "text/html");
        syncBodyState(doc);
        syncAdminStylesheets(doc);
        var swapped = replaceMainContentFromDocument(doc);
        if (!swapped) {
          window.location.href = url.toString();
          return;
        }

        document.title = doc.title || document.title;
        updateActiveMenu(url);

        if (pushState) {
          history.pushState({ adminPartial: true }, "", url.toString());
        }

        return loadScriptsFromDocument(doc);
      })
      .catch(function () {
        window.location.href = url.toString();
      });
  }

  function onMenuClick(event) {
    var link = event.target.closest("a");
    if (!link) {
      return;
    }

    var url = sameOriginAdminUrl(link.getAttribute("href"));
    if (!url) {
      return;
    }

    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
      return;
    }

    event.preventDefault();
    fetchAndSwap(url, true);
  }

  function onPopState() {
    if (!isAdminLayout()) {
      return;
    }
    var url = sameOriginAdminUrl(window.location.href);
    if (!url) {
      return;
    }
    fetchAndSwap(url, false);
  }

  function init() {
    if (!isAdminLayout()) {
      return;
    }

    var menu = document.querySelector(".navigation.admin-sidebar .main-menu");
    if (!menu) {
      return;
    }

    menu.addEventListener("click", onMenuClick);
    window.addEventListener("popstate", onPopState);

    var currentUrl = sameOriginAdminUrl(window.location.href);
    if (currentUrl) {
      updateActiveMenu(currentUrl);
    }
  }

  document.addEventListener("DOMContentLoaded", init);
})();
