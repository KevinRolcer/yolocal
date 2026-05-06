(() => {
  function ensureMatterLoaded() {
    if (window.Matter) return Promise.resolve(true);
    const candidates = [
      "../node_modules/matter-js/build/matter.min.js",
      "https://cdn.jsdelivr.net/npm/matter-js@0.20.0/build/matter.min.js",
    ];

    return new Promise((resolve) => {
      let idx = 0;
      const tryNext = () => {
        if (window.Matter) return resolve(true);
        if (idx >= candidates.length) return resolve(false);

        const src = candidates[idx++];
        const script = document.createElement("script");
        script.src = src;
        script.async = true;
        script.onload = () => {
          if (window.Matter) resolve(true);
          else tryNext();
        };
        script.onerror = tryNext;
        document.head.appendChild(script);
      };
      tryNext();
    });
  }

  function setupMenuToggle() {
    const body = document.body;
    const toggle = document.getElementById("eventsMenuToggle");
    if (!body || !toggle) return;

    const updateText = () => {
      const hidden = body.classList.contains("events-menu-hidden");
      toggle.setAttribute("aria-expanded", hidden ? "false" : "true");
      toggle.setAttribute("aria-label", hidden ? "Mostrar menú" : "Ocultar menú");
    };

    toggle.addEventListener("click", () => {
      body.classList.toggle("events-menu-hidden");
      body.classList.toggle("events-menu-visible");
      updateText();
    });

    updateText();
  }

  function initPhysics() {
  const MatterGlobal = window.Matter;
  if (!MatterGlobal) return false;

  const {
    Engine,
    Render,
    Runner,
    Bodies,
    Composite,
    Body,
    Common,
    Mouse,
    MouseConstraint,
    Query,
  } = MatterGlobal;

  /* Hero + cada evento: círculos Matter.js; en spotlight el canvas no recibe clicks (solo ambiente). */
  const SECTION_SELECTOR = ".snap-section.events-hero, .snap-section.event-spotlight";
  const storyScrollEl = document.querySelector(".events-story");

  /** Punto Matter (coords de mundo) desde un touch igual que Matter.Mouse */
  function heroTouchPoint(evt, canvas, mouse) {
    const t =
      (evt.touches && evt.touches[0]) ||
      (evt.changedTouches && evt.changedTouches[0]);
    if (!t) return null;
    const pixelRatio = mouse.pixelRatio || 1;
    const bounds = canvas.getBoundingClientRect();
    const root = document.documentElement || document.body;
    const sx =
      window.pageXOffset !== undefined ? window.pageXOffset : root.scrollLeft;
    const sy =
      window.pageYOffset !== undefined ? window.pageYOffset : root.scrollTop;
    const w =
      canvas.clientWidth / ((canvas.width || canvas.clientWidth) * pixelRatio);
    const h =
      canvas.clientHeight /
      ((canvas.height || canvas.clientHeight) * pixelRatio);
    return {
      x: (t.pageX - bounds.left - sx) / w,
      y: (t.pageY - bounds.top - sy) / h,
    };
  }

  function heroTouchesMovingBody(engine, evt, canvas, mouse) {
    const p = heroTouchPoint(evt, canvas, mouse);
    if (!p) return false;
    const movers = Composite.allBodies(engine.world).filter(
      (b) => !b.isStatic
    );
    return Query.point(movers, p).length > 0;
  }

  function heroScrollMainByDy(dy) {
    /* Siempre mover .events-story; el scroll del documento en móvil queda inconsistente con preventDefault. */
    if (storyScrollEl) {
      storyScrollEl.scrollTop += dy;
      return;
    }
    window.scrollBy(0, dy);
  }

  /** Solo hero: permite scroll táctil y arrastre de bolas sin listeners nativos conflictivos */
  function bindHeroHybridTouch(canvas, mouse, engine) {
    const thresholdPx = 12;
    let startX = 0;
    let startY = 0;
    let lastScrollY = 0;
    let mode = null;
    let matterDown = false;

    canvas.addEventListener(
      "touchstart",
      (e) => {
        if (e.touches.length !== 1) return;
        const t = e.touches[0];
        startX = t.clientX;
        startY = t.clientY;
        lastScrollY = t.clientY;
        mode = null;
        matterDown = false;

        const onBall = heroTouchesMovingBody(engine, e, canvas, mouse);
        if (onBall) {
          mode = "drag";
          mouse.mousedown(e);
          matterDown = true;
        }
      },
      { passive: false }
    );

    canvas.addEventListener(
      "touchmove",
      (e) => {
        if (e.touches.length !== 1) {
          if (matterDown) mouse.mouseup(e);
          matterDown = false;
          mode = null;
          return;
        }

        const t = e.touches[0];
        const dx = t.clientX - startX;
        const dy = t.clientY - startY;
        const dist = Math.sqrt(dx * dx + dy * dy);

        if (mode === null && dist >= thresholdPx) {
          mode =
            Math.abs(dy) > Math.abs(dx) + 5 ? "scroll" : "drag";
          if (mode === "drag" && !matterDown) {
            mouse.mousedown(e);
            matterDown = true;
          }
        }

        if (mode === null) return;

        if (mode === "scroll") {
          const delta = lastScrollY - t.clientY;
          lastScrollY = t.clientY;
          heroScrollMainByDy(delta);
          e.preventDefault();
        } else if (mode === "drag") {
          mouse.mousemove(e);
          e.preventDefault();
        }
      },
      { passive: false }
    );

    function endHeroTouchGesture(e) {
      if (e.touches.length > 0) return;
      if (matterDown) mouse.mouseup(e);
      matterDown = false;
      mode = null;
    }

    canvas.addEventListener("touchend", endHeroTouchGesture, {
      passive: true,
    });
    canvas.addEventListener("touchcancel", endHeroTouchGesture, {
      passive: true,
    });
  }

  const sections = Array.from(document.querySelectorAll(SECTION_SELECTOR));
  if (!sections.length) return false;

  const physicsScenes = [];

  const randomBetween = (min, max) => Math.random() * (max - min) + min;

  const fillPalette = ["#ffcd00", "#ffffff", "#ebaa04", "#613f9b"];

  function makeShape(x, y, size) {
    const fillStyle = Common.choose(fillPalette);
    const render = {
      fillStyle,
      strokeStyle: "transparent",
      lineWidth: 0,
      opacity: 0.95,
    };

    return Bodies.circle(x, y, size * 0.5, {
      restitution: 0.9,
      friction: 0.02,
      frictionAir: 0.002,
      density: 0.001,
      render,
    });
  }

  function createBounds(world, width, height) {
    const wallThickness = 60;
    const floorY = height;
    const options = {
      isStatic: true,
      render: { visible: false },
    };

    const bounds = Composite.add(world, [
      Bodies.rectangle(width / 2, -wallThickness / 2, width, wallThickness, options),
      Bodies.rectangle(width / 2, floorY + wallThickness / 2, width, wallThickness, options),
      Bodies.rectangle(-wallThickness / 2, height / 2, wallThickness, height, options),
      Bodies.rectangle(width + wallThickness / 2, height / 2, wallThickness, height, options),
    ]);

    return { floorY, bounds };
  }

  function createScene(section) {
    const width = section.clientWidth || window.innerWidth;
    const height = section.clientHeight || window.innerHeight;

    const canvasHost = document.createElement("div");
    canvasHost.className = "physics-layer";
    section.prepend(canvasHost);

    const engine = Engine.create();
    const { world } = engine;
    const isHero = section.classList.contains("events-hero");
    world.gravity.y = isHero ? 0.68 : 0.82;

    const render = Render.create({
      element: canvasHost,
      engine,
      options: {
        width,
        height,
        wireframes: false,
        background: "transparent",
        pixelRatio: 1,
      },
    });

    const runner = Runner.create();
    Render.run(render);
    Runner.run(runner, engine);

    const mouse = Mouse.create(render.canvas);
    /* Hero: resorte más blando = arrastre más holgado; eventos: más firme para clicks precisos junto a la UI */
    const mouseConstraint = MouseConstraint.create(engine, {
      mouse,
      constraint: {
        stiffness: isHero ? 0.088 : 0.22,
        damping: isHero ? 0.048 : 0.08,
        render: { visible: false },
      },
    });
    if (isHero) {
      Composite.add(world, mouseConstraint);
      render.mouse = mouse;
    } else {
      render.mouse = null;
    }

    /* Matter registra wheel/touch con preventDefault: rompe scroll en .events-story y en móvil. */
    const c = render.canvas;
    c.removeEventListener("wheel", mouse.mousewheel);
    c.removeEventListener("touchstart", mouse.mousedown);
    c.removeEventListener("touchmove", mouse.mousemove);
    c.removeEventListener("touchend", mouse.mouseup);

    if (isHero) {
      bindHeroHybridTouch(c, mouse, engine);
    }

    const boundsMeta = createBounds(world, width, height);

    const count = isHero
      ? Math.max(34, Math.round(width / 52))
      : Math.max(20, Math.round(width / 70));
    const bodies = [];
    for (let i = 0; i < count; i += 1) {
      const size = randomBetween(20, 78);
      const body = makeShape(
        randomBetween(40, Math.max(40, width - 40)),
        randomBetween(-120, 20),
        size
      );
      bodies.push(body);
    }
    Composite.add(world, bodies);

    return { section, engine, render, runner, bodies, boundsMeta };
  }

  function applyScrollImpulse(deltaY) {
    const direction = deltaY > 0 ? -1 : 1;
    const forceMagnitude = 0.0022;

    physicsScenes.forEach((scene) => {
      scene.bodies.forEach((body) => {
        Body.applyForce(body, body.position, {
          x: randomBetween(-0.0008, 0.0008),
          y: forceMagnitude * direction,
        });
      });
    });
  }

  /** Rebote marcado cuando entras a otra slide (snap / scroll). */
  function applySectionEnterBounce(scene) {
    if (!scene || !scene.bodies) return;
    scene.bodies.forEach((body) => {
      if (body.isStatic) return;
      Body.applyForce(body, body.position, {
        x: randomBetween(-0.0065, 0.0065),
        y: randomBetween(-0.0075, 0.009),
      });
      Body.setAngularVelocity(body, randomBetween(-0.22, 0.22));
    });
  }

  /** Impulso por desplazamiento (rueda → scroll → delta); funciona también sin wheel (táctil). */
  let lastScrollImpulseAt = 0;
  let lastStoryScrollTop = null;
  function applyScrollImpulseFromDelta(deltaY) {
    const now = performance.now();
    if (now - lastScrollImpulseAt < 55) return;
    lastScrollImpulseAt = now;
    applyScrollImpulse(deltaY || 1);
  }

  function resizeScene(scene) {
    const width = scene.section.clientWidth || window.innerWidth;
    const height = scene.section.clientHeight || window.innerHeight;
    scene.render.canvas.width = width;
    scene.render.canvas.height = height;
    scene.render.options.width = width;
    scene.render.options.height = height;
    scene.render.bounds.max.x = width;
    scene.render.bounds.max.y = height;

    if (scene.boundsMeta?.bounds) {
      Composite.remove(scene.engine.world, scene.boundsMeta.bounds, true);
      scene.boundsMeta = createBounds(scene.engine.world, width, height);
    }
  }

  sections.forEach((section) => {
    const scene = createScene(section);
    physicsScenes.push(scene);
  });

  const sceneBySection = new Map();
  physicsScenes.forEach((sc) => {
    sceneBySection.set(sc.section, sc);
  });

  const story = document.querySelector(".events-story");
  let lastDominantSection = null;
  let dominantTransitionsEnabled = false;

  function pickDominantSectionByOverlap() {
    if (!story) return null;
    const sr = story.getBoundingClientRect();
    const midY = sr.top + sr.height * 0.5;
    let bestSec = null;
    let bestScore = -1;
    sections.forEach((sec) => {
      const r = sec.getBoundingClientRect();
      const overlapW = Math.max(
        0,
        Math.min(r.right, sr.right) - Math.max(r.left, sr.left)
      );
      const overlapH = Math.max(
        0,
        Math.min(r.bottom, sr.bottom) - Math.max(r.top, sr.top)
      );
      const area = overlapW * overlapH;
      const crossesMiddle = midY >= r.top && midY <= r.bottom;
      const score = area + (crossesMiddle ? sr.width * sr.height * 0.05 : 0);
      if (score > bestScore) {
        bestScore = score;
        bestSec = sec;
      }
    });
    return bestScore > 32 ? bestSec : null;
  }

  function updateDominantAndMaybeBounce() {
    if (!story || !dominantTransitionsEnabled) return;
    const dom = pickDominantSectionByOverlap();
    if (!dom || dom === lastDominantSection) return;
    const prev = lastDominantSection;
    lastDominantSection = dom;
    if (prev == null) return;
    const sc = sceneBySection.get(dom);
    if (sc) applySectionEnterBounce(sc);
  }

  if (story) {
    story.style.overflowY = "auto";
    lastStoryScrollTop = story.scrollTop;

    story.addEventListener(
      "scroll",
      () => {
        requestAnimationFrame(() => {
          if (!story || lastStoryScrollTop == null) return;
          const st = story.scrollTop;
          const delta = st - lastStoryScrollTop;
          lastStoryScrollTop = st;
          if (Math.abs(delta) >= 2) applyScrollImpulseFromDelta(delta);
          updateDominantAndMaybeBounce();
        });
      },
      { passive: true }
    );

    setTimeout(() => {
      dominantTransitionsEnabled = true;
      lastDominantSection = pickDominantSectionByOverlap();
    }, 280);
  }

  window.addEventListener("resize", () => {
    physicsScenes.forEach(resizeScene);
  });

  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      physicsScenes.forEach((scene) => Runner.stop(scene.runner));
    } else {
      physicsScenes.forEach((scene) => Runner.run(scene.runner, scene.engine));
    }
  });
  return true;
  }

  function bootPhysicsWithRetry(attempt = 0) {
    const started = initPhysics();
    if (started) return;
    if (attempt >= 25) return;
    setTimeout(() => bootPhysicsWithRetry(attempt + 1), 140);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
      setupMenuToggle();
      ensureMatterLoaded().then(() => {
        requestAnimationFrame(() => bootPhysicsWithRetry(0));
      });
    });
  } else {
    setupMenuToggle();
    ensureMatterLoaded().then(() => {
      requestAnimationFrame(() => bootPhysicsWithRetry(0));
    });
  }
})();
