<?php
/**
 * Admin Manager and UI for Astro Animations
 *
 * @package AstroAnimations
 */

if (!defined('ABSPATH')) {
    exit;
}

class Astro_Anim_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('admin_init', [$this, 'handle_admin_actions']);
    }

    /**
     * Register Admin Menu and Submenus
     *
     * @return void
     */
    public function register_admin_menu() {
        $icon = 'dashicons-art';

        add_menu_page(
            __('Astro Animations', 'astro-animations'),
            __('Astro Animations', 'astro-animations'),
            'manage_options',
            'astro-animations',
            [$this, 'render_list_page'],
            $icon,
            30
        );

        add_submenu_page(
            'astro-animations',
            __('All Animations', 'astro-animations'),
            __('All Animations', 'astro-animations'),
            'manage_options',
            'astro-animations',
            [$this, 'render_list_page']
        );

        add_submenu_page(
            'astro-animations',
            __('Add New Animation', 'astro-animations'),
            __('Add New', 'astro-animations'),
            'manage_options',
            'astro-animations-add',
            [$this, 'render_edit_page']
        );

        add_submenu_page(
            'astro-animations',
            __('Preset Library', 'astro-animations'),
            __('Preset Library', 'astro-animations'),
            'manage_options',
            'astro-animations-presets',
            [$this, 'render_presets_page']
        );
    }

    /**
     * Enqueue Admin Assets
     *
     * @param string $hook
     * @return void
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'astro-animation') === false) {
            return;
        }

        wp_enqueue_style(
            'astro-anim-admin',
            ASTRO_ANIM_URL . 'assets/css/admin.css',
            [],
            ASTRO_ANIM_VERSION
        );

        wp_enqueue_script(
            'astro-anim-admin',
            ASTRO_ANIM_URL . 'assets/js/admin.js',
            ['jquery'],
            ASTRO_ANIM_VERSION,
            true
        );

        wp_localize_script('astro-anim-admin', 'astroAnimAdmin', [
            'ajaxUrl'    => admin_url('admin-ajax.php'),
            'copiedText' => __('Shortcode copied to clipboard!', 'astro-animations'),
        ]);
    }

    /**
     * Handle Admin Form Submissions and Actions
     *
     * @return void
     */
    public function handle_admin_actions() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Save / Update Action
        if (isset($_POST['astro_anim_save']) && check_admin_referer('astro_anim_save_action', 'astro_anim_nonce')) {
            $id = isset($_POST['anim_id']) ? intval($_POST['anim_id']) : 0;

            // Raw code preservation for scripts/styles
            $code = wp_unslash($_POST['code']);

            $data = [
                'title'            => sanitize_text_field($_POST['title']),
                'slug'             => sanitize_title($_POST['slug']),
                'description'      => sanitize_textarea_field($_POST['description'] ?? ''),
                'code'             => $code,
                'container_width'  => sanitize_text_field($_POST['container_width'] ?? '100%'),
                'container_height' => sanitize_text_field($_POST['container_height'] ?? 'auto'),
                'align'            => sanitize_text_field($_POST['align'] ?? 'center'),
                'background'       => sanitize_text_field($_POST['background'] ?? 'transparent'),
                'lazy_load'        => isset($_POST['lazy_load']) ? 1 : 0,
                'isolate_mode'     => isset($_POST['isolate_mode']) ? 1 : 0,
                'custom_class'     => sanitize_text_field($_POST['custom_class'] ?? ''),
                'status'           => isset($_POST['status']) ? 1 : 0,
            ];

            if ($id > 0) {
                Astro_Anim_DB::update($id, $data);
                wp_safe_redirect(admin_url('admin.php?page=astro-animations&message=updated'));
            } else {
                Astro_Anim_DB::insert($data);
                wp_safe_redirect(admin_url('admin.php?page=astro-animations&message=created'));
            }
            exit;
        }

        // Delete Action
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            check_admin_referer('delete_anim_' . $id);
            Astro_Anim_DB::delete($id);
            wp_safe_redirect(admin_url('admin.php?page=astro-animations&message=deleted'));
            exit;
        }

        // Toggle Status Action
        if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            check_admin_referer('toggle_anim_' . $id);
            Astro_Anim_DB::toggle_status($id);
            wp_safe_redirect(admin_url('admin.php?page=astro-animations&message=toggled'));
            exit;
        }

        // Duplicate Action
        if (isset($_GET['action']) && $_GET['action'] === 'duplicate' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            check_admin_referer('duplicate_anim_' . $id);
            $orig = Astro_Anim_DB::get_by_id($id);
            if ($orig) {
                $clone_data = (array) $orig;
                unset($clone_data['id']);
                $clone_data['title'] .= ' (' . __('Copy', 'astro-animations') . ')';
                $clone_data['slug']  .= '-copy';
                Astro_Anim_DB::insert($clone_data);
            }
            wp_safe_redirect(admin_url('admin.php?page=astro-animations&message=duplicated'));
            exit;
        }

        // Install Presets Action
        if (isset($_POST['astro_anim_install_presets']) && check_admin_referer('astro_anim_presets_action', 'astro_anim_presets_nonce')) {
            $count = Astro_Anim_Presets::install_presets();
            wp_safe_redirect(admin_url('admin.php?page=astro-animations&message=presets_installed&count=' . $count));
            exit;
        }
    }

    /**
     * Render Animations List Page
     *
     * @return void
     */
    public function render_list_page() {
        $animations = Astro_Anim_DB::get_all();
        $message    = $_GET['message'] ?? '';
        $count      = $_GET['count'] ?? 0;
        ?>
        <div class="wrap astro-admin-wrap">
            <div class="astro-admin-header">
                <div class="astro-admin-header-title">
                    <h1><?php esc_html_e('Astro Animations Manager', 'astro-animations'); ?></h1>
                    <p class="astro-admin-subtitle"><?php esc_html_e('Manage interactive Canvas simulations, responsive SVGs, and rich HTML/JS widgets with zero layout shifts.', 'astro-animations'); ?></p>
                </div>
                <div class="astro-admin-header-actions">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=astro-animations-add')); ?>" class="button button-primary button-hero">
                        <span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e('Add New Animation', 'astro-animations'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=astro-animations-presets')); ?>" class="button button-secondary">
                        <span class="dashicons dashicons-download"></span> <?php esc_html_e('Preset Library (5 Presets)', 'astro-animations'); ?>
                    </a>
                </div>
            </div>

            <?php if ($message === 'created'): ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e('New animation created successfully!', 'astro-animations'); ?></p></div>
            <?php elseif ($message === 'updated'): ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Animation code and configuration updated.', 'astro-animations'); ?></p></div>
            <?php elseif ($message === 'deleted'): ?>
                <div class="notice notice-warning is-dismissible"><p><?php esc_html_e('Animation removed.', 'astro-animations'); ?></p></div>
            <?php elseif ($message === 'toggled'): ?>
                <div class="notice notice-info is-dismissible"><p><?php esc_html_e('Animation publication status changed.', 'astro-animations'); ?></p></div>
            <?php elseif ($message === 'duplicated'): ?>
                <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Animation duplicated successfully.', 'astro-animations'); ?></p></div>
            <?php elseif ($message === 'presets_installed'): ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <?php
                        printf(
                            /* translators: %d: count of installed presets */
                            esc_html__('Success! %d presets have been loaded into your library.', 'astro-animations'),
                            intval($count)
                        );
                        ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (empty($animations)): ?>
                <div class="astro-empty-card">
                    <div class="astro-empty-icon"><span class="dashicons dashicons-art"></span></div>
                    <h2><?php esc_html_e('No Animations Found', 'astro-animations'); ?></h2>
                    <p><?php esc_html_e('Get started by installing the 5 built-in presets (Particle Network, 3D Glassmorphic Cards, Kinetic Wave, Morphing Blob, and Feature Showcase) with a single click.', 'astro-animations'); ?></p>
                    <form method="post" action="">
                        <?php wp_nonce_field('astro_anim_presets_action', 'astro_anim_presets_nonce'); ?>
                        <input type="hidden" name="astro_anim_install_presets" value="1">
                        <button type="submit" class="button button-primary button-hero">
                            <span class="dashicons dashicons-download"></span> <?php esc_html_e('Install 5 Built-in Presets Now', 'astro-animations'); ?>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="astro-card-table">
                    <table class="wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr>
                                <th style="width: 26%;"><?php esc_html_e('Title & Description', 'astro-animations'); ?></th>
                                <th style="width: 27%;"><?php esc_html_e('Shortcode', 'astro-animations'); ?></th>
                                <th style="width: 15%;"><?php esc_html_e('Dimensions', 'astro-animations'); ?></th>
                                <th style="width: 12%;"><?php esc_html_e('Status', 'astro-animations'); ?></th>
                                <th style="width: 20%; text-align:right;"><?php esc_html_e('Actions', 'astro-animations'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($animations as $anim): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <a href="<?php echo esc_url(admin_url('admin.php?page=astro-animations-add&id=' . $anim->id)); ?>" class="row-title">
                                                <?php echo esc_html($anim->title); ?>
                                            </a>
                                        </strong>
                                        <?php if (!empty($anim->description)): ?>
                                            <p class="description"><?php echo esc_html($anim->description); ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="astro-shortcode-box">
                                            <code>[astro_anim slug="<?php echo esc_attr($anim->slug); ?>"]</code>
                                            <button type="button" class="button button-small astro-copy-btn" data-code='[astro_anim slug="<?php echo esc_attr($anim->slug); ?>"]' title="<?php esc_attr_e('Copy shortcode to clipboard', 'astro-animations'); ?>">
                                                <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e('Copy', 'astro-animations'); ?>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="astro-dim-pill">
                                            <?php echo esc_html($anim->container_width); ?> × <?php echo esc_html($anim->container_height); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (intval($anim->status) === 1): ?>
                                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=astro-animations&action=toggle&id=' . $anim->id), 'toggle_anim_' . $anim->id)); ?>" class="astro-status-badge active" title="<?php esc_attr_e('Click to deactivate', 'astro-animations'); ?>">
                                                <span class="dot"></span> <?php esc_html_e('Active', 'astro-animations'); ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=astro-animations&action=toggle&id=' . $anim->id), 'toggle_anim_' . $anim->id)); ?>" class="astro-status-badge inactive" title="<?php esc_attr_e('Click to activate', 'astro-animations'); ?>">
                                                <span class="dot"></span> <?php esc_html_e('Inactive', 'astro-animations'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:right;">
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=astro-animations-add&id=' . $anim->id)); ?>" class="button button-small" title="<?php esc_attr_e('Edit', 'astro-animations'); ?>">
                                            <span class="dashicons dashicons-edit"></span> <?php esc_html_e('Edit', 'astro-animations'); ?>
                                        </a>
                                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=astro-animations&action=duplicate&id=' . $anim->id), 'duplicate_anim_' . $anim->id)); ?>" class="button button-small" title="<?php esc_attr_e('Duplicate', 'astro-animations'); ?>">
                                            <span class="dashicons dashicons-admin-page"></span>
                                        </a>
                                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=astro-animations&action=delete&id=' . $anim->id), 'delete_anim_' . $anim->id)); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php echo esc_js(__('Are you sure you want to delete this animation?', 'astro-animations')); ?>');" title="<?php esc_attr_e('Delete', 'astro-animations'); ?>">
                                            <span class="dashicons dashicons-trash"></span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="astro-admin-tip-box">
                <h3><span class="dashicons dashicons-info"></span> <?php esc_html_e('How to Embed on Your Pages', 'astro-animations'); ?></h3>
                <p><?php esc_html_e('You can embed any animation into any page builder or template:', 'astro-animations'); ?></p>
                <ol>
                    <li><strong><?php esc_html_e('Block Editor (Gutenberg):', 'astro-animations'); ?></strong> <?php esc_html_e('Add a "Shortcode" block and paste the shortcode.', 'astro-animations'); ?></li>
                    <li><strong><?php esc_html_e('Page Builders (Elementor, Divi, TagDiv Composer, WPBakery):', 'astro-animations'); ?></strong> <?php esc_html_e('Add a Shortcode or HTML widget and insert the tag.', 'astro-animations'); ?></li>
                    <li><strong><?php esc_html_e('PHP Templates:', 'astro-animations'); ?></strong> <code>&lt;?php echo do_shortcode(\'[astro_anim slug="..."]\'); ?&gt;</code></li>
                </ol>
            </div>
        </div>
        <?php
    }

    /**
     * Render Add / Edit Page
     *
     * @return void
     */
    public function render_edit_page() {
        $id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $anim = null;

        if ($id > 0) {
            $anim = Astro_Anim_DB::get_by_id($id);
        }

        $title        = $anim ? $anim->title : '';
        $slug         = $anim ? $anim->slug : '';
        $desc         = $anim ? $anim->description : '';
        $code         = $anim ? $anim->code : '';
        $width        = $anim ? $anim->container_width : '100%';
        $height       = $anim ? $anim->container_height : 'auto';
        $align        = $anim ? $anim->align : 'center';
        $bg           = $anim ? $anim->background : 'transparent';
        $lazy         = $anim ? intval($anim->lazy_load) : 0;
        $isolate      = $anim ? intval($anim->isolate_mode) : 1;
        $custom_class = $anim ? $anim->custom_class : '';
        $status       = $anim ? intval($anim->status) : 1;
        ?>
        <div class="wrap astro-admin-wrap">
            <div class="astro-admin-header">
                <div class="astro-admin-header-title">
                    <h1><?php echo $id > 0 ? sprintf(esc_html__('Edit Animation: %s', 'astro-animations'), esc_html($title)) : esc_html__('Add New Animation', 'astro-animations'); ?></h1>
                    <p class="astro-admin-subtitle"><?php esc_html_e('Paste your HTML, CSS, SVG, or JavaScript code and configure container dimensions and isolation.', 'astro-animations'); ?></p>
                </div>
                <div class="astro-admin-header-actions">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=astro-animations')); ?>" class="button button-secondary">
                        <?php esc_html_e('← Back to All Animations', 'astro-animations'); ?>
                    </a>
                </div>
            </div>

            <form method="post" action="" id="astroAnimForm">
                <?php wp_nonce_field('astro_anim_save_action', 'astro_anim_nonce'); ?>
                <input type="hidden" name="astro_anim_save" value="1">
                <input type="hidden" name="anim_id" value="<?php echo intval($id); ?>">

                <div class="astro-editor-grid">
                    <!-- Left: Code & Core Info -->
                    <div class="astro-editor-main">
                        <div class="astro-card-box">
                            <div class="astro-form-group">
                                <label for="anim_title"><strong><?php esc_html_e('Animation Title', 'astro-animations'); ?></strong> <span class="required">*</span></label>
                                <input type="text" id="anim_title" name="title" value="<?php echo esc_attr($title); ?>" class="large-text" required placeholder="<?php esc_attr_e('e.g. Interactive Particle Network', 'astro-animations'); ?>">
                            </div>

                            <div class="astro-form-row">
                                <div class="astro-form-group" style="flex:1;">
                                    <label for="anim_slug"><strong><?php esc_html_e('Shortcode Slug', 'astro-animations'); ?></strong> <span class="required">*</span></label>
                                    <input type="text" id="anim_slug" name="slug" value="<?php echo esc_attr($slug); ?>" class="regular-text" required placeholder="<?php esc_attr_e('e.g. particle-network', 'astro-animations'); ?>">
                                    <small class="description"><?php esc_html_e('Usage:', 'astro-animations'); ?> <code>[astro_anim slug="<?php echo esc_attr($slug ?: 'sample'); ?>"]</code></small>
                                </div>
                                <div class="astro-form-group" style="flex:1;">
                                    <label for="anim_desc"><strong><?php esc_html_e('Description / Internal Note', 'astro-animations'); ?></strong></label>
                                    <input type="text" id="anim_desc" name="description" value="<?php echo esc_attr($desc); ?>" class="regular-text" placeholder="<?php esc_attr_e('e.g. Hero interactive particle physics component', 'astro-animations'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="astro-card-box">
                            <div class="astro-tabs-bar">
                                <button type="button" class="astro-tab-btn active" data-tab="code-tab">
                                    <span class="dashicons dashicons-editor-code"></span> <?php esc_html_e('HTML / CSS / JS Code', 'astro-animations'); ?>
                                </button>
                                <button type="button" class="astro-tab-btn" data-tab="preview-tab" id="astroLivePreviewBtn">
                                    <span class="dashicons dashicons-visibility"></span> <?php esc_html_e('Live Preview', 'astro-animations'); ?>
                                </button>
                            </div>

                            <div class="astro-tab-content active" id="code-tab">
                                <label for="anim_code" class="screen-reader-text"><?php esc_html_e('Animation Code', 'astro-animations'); ?></label>
                                <textarea id="anim_code" name="code" rows="24" class="large-text code astro-code-editor" required placeholder="<?php esc_attr_e('Paste your <style>, <canvas>, <svg>, <div>, and <script> code here...', 'astro-animations'); ?>"><?php echo esc_textarea($code); ?></textarea>
                                <p class="description" style="margin-top:8px;">
                                    💡 <strong><?php esc_html_e('Developer Tip:', 'astro-animations'); ?></strong> <?php esc_html_e('You can paste complete snippets with inline <style> and <script>. The code executes in isolated container context.', 'astro-animations'); ?>
                                </p>
                            </div>

                            <div class="astro-tab-content" id="preview-tab">
                                <div class="astro-preview-container">
                                    <div class="astro-preview-toolbar">
                                        <span><?php esc_html_e('Live Interactive Sandbox Preview', 'astro-animations'); ?></span>
                                        <button type="button" class="button button-small" id="astroRefreshPreview">
                                            <span class="dashicons dashicons-update"></span> <?php esc_html_e('Refresh', 'astro-animations'); ?>
                                        </button>
                                    </div>
                                    <iframe id="astroPreviewFrame" src="about:blank"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Settings & Properties -->
                    <div class="astro-editor-sidebar">
                        <div class="astro-card-box">
                            <h3><?php esc_html_e('Container & Display', 'astro-animations'); ?></h3>

                            <div class="astro-form-group">
                                <label for="container_width"><?php esc_html_e('Max Width (CSS max-width)', 'astro-animations'); ?></label>
                                <input type="text" id="container_width" name="container_width" value="<?php echo esc_attr($width); ?>" class="regular-text" placeholder="100%, 860px, 1200px">
                                <small class="description"><?php esc_html_e('e.g.', 'astro-animations'); ?> <code>100%</code>, <code>860px</code>, <code>1200px</code></small>
                            </div>

                            <div class="astro-form-group">
                                <label for="container_height"><?php esc_html_e('Height / Min-Height', 'astro-animations'); ?></label>
                                <input type="text" id="container_height" name="container_height" value="<?php echo esc_attr($height); ?>" class="regular-text" placeholder="auto, 600px, clamp(...)">
                                <small class="description"><?php esc_html_e('e.g.', 'astro-animations'); ?> <code>auto</code>, <code>clamp(500px, 80vh, 720px)</code></small>
                            </div>

                            <div class="astro-form-group">
                                <label for="anim_align"><?php esc_html_e('Alignment', 'astro-animations'); ?></label>
                                <select id="anim_align" name="align" class="regular-text">
                                    <option value="center" <?php selected($align, 'center'); ?>><?php esc_html_e('Center', 'astro-animations'); ?></option>
                                    <option value="left" <?php selected($align, 'left'); ?>><?php esc_html_e('Left', 'astro-animations'); ?></option>
                                    <option value="right" <?php selected($align, 'right'); ?>><?php esc_html_e('Right', 'astro-animations'); ?></option>
                                </select>
                            </div>

                            <div class="astro-form-group">
                                <label for="anim_bg"><?php esc_html_e('Background Color', 'astro-animations'); ?></label>
                                <input type="text" id="anim_bg" name="background" value="<?php echo esc_attr($bg); ?>" class="regular-text" placeholder="transparent or #ffffff">
                            </div>
                        </div>

                        <div class="astro-card-box">
                            <h3><?php esc_html_e('Performance & Security', 'astro-animations'); ?></h3>

                            <div class="astro-form-checkbox">
                                <label>
                                    <input type="checkbox" name="isolate_mode" value="1" <?php checked($isolate, 1); ?>>
                                    <strong><?php esc_html_e('CSS & Layout Isolation', 'astro-animations'); ?></strong>
                                </label>
                                <small class="description"><?php esc_html_e('Applies isolation: isolate and contain: layout to eliminate Cumulative Layout Shift (CLS).', 'astro-animations'); ?></small>
                            </div>

                            <div class="astro-form-checkbox">
                                <label>
                                    <input type="checkbox" name="lazy_load" value="1" <?php checked($lazy, 1); ?>>
                                    <strong><?php esc_html_e('Lazy Load on Scroll', 'astro-animations'); ?></strong>
                                </label>
                                <small class="description"><?php esc_html_e('Initializes scripts only when entering viewport, maximizing Google Core Web Vitals speed scores.', 'astro-animations'); ?></small>
                            </div>

                            <div class="astro-form-group" style="margin-top:14px;">
                                <label for="custom_class"><?php esc_html_e('Custom CSS Class', 'astro-animations'); ?></label>
                                <input type="text" id="custom_class" name="custom_class" value="<?php echo esc_attr($custom_class); ?>" class="regular-text" placeholder="my-custom-animation">
                            </div>
                        </div>

                        <div class="astro-card-box">
                            <h3><?php esc_html_e('Publication Status', 'astro-animations'); ?></h3>
                            <div class="astro-form-checkbox">
                                <label>
                                    <input type="checkbox" name="status" value="1" <?php checked($status, 1); ?>>
                                    <strong><?php esc_html_e('Active (Published)', 'astro-animations'); ?></strong>
                                </label>
                            </div>

                            <div class="astro-submit-box" style="margin-top:18px;">
                                <button type="submit" class="button button-primary button-large button-block">
                                    <span class="dashicons dashicons-saved"></span> <?php echo $id > 0 ? esc_html__('Save Changes', 'astro-animations') : esc_html__('Save & Publish Animation', 'astro-animations'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Render Presets Page
     *
     * @return void
     */
    public function render_presets_page() {
        $presets = Astro_Anim_Presets::get_presets();
        ?>
        <div class="wrap astro-admin-wrap">
            <div class="astro-admin-header">
                <div class="astro-admin-header-title">
                    <h1><?php esc_html_e('Preset Library', 'astro-animations'); ?></h1>
                    <p class="astro-admin-subtitle"><?php esc_html_e('Install and explore 5 hand-crafted, high-performance web simulations and interactive UI widgets.', 'astro-animations'); ?></p>
                </div>
                <div class="astro-admin-header-actions">
                    <form method="post" action="">
                        <?php wp_nonce_field('astro_anim_presets_action', 'astro_anim_presets_nonce'); ?>
                        <input type="hidden" name="astro_anim_install_presets" value="1">
                        <button type="submit" class="button button-primary button-hero">
                            <span class="dashicons dashicons-download"></span> <?php esc_html_e('Install / Sync All 5 Presets', 'astro-animations'); ?>
                        </button>
                    </form>
                </div>
            </div>

            <div class="astro-presets-grid">
                <?php foreach ($presets as $p): ?>
                    <?php $exists = Astro_Anim_DB::slug_exists($p['slug']); ?>
                    <div class="astro-preset-card <?php echo $exists ? 'is-installed' : ''; ?>">
                        <div class="astro-preset-header">
                            <span class="dashicons dashicons-art"></span>
                            <h4><?php echo esc_html($p['title']); ?></h4>
                            <?php if ($exists): ?>
                                <span class="astro-badge-installed"><?php esc_html_e('Installed', 'astro-animations'); ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="astro-preset-desc"><?php echo esc_html($p['description']); ?></p>
                        <div class="astro-preset-footer">
                            <code>[astro_anim slug="<?php echo esc_attr($p['slug']); ?>"]</code>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
