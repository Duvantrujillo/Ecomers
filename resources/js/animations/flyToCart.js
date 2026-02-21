// resources/js/animations/flyToCart.js

function getCenter(rect) {
  return { x: rect.left + rect.width / 2, y: rect.top + rect.height / 2 };
}

function easeInOutCubic(t) {
  return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
}

// bezier cuadrática
function bezier2(p0, p1, p2, t) {
  const u = 1 - t;
  return {
    x: u * u * p0.x + 2 * u * t * p1.x + t * t * p2.x,
    y: u * u * p0.y + 2 * u * t * p1.y + t * t * p2.y,
  };
}

function rand(min, max) {
  return Math.random() * (max - min) + min;
}

// ✅ Clamp para que el destino no se vaya fuera del viewport (si el header está arriba escondido)
function clamp(n, min, max) {
  return Math.max(min, Math.min(max, n));
}

export function flyToCart(fromEl, toEl, options = {}) {
  if (!fromEl || !toEl) return;

  const opts = {
    duration: 1250,
    arcHeight: 220,
    zIndex: 200,
    popClass: "cart-pop",
    orbCount: 6,
    spread: 22,
    baseSize: 10,
    sizeJitter: 6,
    glowStrength: 22,
    trailBlur: 10,
    trailSize: 26,
   colorMain: "rgba(22,163,74,1)",       // verde principal
colorGlow: "rgba(22,163,74,0.40)",    // glow suave
colorTrail: "rgba(22,163,74,0.22)",   // trail más transparente
    swirlAmp: 18,
    swirlFreq: 10,
    stagger: 55,
    hitFlash: true,

    // ✅ nuevo: margen para no irse “arriba”
    viewportPadding: 10,

    ...options,
  };

  const fromRect = fromEl.getBoundingClientRect();
  const start = getCenter(fromRect);

  // ✅ control fijo (arco) usando el destino inicial solo para “forma”
  const initialToRect = toEl.getBoundingClientRect();
  const initialEnd = getCenter(initialToRect);

 // Distancia real entre inicio y fin
const dx = initialEnd.x - start.x;
const dy = initialEnd.y - start.y;
const distance = Math.hypot(dx, dy);

// Altura dinámica basada en distancia (máximo 25% del alto de pantalla)
const dynamicArc = Math.min(
  distance * 0.35,
  window.innerHeight * 0.25
);

// Punto de control responsive
const ctrl = {
  x: start.x + dx / 2,
  y: Math.min(start.y, initialEnd.y) - dynamicArc,
};

// Clamp para que nunca se salga arriba del viewport
ctrl.y = clamp(
  ctrl.y,
  opts.viewportPadding,
  window.innerHeight - opts.viewportPadding
);

  const particles = [];

  for (let i = 0; i < opts.orbCount; i++) {
    const wrap = document.createElement("div");
    wrap.className = "fixed pointer-events-none";
    wrap.style.zIndex = String(opts.zIndex);
    wrap.style.left = `${start.x}px`;
    wrap.style.top = `${start.y}px`;
    wrap.style.transform = "translate(-50%, -50%)";
    wrap.style.willChange = "transform, left, top, opacity";

    const trail = document.createElement("div");
    trail.style.position = "absolute";
    trail.style.left = "0";
    trail.style.top = "0";
    trail.style.width = `${opts.trailSize}px`;
    trail.style.height = `${opts.trailSize}px`;
    trail.style.borderRadius = "9999px";
    trail.style.background = `radial-gradient(circle at 30% 30%, ${opts.colorTrail}, transparent 70%)`;
    trail.style.filter = `blur(${opts.trailBlur}px)`;
    trail.style.transform = "translate(-50%, -50%)";
    trail.style.opacity = "0.95";
    trail.style.willChange = "transform, opacity, filter";

    const size = Math.round(opts.baseSize + rand(0, opts.sizeJitter));
    const orb = document.createElement("div");
    orb.style.position = "absolute";
    orb.style.left = "0";
    orb.style.top = "0";
    orb.style.width = `${size}px`;
    orb.style.height = `${size}px`;
    orb.style.borderRadius = "9999px";
    orb.style.background = `radial-gradient(circle at 30% 30%, rgba(255,255,255,0.85), ${opts.colorMain} 55%)`;
    orb.style.boxShadow = `0 0 ${opts.glowStrength}px ${opts.colorGlow}`;
    orb.style.transform = "translate(-50%, -50%) scale(1)";
    orb.style.willChange = "transform, opacity";

    wrap.appendChild(trail);
    wrap.appendChild(orb);
    document.body.appendChild(wrap);

    const ox = rand(-opts.spread, opts.spread);
    const oy = rand(-opts.spread, opts.spread);

    particles.push({
      wrap,
      orb,
      trail,
      offset: { x: ox, y: oy },
      phase: rand(0, Math.PI * 2),
      delay: i * opts.stagger,
    });
  }

  const startTime = performance.now();

  function frame(now) {
    let alive = false;

    // ✅ destino dinámico: siempre persigue el carrito REAL
    const toRectNow = toEl.getBoundingClientRect();
    let end = getCenter(toRectNow);

    // ✅ si el header está oculto arriba, el end.y puede quedar negativo:
    // lo “clamp” al viewport para que nunca se vaya por encima de la pantalla.
    end = {
      x: clamp(end.x, opts.viewportPadding, window.innerWidth - opts.viewportPadding),
      y: clamp(end.y, opts.viewportPadding, window.innerHeight - opts.viewportPadding),
    };

    for (const p of particles) {
      const localNow = now - startTime - p.delay;
      if (localNow < 0) {
        alive = true;
        continue;
      }

      const t = Math.min(localNow / opts.duration, 1);
      const e = easeInOutCubic(t);

      const pos = bezier2(
        { x: start.x + p.offset.x, y: start.y + p.offset.y },
        ctrl,
        end,
        e
      );

      const swirl = Math.sin(e * opts.swirlFreq + p.phase) * opts.swirlAmp * (1 - e);
      const swirlY =
        Math.cos(e * (opts.swirlFreq * 0.75) + p.phase) * (opts.swirlAmp * 0.35) * (1 - e);

      const x = pos.x + swirl;
      const y = pos.y + swirlY;

      p.wrap.style.left = `${x}px`;
      p.wrap.style.top = `${y}px`;

      const dx = end.x - x;
      const dy = end.y - y;
      const angle = Math.atan2(dy, dx);

      const behind = 18 + (1 - e) * 12;
      const tx = Math.cos(angle) * behind;
      const ty = Math.sin(angle) * behind;

      const stretchX = 1 + (1 - e) * 0.55;
      const stretchY = 1 - (1 - e) * 0.25;

      const blur = opts.trailBlur + (1 - e) * 6;
      p.trail.style.filter = `blur(${blur}px)`;
      p.trail.style.transform = `translate(calc(-50% + ${tx}px), calc(-50% + ${ty}px)) scaleX(${stretchX}) scaleY(${stretchY})`;

      const pulse = 1 + Math.sin(e * Math.PI) * 0.14;
      p.orb.style.transform = `translate(-50%, -50%) scale(${pulse})`;

      const alpha = 1 - e * 0.88;
      p.wrap.style.opacity = String(alpha);

      if (t < 1) alive = true;
      else {
        p.wrap.remove();
        window.dispatchEvent(new CustomEvent("cart:orb-hit"));
      }
    }

    if (alive) {
      requestAnimationFrame(frame);
    } else {
      // ✅ al final, toma de nuevo el end real (sin clamp) para el flash
      const toRectFinal = toEl.getBoundingClientRect();
      const endFinal = getCenter(toRectFinal);

      if (opts.hitFlash) {
        const flash = document.createElement("div");
        flash.className = "fixed pointer-events-none";
        flash.style.zIndex = String(opts.zIndex + 1);
        flash.style.left = `${endFinal.x}px`;
        flash.style.top = `${endFinal.y}px`;
        flash.style.width = "22px";
        flash.style.height = "22px";
        flash.style.borderRadius = "9999px";
        flash.style.transform = "translate(-50%, -50%)";
        flash.style.boxShadow = `0 0 26px ${opts.colorGlow}`;
        flash.style.background = `radial-gradient(circle, rgba(255,255,255,0.9), ${opts.colorTrail} 60%, transparent 78%)`;
        flash.style.opacity = "0.95";
        flash.style.filter = "blur(1px)";
        document.body.appendChild(flash);
        setTimeout(() => flash.remove(), 160);
      }

      toEl.classList.add(opts.popClass);
      setTimeout(() => toEl.classList.remove(opts.popClass), 180);
      window.dispatchEvent(new CustomEvent("cart:arrived"));
    }
  }

  requestAnimationFrame(frame);
}