<div align="center">

# Astro Animations 🚀
### Interactive Canvas, SVG & Web Component Manager for WordPress

[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue.svg?style=flat-square&logo=wordpress)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4.svg?style=flat-square&logo=php)](https://www.php.net)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-green.svg?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat-square)](CONTRIBUTING.md)

*A high-performance WordPress animation and interactive component manager. Easily embed isolated Canvas simulations, interactive SVG graphics, particle physics networks, and glassmorphic UI components with clean shortcodes.*

</div>

---

## 🚀 Overview

**Astro Animations** solves the common challenges of embedding complex interactive JavaScript, HTML5 Canvas, and SVG components in WordPress. Often, third-party scripts and animation wrappers cause **Cumulative Layout Shift (CLS)**, conflict with theme headers, or degrade Google PageSpeed performance.

With **Astro Animations**, every animation runs within an isolated CSS container (`isolation: isolate; contain: layout;`), features optional **IntersectionObserver-based lazy loading**, and can be embedded anywhere using a lightweight shortcode.

---

## ✨ Features

- 🎯 **Centralized Management**: Create, edit, duplicate, and toggle unlimited animations from a sleek, responsive admin dashboard.
- ⚡ **Zero Layout Shifts (CLS Protected)**: Automated layout containment prevents animations from jumping or interfering with page headers, sticky navigations, and footer elements.
- ⏱️ **Lazy Loading on Scroll**: Scripts and rendering loops initiate only when the visitor scrolls near the component, preserving page speed and Core Web Vitals scores.
- 👁️ **Live Sandbox Preview**: Instant real-time preview iframe directly inside the code editor to test animations before publishing.
- 📋 **One-Click Shortcode Copy**: Click to copy `[astro_anim slug="..."]` directly to your clipboard.
- 🎨 **5 Hand-crafted Presets**: Includes the Neural Particle Network, 3D Glassmorphic KPI Cards, Kinetic Sine Wave Canvas, Morphing Gradient Blob, and Architecture Explorer.
- 🌐 **Global & Translation Ready**: Fully localized in English with standard gettext `.pot` catalog included.
- 🧩 **Page Builder Agnostic**: Works seamlessly with the Block Editor (Gutenberg), Elementor, Divi, TagDiv Composer, WPBakery, Beaver Builder, or raw PHP templates.

---

## 📦 Built-in Preset Library

The plugin comes with 5 production-ready, highly optimized interactive presets:

| Slug | Preset Name | Dimensions | Description |
| :--- | :--- | :--- | :--- |
| `particle-network` | **Interactive Particle Network** | `100% × 440px` | Interactive HTML5 Canvas particle constellation that forms dynamic network nodes and responds to mouse hover and touch interactions. |
| `glass-stats` | **3D Glassmorphic KPI Cards** | `1100px × auto` | Modern glassmorphism metric cards featuring smooth 3D parallax tilt, dynamic digit rolling counters, and gradient accent lines. |
| `fluid-waves` | **Kinetic Sine Wave Canvas** | `100% × 360px` | Smooth multi-layered sinusoidal wave canvas with iridescent gradient fills, responsive amplitude, and organic mathematical flow. |
| `morphing-blob` | **Organic Morphing Gradient Blob** | `720px × auto` | Fluid organic SVG blob animation with rotating gradient shifts, floating kinetic glass badges, and call-to-action button. |
| `interactive-cards` | **Architecture Showcase & Modal** | `100% × auto` | Modern feature showcase cards with frosted glass detail modal, responsive grid, and smooth micro-interactions. |

---

## 🛠️ Installation

### Via WordPress Admin:
1. Download the latest `astro-animations.zip` from [Releases](https://github.com/ferhat/astro-animations/releases).
2. Go to your WordPress Admin > **Plugins** > **Add New** > **Upload Plugin**.
3. Select `astro-animations.zip` and click **Install Now**.
4. Click **Activate Plugin**.
5. Navigate to **Astro Animations** in the sidebar and click **Install 5 Built-in Presets Now**.

### Via Git:
```bash
cd wp-content/plugins/
git clone https://github.com/ferhat/astro-animations.git
```

---

## 📖 Shortcode Usage & Parameters

Insert any animation on your site using:
```text
[astro_anim slug="particle-network"]
```

### Supported Attributes

| Attribute | Default | Description | Example |
| :--- | :--- | :--- | :--- |
| `slug` | `""` | The unique slug identifier of the animation. | `slug="particle-network"` |
| `id` | `0` | The database ID of the animation (alternative to slug). | `id="2"` |
| `width` | *(DB value)* | Overrides the container's maximum width. | `width="100%"` or `width="900px"` |
| `height` | *(DB value)* | Overrides the container's minimum height. | `height="450px"` or `height="auto"` |
| `align` | *(DB value)* | Alignment inside the page (`center`, `left`, `right`). | `align="center"` |
| `bg` | *(DB value)* | Container background color or gradient. | `bg="#0f172a"` |
| `class` | `""` | Additional custom CSS class(es) added to the root container. | `class="my-hero-anim"` |

#### Advanced Example:
```text
[astro_anim slug="particle-network" width="100%" height="450px" align="center" class="custom-hero-particles"]
```

#### PHP Template Integration:
```php
<?php echo do_shortcode('[astro_anim slug="particle-network"]'); ?>
```

---

## 🔌 Developer Hooks & Events

### PHP Filters
Developers can filter the final rendered HTML output before it is printed:

```php
add_filter('astro_anim_output', function($html, $animation, $atts) {
    // Custom modifications
    return $html;
}, 10, 3);
```

### JavaScript Custom Events
The plugin emits native browser events that your custom scripts can listen for:

```javascript
// Triggered when a lazy-loaded animation enters viewport
document.addEventListener('astro:anim-inview', function(event) {
    console.log('Animation in view:', event.target);
});
```

---

## 📂 Project Structure

```
astro-animations/
├── .github/
│   ├── ISSUE_TEMPLATE/
│   │   ├── bug_report.md
│   │   └── feature_request.md
│   └── PULL_REQUEST_TEMPLATE.md
├── assets/
│   ├── css/
│   │   ├── admin.css          # Admin styling, tables, editor & modal preview
│   │   └── public.css         # Frontend container & lazy loading rules
│   └── js/
│       ├── admin.js           # Clipboard copy, slug generator & iframe sandbox
│       └── public.js          # Viewport IntersectionObserver engine
├── includes/
│   ├── class-astro-anim-admin.php    # Admin UI, menus, tabs & form actions
│   ├── class-astro-anim-db.php       # Database schema & prepared queries
│   ├── class-astro-anim-presets.php  # 5 built-in modern presets & installer
│   └── class-astro-anim-public.php   # Shortcode handler & frontend renderer
├── languages/
│   └── astro-animations.pot   # Translation catalog template
├── .gitignore
├── CHANGELOG.md
├── CONTRIBUTING.md
├── LICENSE                    # GNU General Public License v2.0
├── README.md                  # Plugin documentation
├── astro-animations.php       # Main plugin loader & activation hooks
└── readme.txt                 # WordPress.org repository specifications
```

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!  
Feel free to check out the [Issues page](https://github.com/ferhat/astro-animations/issues) or read our [Contributing Guide](CONTRIBUTING.md).

---

## 📄 License

This project is open-source software licensed under the **GNU General Public License v2.0 or later** (GPL-2.0-or-later). See the [LICENSE](LICENSE) file for more details.
