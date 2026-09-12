(() => {
  const navigation = document.querySelector(".navigation.admin-sidebar");
  const main = document.querySelector(".main");
  const originalToggle = main?.querySelector(".topbar .toggle");
  const sidebar = navigation?.querySelector(".sidebar");
  if (!originalToggle || !sidebar || navigation.dataset.menuInitialized) return;
  navigation.dataset.menuInitialized = "true";

  const toggle = document.createElement("button");
  toggle.type = "button";
  toggle.className = originalToggle.className;
  toggle.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" /></svg>';
  originalToggle.replaceWith(toggle);
  sidebar.id = sidebar.id || "admin-sidebar-menu";
  toggle.setAttribute("aria-controls", sidebar.id);

  const closeButton = sidebar.querySelector(".admin-sidebar-close");
  const backdrop = document.createElement("button");
  backdrop.type = "button";
  backdrop.className = "admin-sidebar-backdrop";
  backdrop.setAttribute("aria-label", "Cerrar menú de navegación");
  backdrop.tabIndex = -1;
  navigation.prepend(backdrop);

  const mobileViewport = window.matchMedia("(max-width: 991px)");
  const initialOverflow = document.documentElement.style.overflow;

  function setMenuActive(active, restoreFocus = false) {
    const mobile = mobileViewport.matches;
    const drawerOpen = mobile && active;
    navigation.classList.toggle("active", active);
    main.classList.toggle("active", active);
    toggle.setAttribute("aria-expanded", String(mobile ? active : !active));
    toggle.setAttribute("aria-label", mobile
      ? (active ? "Cerrar menú de navegación" : "Abrir menú de navegación")
      : (active ? "Expandir menú de navegación" : "Contraer menú de navegación"));
    sidebar.inert = mobile && !active;
    main.inert = drawerOpen;
    backdrop.hidden = !drawerOpen;
    document.documentElement.style.overflow = drawerOpen ? "hidden" : initialOverflow;
    if (drawerOpen) closeButton?.focus();
    else if (restoreFocus) toggle.focus();
  }

  function closeMobileMenu() {
    if (mobileViewport.matches) setMenuActive(false, true);
  }

  toggle.addEventListener("click", () => setMenuActive(!navigation.classList.contains("active")));
  closeButton?.addEventListener("click", closeMobileMenu);
  backdrop.addEventListener("click", closeMobileMenu);
  sidebar.addEventListener("click", (event) => {
    if (event.target.closest("a") && !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey) {
      closeMobileMenu();
    }
  });
  document.addEventListener("keydown", (event) => {
    if (!mobileViewport.matches || !navigation.classList.contains("active")) return;
    if (event.key === "Escape") closeMobileMenu();
    if (event.key !== "Tab") return;
    const controls = sidebar.querySelectorAll('button, a[href]');
    const first = controls[0];
    const last = controls[controls.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last?.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first?.focus();
    }
  });
  mobileViewport.addEventListener("change", () => setMenuActive(false, true));
  setMenuActive(false);
})();
