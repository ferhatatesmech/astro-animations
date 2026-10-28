<?php
/**
 * Built-in Preset Library for Astro Animations
 *
 * @package AstroAnimations
 */

if (!defined('ABSPATH')) {
    exit;
}

class Astro_Anim_Presets {

    /**
     * Return all built-in animation presets
     *
     * @return array
     */
    public static function get_presets() {
        return [
            [
                'slug'             => 'particle-network',
                'title'            => 'Interactive Particle Network',
                'description'      => 'Interactive HTML5 Canvas particle constellation that forms dynamic network nodes and responds to mouse hover and touch interactions.',
                'container_width'  => '100%',
                'container_height' => '440px',
                'align'            => 'center',
                'background'       => 'transparent',
                'lazy_load'        => 1,
                'isolate_mode'     => 1,
                'custom_class'     => 'astro-anim-particles',
                'status'           => 1,
                'code'             => '<!-- ASTRO ANIM: Interactive Particle Network -->
<div class="anim-particle-network" id="animParticleNetwork">
  <style>
    .anim-particle-network {
      position: relative;
      width: 100%;
      height: 440px;
      background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #0f172a 100%);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 20px 50px rgba(15, 23, 42, 0.4);
      isolation: isolate;
      contain: layout paint;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      box-sizing: border-box;
    }
    .anim-particle-network * { box-sizing: border-box; }
    .anim-particle-network canvas {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      display: block;
      cursor: crosshair;
    }
    .anim-particle-overlay {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      pointer-events: none;
      text-align: center;
      padding: 24px;
      color: #ffffff;
      z-index: 2;
    }
    .anim-particle-pill {
      display: inline-block;
      padding: 6px 14px;
      border-radius: 999px;
      background: rgba(99, 102, 241, 0.25);
      border: 1px solid rgba(165, 180, 252, 0.4);
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: #c7d2fe;
      margin-bottom: 12px;
      -webkit-backdrop-filter: blur(8px);
      backdrop-filter: blur(8px);
    }
    .anim-particle-title {
      margin: 0;
      font-size: clamp(24px, 4.5vw, 40px);
      font-weight: 700;
      letter-spacing: -0.02em;
      line-height: 1.15;
      background: linear-gradient(135deg, #ffffff 40%, #a5b4fc 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .anim-particle-subtitle {
      margin: 10px 0 0;
      font-size: clamp(13px, 2vw, 15px);
      color: #94a3b8;
      max-width: 520px;
      line-height: 1.6;
    }
    @media (max-width: 640px) {
      .anim-particle-network { height: 360px; }
    }
  </style>

  <canvas></canvas>

  <div class="anim-particle-overlay">
    <span class="anim-particle-pill">Interactive Physics</span>
    <h2 class="anim-particle-title">Neural Particle Field</h2>
    <p class="anim-particle-subtitle">Move cursor to interact with floating nodes, magnetic repulsion forces, and dynamic distance-based constellations.</p>
  </div>

  <script>
  (function(){
    var root = document.getElementById(\'animParticleNetwork\');
    if (!root) return;
    var cv = root.querySelector(\'canvas\');
    if (!cv) return;
    var ctx = cv.getContext(\'2d\');
    var W = 0, H = 0, dpr = 1;
    var particles = [];
    var mouse = { x: null, y: null, radius: 140 };

    function resize() {
      dpr = window.devicePixelRatio || 1;
      W = root.clientWidth;
      H = root.clientHeight;
      cv.width = W * dpr;
      cv.height = H * dpr;
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      initParticles();
    }

    function initParticles() {
      particles = [];
      var count = Math.floor((W * H) / 9500);
      count = Math.min(Math.max(count, 35), 110);
      for (var i = 0; i < count; i++) {
        particles.push({
          x: Math.random() * W,
          y: Math.random() * H,
          vx: (Math.random() - 0.5) * 0.9,
          vy: (Math.random() - 0.5) * 0.9,
          r: Math.random() * 2 + 1.5
        });
      }
    }

    root.addEventListener(\'pointermove\', function(e) {
      var r = root.getBoundingClientRect();
      mouse.x = e.clientX - r.left;
      mouse.y = e.clientY - r.top;
    });

    root.addEventListener(\'pointerleave\', function() {
      mouse.x = null;
      mouse.y = null;
    });

    var animId = null;
    function loop() {
      ctx.clearRect(0, 0, W, H);

      for (var i = 0; i < particles.length; i++) {
        var p = particles[i];
        p.x += p.vx;
        p.y += p.vy;

        if (p.x < 0 || p.x > W) p.vx *= -1;
        if (p.y < 0 || p.y > H) p.vy *= -1;

        if (mouse.x !== null) {
          var dx = mouse.x - p.x;
          var dy = mouse.y - p.y;
          var dist = Math.hypot(dx, dy);
          if (dist < mouse.radius) {
            var force = (mouse.radius - dist) / mouse.radius;
            p.x -= (dx / dist) * force * 3.5;
            p.y -= (dy / dist) * force * 3.5;
          }
        }

        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = \'#818cf8\';
        ctx.fill();

        for (var j = i + 1; j < particles.length; j++) {
          var p2 = particles[j];
          var d = Math.hypot(p.x - p2.x, p.y - p2.y);
          if (d < 125) {
            ctx.beginPath();
            ctx.moveTo(p.x, p.y);
            ctx.lineTo(p2.x, p2.y);
            ctx.strokeStyle = \'rgba(129, 140, 248, \' + (1 - d / 125) * 0.35 + \')\';
            ctx.lineWidth = 0.9;
            ctx.stroke();
          }
        }
      }
      animId = requestAnimationFrame(loop);
    }

    if (window.ResizeObserver) {
      new ResizeObserver(resize).observe(root);
    } else {
      window.addEventListener(\'resize\', resize);
    }
    resize();
    loop();
  })();
  </script>
</div>',
            ],
            [
                'slug'             => 'glass-stats',
                'title'            => '3D Glassmorphic KPI & Metrics Cards',
                'description'      => 'Modern glassmorphism metric cards featuring smooth 3D parallax tilt, dynamic digit rolling counters, and gradient accent lines.',
                'container_width'  => '1100px',
                'container_height' => 'auto',
                'align'            => 'center',
                'background'       => 'transparent',
                'lazy_load'        => 0,
                'isolate_mode'     => 1,
                'custom_class'     => 'astro-anim-stats',
                'status'           => 1,
                'code'             => '<!-- ASTRO ANIM: 3D Glassmorphic KPI & Metrics Cards -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
<div class="anim-glass-stats" id="animGlassStats">
  <style>
    .anim-glass-stats {
      --primary: #6366f1;
      --accent: #06b6d4;
      --ink: #0f172a;
      --muted: #64748b;
      position: relative;
      width: 100%;
      max-width: 1100px;
      margin: 0 auto;
      padding: 30px 16px;
      box-sizing: border-box;
      font-family: \'Plus Jakarta Sans\', system-ui, sans-serif;
      isolation: isolate;
      contain: layout;
    }
    .anim-glass-stats * { box-sizing: border-box; }
    .anim-glass-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
    }
    .anim-glass-card {
      position: relative;
      background: rgba(255, 255, 255, 0.7);
      -webkit-backdrop-filter: blur(20px) saturate(180%);
      backdrop-filter: blur(20px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.9);
      border-radius: 24px;
      padding: 28px 24px;
      box-shadow: 0 16px 40px rgba(99, 102, 241, 0.08), inset 0 1px 0 #ffffff;
      transform: perspective(800px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg)) translateY(var(--ty, 0px));
      transition: transform 0.25s ease-out, box-shadow 0.25s ease-out;
      overflow: hidden;
      cursor: default;
    }
    .anim-glass-card:hover {
      --ty: -6px;
      box-shadow: 0 24px 50px rgba(99, 102, 241, 0.16), inset 0 1px 0 #ffffff;
      border-color: rgba(99, 102, 241, 0.3);
    }
    .anim-glass-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--c1, #6366f1), var(--c2, #06b6d4));
      border-radius: 24px 24px 0 0;
    }
    .anim-glass-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: grid;
      place-items: center;
      background: rgba(99, 102, 241, 0.1);
      color: var(--c1, #6366f1);
      margin-bottom: 20px;
    }
    .anim-glass-icon svg { width: 24px; height: 24px; }
    .anim-glass-val {
      font-size: clamp(32px, 3.5vw, 44px);
      font-weight: 700;
      color: var(--ink);
      line-height: 1;
      letter-spacing: -0.03em;
      margin-bottom: 8px;
      display: flex;
      align-items: baseline;
    }
    .anim-glass-val .suffix {
      font-size: 0.65em;
      color: var(--c1, #6366f1);
      margin-left: 2px;
    }
    .anim-glass-label {
      font-size: 14px;
      font-weight: 600;
      color: var(--muted);
      margin-bottom: 12px;
    }
    .anim-glass-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      font-weight: 600;
      color: #059669;
      background: #ecfdf5;
      padding: 3px 9px;
      border-radius: 999px;
    }
    @media (max-width: 640px) {
      .anim-glass-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
      .anim-glass-card { padding: 20px 16px; }
    }
  </style>

  <div class="anim-glass-grid">
    <div class="anim-glass-card" style="--c1:#6366f1; --c2:#8b5cf6;">
      <div class="anim-glass-icon">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      </div>
      <div class="anim-glass-val"><span class="counter" data-target="99.9">0</span><span class="suffix">%</span></div>
      <div class="anim-glass-label">Service Uptime SLA</div>
      <span class="anim-glass-badge">✓ Always Online</span>
    </div>

    <div class="anim-glass-card" style="--c1:#06b6d4; --c2:#3b82f6;">
      <div class="anim-glass-icon">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
      </div>
      <div class="anim-glass-val"><span class="counter" data-target="250">0</span><span class="suffix">k+</span></div>
      <div class="anim-glass-label">Active Users</div>
      <span class="anim-glass-badge">↑ 34% this month</span>
    </div>

    <div class="anim-glass-card" style="--c1:#10b981; --c2:#14b8a6;">
      <div class="anim-glass-icon">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div class="anim-glass-val"><span class="counter" data-target="15">0</span><span class="suffix">ms</span></div>
      <div class="anim-glass-label">Global Latency</div>
      <span class="anim-glass-badge">⚡ Lightning Fast</span>
    </div>

    <div class="anim-glass-card" style="--c1:#f59e0b; --c2:#f97316;">
      <div class="anim-glass-icon">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
      </div>
      <div class="anim-glass-val"><span class="counter" data-target="4.9">0</span><span class="suffix">★</span></div>
      <div class="anim-glass-label">Customer Rating</div>
      <span class="anim-glass-badge">★ 2,400+ Reviews</span>
    </div>
  </div>

  <script>
  (function(){
    var root = document.getElementById(\'animGlassStats\');
    if (!root) return;
    var cards = root.querySelectorAll(\'.anim-glass-card\');

    // 3D Parallax Tilt
    cards.forEach(function(card){
      card.addEventListener(\'pointermove\', function(e){
        var r = card.getBoundingClientRect();
        var nx = (e.clientX - r.left) / r.width - 0.5;
        var ny = (e.clientY - r.top) / r.height - 0.5;
        card.style.setProperty(\'--ry\', (nx * 14).toFixed(2) + \'deg\');
        card.style.setProperty(\'--rx\', (-ny * 14).toFixed(2) + \'deg\');
      });
      card.addEventListener(\'pointerleave\', function(){
        card.style.setProperty(\'--rx\', \'0deg\');
        card.style.setProperty(\'--ry\', \'0deg\');
      });
    });

    // Rolling Counters
    var hasRun = false;
    function animateCounters() {
      if (hasRun) return;
      hasRun = true;
      var counters = root.querySelectorAll(\'.counter\');
      counters.forEach(function(el){
        var target = parseFloat(el.getAttribute(\'data-target\'));
        var isFloat = target % 1 !== 0;
        var dur = 1800;
        var start = performance.now();
        function update(t) {
          var progress = Math.min((t - start) / dur, 1);
          var ease = 1 - Math.pow(1 - progress, 3);
          var val = target * ease;
          el.textContent = isFloat ? val.toFixed(1) : Math.round(val);
          if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
      });
    }

    if (\'IntersectionObserver\' in window) {
      new IntersectionObserver(function(entries, observer){
        if (entries[0].isIntersecting) {
          animateCounters();
          observer.disconnect();
        }
      }, { threshold: 0.2 }).observe(root);
    } else {
      setTimeout(animateCounters, 500);
    }
  })();
  </script>
</div>',
            ],
            [
                'slug'             => 'fluid-waves',
                'title'            => 'Kinetic Sine Wave Canvas',
                'description'      => 'Smooth multi-layered sinusoidal wave canvas with iridescent gradient fills, responsive amplitude, and organic mathematical flow.',
                'container_width'  => '100%',
                'container_height' => '360px',
                'align'            => 'center',
                'background'       => 'transparent',
                'lazy_load'        => 1,
                'isolate_mode'     => 1,
                'custom_class'     => 'astro-anim-waves',
                'status'           => 1,
                'code'             => '<!-- ASTRO ANIM: Kinetic Sine Wave Canvas -->
<div class="anim-wave-container" id="animSineWave">
  <style>
    .anim-wave-container {
      position: relative;
      width: 100%;
      height: 360px;
      background: linear-gradient(135deg, #090d16 0%, #111827 100%);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
      isolation: isolate;
      contain: layout paint;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      box-sizing: border-box;
    }
    .anim-wave-container * { box-sizing: border-box; }
    .anim-wave-container canvas {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      display: block;
    }
    .anim-wave-content {
      position: absolute;
      top: 30px;
      left: 30px;
      z-index: 2;
      pointer-events: none;
      color: #ffffff;
    }
    .anim-wave-tag {
      font-size: 11px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      font-weight: 700;
      color: #38bdf8;
      background: rgba(56, 189, 248, 0.15);
      padding: 4px 10px;
      border-radius: 6px;
      display: inline-block;
      margin-bottom: 8px;
    }
    .anim-wave-heading {
      margin: 0;
      font-size: clamp(20px, 3.2vw, 28px);
      font-weight: 700;
      letter-spacing: -0.02em;
      color: #f8fafc;
    }
    .anim-wave-desc {
      margin: 6px 0 0;
      font-size: 13px;
      color: #94a3b8;
    }
  </style>

  <div class="anim-wave-content">
    <span class="anim-wave-tag">Generative Dynamics</span>
    <h3 class="anim-wave-heading">Harmonic Multi-Wave Synthesis</h3>
    <p class="anim-wave-desc">Real-time trigonometric wave superposition with iridescent gradient fills.</p>
  </div>

  <canvas></canvas>

  <script>
  (function(){
    var root = document.getElementById(\'animSineWave\');
    if (!root) return;
    var cv = root.querySelector(\'canvas\');
    if (!cv) return;
    var ctx = cv.getContext(\'2d\');
    var W = 0, H = 0, dpr = 1;
    var step = 0;

    function resize() {
      dpr = window.devicePixelRatio || 1;
      W = root.clientWidth;
      H = root.clientHeight;
      cv.width = W * dpr;
      cv.height = H * dpr;
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    var waves = [
      { color: \'rgba(56, 189, 248, 0.4)\', amp: 45, freq: 0.007, speed: 0.03, phase: 0 },
      { color: \'rgba(99, 102, 241, 0.45)\', amp: 55, freq: 0.005, speed: 0.02, phase: 2 },
      { color: \'rgba(236, 72, 153, 0.35)\', amp: 35, freq: 0.009, speed: 0.025, phase: 4 }
    ];

    function drawWave(w) {
      ctx.beginPath();
      ctx.moveTo(0, H);
      var midY = H * 0.65;

      for (var x = 0; x <= W; x += 4) {
        var y = midY + Math.sin(x * w.freq + step * w.speed + w.phase) * w.amp;
        ctx.lineTo(x, y);
      }
      ctx.lineTo(W, H);
      ctx.closePath();
      ctx.fillStyle = w.color;
      ctx.fill();
    }

    var animId = null;
    function render() {
      ctx.clearRect(0, 0, W, H);
      step += 1;
      for (var i = 0; i < waves.length; i++) {
        drawWave(waves[i]);
      }
      animId = requestAnimationFrame(render);
    }

    if (window.ResizeObserver) {
      new ResizeObserver(resize).observe(root);
    } else {
      window.addEventListener(\'resize\', resize);
    }
    resize();
    render();
  })();
  </script>
</div>',
            ],
            [
                'slug'             => 'morphing-blob',
                'title'            => 'Organic Morphing Gradient Blob & Hero Banner',
                'description'      => 'Fluid organic SVG blob animation with rotating gradient shifts, floating kinetic glass badges, and call-to-action button.',
                'container_width'  => '720px',
                'container_height' => 'auto',
                'align'            => 'center',
                'background'       => 'transparent',
                'lazy_load'        => 0,
                'isolate_mode'     => 1,
                'custom_class'     => 'astro-anim-hero-blob',
                'status'           => 1,
                'code'             => '<!-- ASTRO ANIM: Morphing SVG Blob & Hero Banner -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
<div class="anim-hero-blob" id="animHeroBlob">
  <style>
    .anim-hero-blob {
      --primary: #6366f1;
      --secondary: #ec4899;
      --cyan: #06b6d4;
      position: relative;
      width: 100%;
      max-width: 720px;
      margin: 0 auto;
      padding: 50px 36px;
      border-radius: 32px;
      background: rgba(255, 255, 255, 0.65);
      -webkit-backdrop-filter: blur(25px) saturate(180%);
      backdrop-filter: blur(25px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.95);
      box-shadow: 0 25px 60px rgba(99, 102, 241, 0.12), inset 0 1px 0 #ffffff;
      overflow: hidden;
      isolation: isolate;
      contain: layout;
      font-family: \'Plus Jakarta Sans\', system-ui, sans-serif;
      text-align: center;
      box-sizing: border-box;
    }
    .anim-hero-blob * { box-sizing: border-box; }
    
    .anim-blob-svg {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 130%;
      height: 130%;
      transform: translate(-50%, -50%);
      z-index: -1;
      pointer-events: none;
      filter: blur(50px);
      opacity: 0.65;
      animation: animBlobSpin 28s linear infinite;
    }
    @keyframes animBlobSpin {
      to { transform: translate(-50%, -50%) rotate(360deg); }
    }

    .anim-blob-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 16px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.85);
      border: 1px solid rgba(99, 102, 241, 0.25);
      box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
      font-size: 13px;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 20px;
    }
    .anim-blob-badge .dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #10b981;
      box-shadow: 0 0 8px #10b981;
    }
    .anim-blob-title {
      margin: 0;
      font-size: clamp(28px, 4.5vw, 44px);
      font-weight: 700;
      letter-spacing: -0.03em;
      line-height: 1.15;
      color: #0f172a;
    }
    .anim-blob-title span {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .anim-blob-lead {
      margin: 16px auto 28px;
      font-size: clamp(14px, 2vw, 17px);
      line-height: 1.6;
      color: #475569;
      max-width: 520px;
    }
    .anim-blob-chips {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 30px;
    }
    .anim-blob-chip {
      padding: 6px 14px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.7);
      border: 1px solid rgba(226, 232, 240, 0.8);
      font-size: 13px;
      font-weight: 600;
      color: #334155;
    }
    .anim-blob-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 14px 34px;
      border-radius: 999px;
      background: linear-gradient(135deg, #6366f1, #8b5cf6);
      color: #ffffff;
      font-size: 15px;
      font-weight: 600;
      text-decoration: none;
      box-shadow: 0 10px 28px rgba(99, 102, 241, 0.35);
      transition: transform 0.25s, box-shadow 0.25s;
      cursor: pointer;
    }
    .anim-blob-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 16px 36px rgba(99, 102, 241, 0.45);
      color: #ffffff;
    }
  </style>

  <!-- Animated Morphing Gradient SVG -->
  <svg class="anim-blob-svg" viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <linearGradient id="blobGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#818cf8"/>
        <stop offset="50%" stop-color="#f472b6"/>
        <stop offset="100%" stop-color="#38bdf8"/>
      </linearGradient>
    </defs>
    <path fill="url(#blobGrad)">
      <animate attributeName="d" dur="14s" repeatCount="indefinite"
        values="
          M420,320Q380,390,310,410Q240,430,170,390Q100,350,110,270Q120,190,180,140Q240,90,320,110Q400,130,430,200Q460,270,420,320Z;
          M440,310Q400,370,330,420Q260,470,180,430Q100,390,110,300Q120,210,170,150Q220,90,310,100Q400,110,440,190Q480,270,440,310Z;
          M410,340Q360,430,280,440Q200,450,140,390Q80,330,100,240Q120,150,190,120Q260,90,340,110Q420,130,440,210Q460,290,410,340Z;
          M420,320Q380,390,310,410Q240,430,170,390Q100,350,110,270Q120,190,180,140Q240,90,320,110Q400,130,430,200Q460,270,420,320Z
        "
      />
    </path>
  </svg>

  <div class="anim-blob-badge"><span class="dot"></span> Next-Gen Web Components</div>
  <h2 class="anim-blob-title">Build High-Impact <span>Interactive Experiences</span></h2>
  <p class="anim-blob-lead">Supercharge your WordPress pages with fluid physics simulations, responsive glassmorphism, and hardware-accelerated animations.</p>

  <div class="anim-blob-chips">
    <span class="anim-blob-chip">⚡ 60 FPS Native</span>
    <span class="anim-blob-chip">🛡️ Zero CLS Jumps</span>
    <span class="anim-blob-chip">📱 100% Responsive</span>
    <span class="anim-blob-chip">🌐 SEO Optimized</span>
  </div>

  <a href="#explore" class="anim-blob-btn">
    Explore Showcase →
  </a>
</div>',
            ],
            [
                'slug'             => 'interactive-cards',
                'title'            => 'Engine Architecture Showcase & Modal Explorer',
                'description'      => 'Modern feature showcase cards with frosted glass detail modal, responsive grid, and smooth interactive micro-interactions.',
                'container_width'  => '100%',
                'container_height' => 'auto',
                'align'            => 'center',
                'background'       => 'transparent',
                'lazy_load'        => 0,
                'isolate_mode'     => 1,
                'custom_class'     => 'astro-anim-features',
                'status'           => 1,
                'code'             => '<!-- ASTRO ANIM: Feature Showcase & Modal Explorer -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
<div class="anim-features-wrap" id="animFeaturesWrap">
  <style>
    .anim-features-wrap {
      --primary: #6366f1;
      --ink: #0f172a;
      --muted: #64748b;
      position: relative;
      width: 100%;
      margin: 0 auto;
      padding: 20px 0;
      box-sizing: border-box;
      font-family: \'Plus Jakarta Sans\', system-ui, sans-serif;
      isolation: isolate;
      contain: layout;
    }
    .anim-features-wrap * { box-sizing: border-box; }

    .anim-feat-head {
      text-align: center;
      max-width: 600px;
      margin: 0 auto 30px;
    }
    .anim-feat-title {
      font-size: clamp(24px, 3.8vw, 36px);
      font-weight: 700;
      color: var(--ink);
      margin: 0 0 10px;
      letter-spacing: -0.02em;
    }
    .anim-feat-sub {
      margin: 0;
      color: var(--muted);
      font-size: 15px;
      line-height: 1.6;
    }
    .anim-feat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
    }
    .anim-feat-card {
      background: rgba(255, 255, 255, 0.75);
      -webkit-backdrop-filter: blur(18px);
      backdrop-filter: blur(18px);
      border: 1px solid rgba(226, 232, 240, 0.8);
      border-radius: 20px;
      padding: 26px 22px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
      transition: transform 0.25s, box-shadow 0.25s;
      cursor: pointer;
      display: flex;
      flex-direction: column;
    }
    .anim-feat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 18px 45px rgba(99, 102, 241, 0.12);
      border-color: rgba(99, 102, 241, 0.3);
    }
    .anim-feat-icon {
      width: 50px;
      height: 50px;
      border-radius: 14px;
      background: rgba(99, 102, 241, 0.12);
      color: var(--primary);
      display: grid;
      place-items: center;
      font-size: 24px;
      margin-bottom: 18px;
    }
    .anim-feat-name {
      margin: 0 0 8px;
      font-size: 18px;
      font-weight: 700;
      color: var(--ink);
    }
    .anim-feat-desc {
      margin: 0;
      font-size: 14px;
      color: var(--muted);
      line-height: 1.6;
      flex: 1;
    }
    .anim-feat-link {
      margin-top: 18px;
      color: var(--primary);
      font-size: 13px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    /* Modal Sheet */
    .anim-modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.5);
      -webkit-backdrop-filter: blur(8px);
      backdrop-filter: blur(8px);
      z-index: 999999;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      opacity: 0;
      visibility: hidden;
      transition: all 0.25s ease;
    }
    .anim-modal-backdrop.open {
      opacity: 1;
      visibility: visible;
    }
    .anim-modal-box {
      background: #ffffff;
      border-radius: 24px;
      max-width: 540px;
      width: 100%;
      padding: 32px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
      position: relative;
      transform: translateY(20px);
      transition: transform 0.25s ease;
    }
    .anim-modal-backdrop.open .anim-modal-box {
      transform: translateY(0);
    }
    .anim-modal-close {
      position: absolute;
      top: 18px;
      right: 18px;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: none;
      background: #f1f5f9;
      cursor: pointer;
      display: grid;
      place-items: center;
      font-size: 18px;
      color: #64748b;
    }
    .anim-modal-close:hover { background: #e2e8f0; color: #0f172a; }
  </style>

  <div class="anim-feat-head">
    <h3 class="anim-feat-title">Engine Architecture</h3>
    <p class="anim-feat-sub">Engineered specifically to solve layout shifts and performance bottlenecks in modern web environments.</p>
  </div>

  <div class="anim-feat-grid">
    <div class="anim-feat-card" data-title="Layout Isolation Engine" data-detail="Uses native CSS containment (`contain: layout; isolation: isolate;`) to ensure scripts, canvas transformations, and keyframe animations cannot bleed into or shift outside page headers, sticky menus, or footers.">
      <div class="anim-feat-icon">🛡️</div>
      <h4 class="anim-feat-name">Layout Isolation</h4>
      <p class="anim-feat-desc">Strict container sandboxing completely eliminates Cumulative Layout Shift (CLS).</p>
      <span class="anim-feat-link">Learn details →</span>
    </div>

    <div class="anim-feat-card" data-title="Intersection Observer Engine" data-detail="The built-in lazy loader evaluates viewport proximity. Heavy animation loops and requestAnimationFrame instances are activated only when the user scrolls near, saving GPU cycles and battery on mobile devices.">
      <div class="anim-feat-icon">⚡</div>
      <h4 class="anim-feat-name">Smart Lazy Engine</h4>
      <p class="anim-feat-desc">Initializes scripts only when approaching viewport for maximum PageSpeed scores.</p>
      <span class="anim-feat-link">Learn details →</span>
    </div>

    <div class="anim-feat-card" data-title="Live Sandbox Preview" data-detail="Test interactive JavaScript and Canvas code in real-time inside the WordPress admin editor. The iframe sandbox isolates scripts safely before saving to your live pages.">
      <div class="anim-feat-icon">👁️</div>
      <h4 class="anim-feat-name">Admin Sandbox</h4>
      <p class="anim-feat-desc">Live preview iframe provides real-time verification before publishing.</p>
      <span class="anim-feat-link">Learn details →</span>
    </div>

    <div class="anim-feat-card" data-title="Universal Shortcode API" data-detail="Embed with simple shortcodes like `[astro_anim slug=\'...\']` with optional inline overrides for max-width, height, page alignment, and custom CSS classes across any page builder.">
      <div class="anim-feat-icon">🧩</div>
      <h4 class="anim-feat-name">Universal Shortcode</h4>
      <p class="anim-feat-desc">Compatible with Gutenberg, Elementor, Divi, TagDiv, WPBakery, and raw PHP.</p>
      <span class="anim-feat-link">Learn details →</span>
    </div>
  </div>

  <!-- Detail Modal -->
  <div class="anim-modal-backdrop" id="animFeatureModal">
    <div class="anim-modal-box">
      <button class="anim-modal-close" type="button" aria-label="Close">×</button>
      <h3 id="animModalTitle" style="margin:0 0 12px; font-size:22px; color:#0f172a;"></h3>
      <p id="animModalDetail" style="margin:0; font-size:15px; color:#475569; line-height:1.6;"></p>
    </div>
  </div>

  <script>
  (function(){
    var root = document.getElementById(\'animFeaturesWrap\');
    if (!root) return;
    var modal = root.querySelector(\'#animFeatureModal\');
    var modalTitle = root.querySelector(\'#animModalTitle\');
    var modalDetail = root.querySelector(\'#animModalDetail\');
    var closeBtn = root.querySelector(\'.anim-modal-close\');

    root.querySelectorAll(\'.anim-feat-card\').forEach(function(card){
      card.addEventListener(\'click\', function(){
        modalTitle.textContent = card.getAttribute(\'data-title\');
        modalDetail.textContent = card.getAttribute(\'data-detail\');
        modal.classList.add(\'open\');
      });
    });

    function closeModal() { modal.classList.remove(\'open\'); }
    closeBtn.addEventListener(\'click\', closeModal);
    modal.addEventListener(\'click\', function(e){ if (e.target === modal) closeModal(); });
    document.addEventListener(\'keydown\', function(e){ if (e.key === \'Escape\') closeModal(); });
  })();
  </script>
</div>',
            ],
        ];
    }

    /**
     * Install presets into the database if not already present
     *
     * @return int Number of installed presets
     */
    public static function install_presets() {
        $presets = self::get_presets();
        $count   = 0;

        foreach ($presets as $p) {
            if (!Astro_Anim_DB::slug_exists($p['slug'])) {
                Astro_Anim_DB::insert($p);
                $count++;
            }
        }

        return $count;
    }
}
