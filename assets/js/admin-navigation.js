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

    var stagedStyles = [];
    var orderedStyles = [];
    var loadingStyles = incomingPageStyles.map(function (link) {
      var absoluteHref = new URL(link.getAttribute("href"), window.location.href).toString();
      var existing = currentPageStyles.find(function (styleLink) {
        return new URL(styleLink.getAttribute("href"), window.location.href).toString() === absoluteHref;
      });

      if (existing) {
        orderedStyles.push({ link: existing, media: link.media });
        return Promise.resolve();
      }

      var styleLink = link.cloneNode(true);
      styleLink.href = absoluteHref;
      styleLink.media = "not all";
      styleLink.removeAttribute("data-admin-css");
      stagedStyles.push(styleLink);
      orderedStyles.push({ link: styleLink, media: link.media });
      return new Promise(function (resolve, reject) {
        var timeout = setTimeout(function () {
          finish(new Error("Se agotó el tiempo de carga de los estilos"));
        }, 15000);
        function finish(error) {
          clearTimeout(timeout);
          styleLink.onload = null;
          styleLink.onerror = null;
          if (error) reject(error);
          else resolve();
        }
        styleLink.onload = function () { finish(); };
        styleLink.onerror = function () { finish(new Error("No se pudieron cargar los estilos")); };
        head.appendChild(styleLink);
      });
    });

    function discard() {
      stagedStyles.forEach(function (link) { link.remove(); });
    }

    return Promise.all(loadingStyles).then(function () {
      return {
        discard: discard,
        apply: function () {
          currentPageStyles.forEach(function (link) {
            if (!orderedStyles.some(function (style) { return style.link === link; })) link.remove();
          });
          orderedStyles.forEach(function (style) {
            style.link.media = style.media;
            style.link.setAttribute("data-admin-css", "page");
            head.appendChild(style.link);
          });
          head.querySelectorAll("style[data-admin-inline]").forEach(function (style) { style.remove(); });
          doc.head.querySelectorAll("style").forEach(function (style) {
            var clone = style.cloneNode(true);
            clone.setAttribute("data-admin-inline", "1");
            head.appendChild(clone);
          });
        }
      };
    }).catch(function (error) {
      discard();
      throw error;
    });
  }

  function loadScriptsFromDocument(doc, requestId) {
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
          if (requestId !== activeRequestId) return;
          return new Promise(function (resolve) {
            var script = document.createElement("script");
            var absoluteSrc = new URL(src, window.location.href);
            var externalLibrary = absoluteSrc.origin !== window.location.origin;
            if (externalLibrary && Array.prototype.some.call(document.scripts, function (loaded) {
              return loaded.src === absoluteSrc.toString();
            })) {
              resolve();
              return;
            }
            // Los módulos locales necesitan una nueva evaluación para enlazar el DOM reemplazado.
            if (!externalLibrary && scriptNode.type === "module") {
              absoluteSrc.searchParams.set("pjax", String(requestId));
            }
            script.src = absoluteSrc.toString();
            script.async = false;
            if (scriptNode.type) {
              script.type = scriptNode.type;
            }
            if (!externalLibrary) script.setAttribute(DYNAMIC_SCRIPT_ATTR, "1");
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
        if (requestId !== activeRequestId) return;
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
      cache: "no-store",
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
      .then(async function (html) {
        if (requestId !== activeRequestId) {
          return;
        }

        var parser = new DOMParser();
        var doc = parser.parseFromString(html, "text/html");
        if (!doc.querySelector(".main")) {
          window.location.href = url.toString();
          return;
        }
        var styles = await syncAdminStylesheets(doc);
        if (requestId !== activeRequestId) {
          styles.discard();
          return;
        }
        styles.apply();
        syncBodyState(doc);
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

        return loadScriptsFromDocument(doc, requestId);
      })
      .catch(function () {
        if (requestId === activeRequestId) window.location.href = url.toString();
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

    document.head.querySelectorAll("style").forEach(function (style) {
      style.setAttribute("data-admin-inline", "1");
    });
    menu.addEventListener("click", onMenuClick);
    window.addEventListener("popstate", onPopState);

    var currentUrl = sameOriginAdminUrl(window.location.href);
    if (currentUrl) {
      updateActiveMenu(currentUrl);
    }
  }

  document.addEventListener("DOMContentLoaded", init);
})();
