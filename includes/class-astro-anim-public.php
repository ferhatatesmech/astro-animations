<?php
/**
 * Frontend public renderer for Astro Animations
 *
 * @package AstroAnimations
 */

if (!defined('ABSPATH')) {
    exit;
}

class Astro_Anim_Public {

    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode('astro_anim', [$this, 'render_shortcode']);
        add_shortcode('astro_animation', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Register frontend scripts and styles
     *
     * @return void
     */
    public function enqueue_assets() {
        wp_register_style(
            'astro-anim-public',
            ASTRO_ANIM_URL . 'assets/css/public.css',
            [],
            ASTRO_ANIM_VERSION
        );

        wp_register_script(
            'astro-anim-public',
            ASTRO_ANIM_URL . 'assets/js/public.js',
            [],
            ASTRO_ANIM_VERSION,
            true
        );
    }

    /**
     * Render animation shortcode
     *
     * Usage examples:
     * [astro_anim slug="particle-network"]
     * [astro_anim slug="particle-network" width="100%" height="450px" align="center"]
     * [astro_anim id="1"]
     *
     * @param array $atts
     * @param string|null $content
     * @return string
     */
    public function render_shortcode($atts, $content = null) {
        $atts = shortcode_atts([
            'id'     => 0,
            'slug'   => '',
            'name'   => '',
            'width'  => '',
            'height' => '',
            'align'  => '',
            'bg'     => '',
            'class'  => '',
        ], $atts, 'astro_anim');

        $animation = null;

        if (!empty($atts['id'])) {
            $animation = Astro_Anim_DB::get_by_id(intval($atts['id']));
        } elseif (!empty($atts['slug'])) {
            $animation = Astro_Anim_DB::get_by_slug($atts['slug']);
        } elseif (!empty($atts['name'])) {
            $animation = Astro_Anim_DB::get_by_slug($atts['name']);
        }

        // Animation not found notice
        if (!$animation) {
            if (current_user_can('manage_options')) {
                $identifier = !empty($atts['slug']) ? $atts['slug'] : (!empty($atts['id']) ? $atts['id'] : $atts['name']);
                return sprintf(
                    '<div class="astro-anim-notice astro-anim-notice-error" style="padding:14px 18px; background:#fff1f2; border:1px solid #fecdd3; color:#be123c; border-radius:10px; font-size:13px; font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; margin:14px 0;"><strong>%s</strong> %s</div>',
                    esc_html__('Astro Animations Notice:', 'astro-animations'),
                    sprintf(
                        /* translators: %s: animation slug or ID */
                        esc_html__('Animation "%s" was not found. Please verify the slug or ID in the Astro Animations manager.', 'astro-animations'),
                        esc_html($identifier)
                    )
                );
            }
            return '';
        }

        // Inactive status check
        if (intval($animation->status) !== 1) {
            if (current_user_can('manage_options')) {
                return sprintf(
                    '<div class="astro-anim-notice astro-anim-notice-inactive" style="padding:10px 14px; background:#f8fafc; border:1px dashed #94a3b8; color:#64748b; border-radius:8px; font-size:12px; font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif; margin:10px 0;"><em>%s</em></div>',
                    sprintf(
                        /* translators: %s: animation title */
                        esc_html__('Astro Animation ("%s") is currently inactive and hidden from site visitors.', 'astro-animations'),
                        esc_html($animation->title)
                    )
                );
            }
            return '';
        }

        // Enqueue assets
        wp_enqueue_style('astro-anim-public');
        wp_enqueue_script('astro-anim-public');

        // Parameter resolution (shortcode attribute overrides DB setting)
        $width  = !empty($atts['width'])  ? $atts['width']  : $animation->container_width;
        $height = !empty($atts['height']) ? $atts['height'] : $animation->container_height;
        $align  = !empty($atts['align'])  ? $atts['align']  : $animation->align;
        $bg     = !empty($atts['bg'])     ? $atts['bg']     : $animation->background;

        // Container margin based on alignment
        $margin = '0 auto';
        if ($align === 'left') {
            $margin = '0 auto 0 0';
        } elseif ($align === 'right') {
            $margin = '0 0 0 auto';
        }

        // Container inline CSS
        $style_rules   = [];
        $style_rules[] = 'width: 100%;';
        if (!empty($width) && $width !== 'auto') {
            $style_rules[] = 'max-width: ' . esc_attr($width) . ';';
        }
        if (!empty($height) && $height !== 'auto') {
            $style_rules[] = 'min-height: ' . esc_attr($height) . ';';
        }
        $style_rules[] = 'margin: ' . esc_attr($margin) . ';';
        if (!empty($bg) && $bg !== 'transparent') {
            $style_rules[] = 'background: ' . esc_attr($bg) . ';';
        }
        if (intval($animation->isolate_mode) === 1) {
            $style_rules[] = 'isolation: isolate;';
            $style_rules[] = 'contain: layout;';
        }

        $style_attr = implode(' ', $style_rules);

        // Wrapper CSS Classes
        $wrapper_classes = [
            'astro-anim-root',
            'astro-anim-' . sanitize_html_class($animation->slug),
            'astro-anim-id-' . intval($animation->id)
        ];
        if (!empty($animation->custom_class)) {
            $wrapper_classes[] = sanitize_html_class($animation->custom_class);
        }
        if (!empty($atts['class'])) {
            $wrapper_classes[] = sanitize_html_class($atts['class']);
        }

        $class_attr = implode(' ', array_filter($wrapper_classes));
        $lazy_attr  = (intval($animation->lazy_load) === 1) ? ' data-lazy="1"' : '';

        // Build HTML output
        $output  = '';
        $output .= '<!-- Astro Animations: ' . esc_attr($animation->title) . ' [slug=' . esc_attr($animation->slug) . '] -->' . "\n";
        $output .= '<div id="astro-anim-' . intval($animation->id) . '" class="' . esc_attr($class_attr) . '" style="' . $style_attr . '"' . $lazy_attr . '>' . "\n";
        $output .= $animation->code . "\n";
        $output .= '</div>' . "\n";
        $output .= '<!-- /Astro Animations: ' . esc_attr($animation->slug) . ' -->' . "\n";

        /**
         * Filter rendered animation markup
         *
         * @param string $output
         * @param object $animation
         * @param array  $atts
         */
        return apply_filters('astro_anim_output', $output, $animation, $atts);
    }
}
