=== Astro Animations - Interactive Canvas, SVG & HTML Component Manager ===
Contributors: ferhat, astroanimations
Tags: animation, canvas, svg, interactive, shortcode, components, glassmorphism, particles
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional WordPress manager for interactive HTML, Canvas simulations, SVG animations, and modern web components with zero layout shift.

== Description ==

**Astro Animations** is a high-performance WordPress animation and interactive component manager. It allows site owners, designers, and developers to centrally organize, preview, and embed isolated Canvas simulations, interactive SVG graphics, 3D widgets, and glassmorphic UI blocks anywhere using clean shortcodes.

### Key Highlights

* **Zero Cumulative Layout Shift (CLS)**: Built-in CSS isolation (`isolation: isolate; contain: layout;`) prevents animations from disrupting menus, themes, or page layouts.
* **Core Web Vitals Optimized**: Optional IntersectionObserver-powered Lazy Loading (`data-lazy="1"`) ensures fast initial page loads.
* **Live Sandbox Preview**: Instant real-time preview iframe inside the WordPress admin editor.
* **One-Click Shortcode Copy**: Easily copy shortcodes (`[astro_anim slug="..."]`) directly to your clipboard.
* **5 Built-in Modern Presets**: Includes the Neural Particle Network, 3D Glassmorphic KPI Cards, Kinetic Sine Wave Canvas, Morphing Gradient Blob, and Feature Architecture Explorer.
* **Fully Compatible**: Works out-of-the-box with Gutenberg, Elementor, Divi, TagDiv Composer, WPBakery, or custom theme templates.
* **Internationalized & Global**: Ready for translation with gettext POT template included.

== Installation ==

1. Upload the `astro-animations` directory to the `/wp-content/plugins/` directory, or upload the `astro-animations.zip` file via **Plugins > Add New > Upload Plugin**.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Navigate to **Astro Animations** in your WordPress admin menu.
4. Click **Install 5 Built-in Presets** or create your first custom animation.
5. Copy the generated shortcode (e.g. `[astro_anim slug="particle-network"]`) and paste it into any page or post.

== Frequently Asked Questions ==

= How do I embed an animation in Gutenberg? =
Add a **Shortcode** block to your page and paste `[astro_anim slug="particle-network"]`.

= Can I customize the width or height of an animation? =
Yes! You can set dimensions in the admin dashboard, or override them directly inside the shortcode:
`[astro_anim slug="particle-network" width="100%" height="450px" align="center"]`

= Does this plugin affect page loading speed? =
No. Astro Animations supports native lazy loading. When enabled, scripts and rendering loops activate only as the visitor scrolls near the component.

== Screenshots ==

1. Admin Dashboard with list of animations and quick copy buttons.
2. Code Editor with syntax highlighting and Live Sandbox Preview.
3. Hand-crafted Preset Library with modern web widgets.

== Changelog ==

= 1.0.0 =
* Initial global release.
* Added 5 hand-crafted interactive modern presets (Canvas particles, 3D glass cards, kinetic wave, SVG blob, UI modal).
* Added zero-CLS isolation and viewport lazy loading.
* Complete English localization and POT translation catalog.
