# Changelog

All notable changes to **Astro Animations** will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.0.0] - 2026-10-28

### Added
- **Global Open-Source Release**: Complete English localization and WordPress internationalization (i18n) support with `.pot` translation catalog.
- **Centralized Animation Hub**: Manage unlimited HTML, SVG, Canvas, and CSS animations from a single dashboard.
- **Auto Shortcode Engine**: Automatic unique slug generation with one-click clipboard copy (`[astro_anim slug="..."]`).
- **Layout & CLS Protection**: Native CSS isolation (`isolation: isolate; contain: layout;`) preventing any theme or navigation jumps.
- **Smart Lazy Loading**: Optional viewport IntersectionObserver loading (`data-lazy="1"`) for high Google PageSpeed / Core Web Vitals scores.
- **Live Sandbox Preview**: Real-time iframe sandbox testing environment directly inside the editor.
- **5 Production-Ready Web Presets**:
  1. `particle-network` — Interactive HTML5 Canvas neural particle field with magnetic mouse repulsion.
  2. `glass-stats` — 3D Glassmorphism KPI metric cards with mouse tilt and animated digit rolling counters.
  3. `fluid-waves` — Kinetic multi-wave trigonometric canvas with iridescent gradient fills.
  4. `morphing-blob` — Organic SVG morphing geometry with continuous gradient rotation and hero CTA.
  5. `interactive-cards` — Modern architecture showcase cards with frosted glass detail modal explorer.
- **Developer Hooks**: `astro_anim_output` filter and native browser event (`astro:anim-inview`).
