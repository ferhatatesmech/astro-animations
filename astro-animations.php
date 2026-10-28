<?php
/**
 * Plugin Name:       Astro Animations - Interactive Canvas, SVG & HTML Component Manager
 * Plugin URI:        https://github.com/ferhat/astro-animations
 * Description:       High-performance WordPress animation and interactive component manager. Easily embed isolated Canvas simulations, interactive SVG graphics, particle physics networks, and glassmorphic UI components with clean shortcodes.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Astro Animations Contributors
 * Author URI:        https://github.com/ferhat/astro-animations
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       astro-animations
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin Constants
define('ASTRO_ANIM_VERSION', '1.0.0');
define('ASTRO_ANIM_FILE', __FILE__);
define('ASTRO_ANIM_DIR', plugin_dir_path(__FILE__));
define('ASTRO_ANIM_URL', plugin_dir_url(__FILE__));
define('ASTRO_ANIM_BASENAME', plugin_basename(__FILE__));

// Require Core Classes
require_once ASTRO_ANIM_DIR . 'includes/class-astro-anim-db.php';
require_once ASTRO_ANIM_DIR . 'includes/class-astro-anim-presets.php';
require_once ASTRO_ANIM_DIR . 'includes/class-astro-anim-public.php';
require_once ASTRO_ANIM_DIR . 'includes/class-astro-anim-admin.php';

/**
 * Load plugin text domain for internationalization (i18n)
 */
function astro_anim_load_textdomain() {
    load_plugin_textdomain(
        'astro-animations',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
}
add_action('init', 'astro_anim_load_textdomain');

/**
 * Plugin activation hook
 */
function astro_anim_activate() {
    Astro_Anim_DB::create_tables();

    // Auto-install presets on initial activation if table is empty
    $existing = Astro_Anim_DB::get_all();
    if (empty($existing)) {
        Astro_Anim_Presets::install_presets();
    }
}
register_activation_hook(__FILE__, 'astro_anim_activate');

/**
 * Plugin deactivation hook
 */
function astro_anim_deactivate() {
    // Flush rewrite rules or clean temporary transients if any
}
register_deactivation_hook(__FILE__, 'astro_anim_deactivate');

/**
 * Add settings action link to Plugins listing page
 */
function astro_anim_action_links($links) {
    $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=astro-animations')) . '">' . esc_html__('Dashboard', 'astro-animations') . '</a>';
    $presets_link  = '<a href="' . esc_url(admin_url('admin.php?page=astro-animations-presets')) . '">' . esc_html__('Preset Library', 'astro-animations') . '</a>';
    array_unshift($links, $presets_link);
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . ASTRO_ANIM_BASENAME, 'astro_anim_action_links');

/**
 * Initialize components
 */
function astro_anim_init() {
    new Astro_Anim_Public();
    if (is_admin()) {
        new Astro_Anim_Admin();
    }
}
add_action('plugins_loaded', 'astro_anim_init');
