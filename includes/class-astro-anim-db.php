<?php
/**
 * Database manager for Astro Animations
 *
 * @package AstroAnimations
 */

if (!defined('ABSPATH')) {
    exit;
}

class Astro_Anim_DB {

    /**
     * Get table name with WordPress prefix
     *
     * @return string
     */
    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . 'astro_animations';
    }

    /**
     * Create or upgrade database table
     *
     * @return void
     */
    public static function create_tables() {
        global $wpdb;
        $table_name      = self::table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            description TEXT NULL,
            code LONGTEXT NOT NULL,
            container_width VARCHAR(50) DEFAULT '100%',
            container_height VARCHAR(50) DEFAULT 'auto',
            align VARCHAR(20) DEFAULT 'center',
            background VARCHAR(50) DEFAULT 'transparent',
            lazy_load TINYINT(1) DEFAULT 0,
            isolate_mode TINYINT(1) DEFAULT 1,
            custom_class VARCHAR(100) DEFAULT '',
            status TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Resolve legacy or localized slug aliases for cross-compatibility
     *
     * @param string $slug
     * @return string|null
     */
    public static function resolve_slug_alias($slug) {
        $aliases = [
            'particles'         => 'particle-network',
            'stats'             => 'glass-stats',
            'wave'              => 'fluid-waves',
            'waves'             => 'fluid-waves',
            'blob'              => 'morphing-blob',
            'features'          => 'interactive-cards',
            'particle-network'  => 'particles',
            'glass-stats'       => 'stats',
            'fluid-waves'       => 'wave',
            'morphing-blob'     => 'blob',
            'interactive-cards' => 'features',
        ];

        return $aliases[$slug] ?? null;
    }

    /**
     * Get all animations
     *
     * @param int|null $status Optional status filter (1 for active, 0 for inactive)
     * @return array
     */
    public static function get_all($status = null) {
        global $wpdb;
        $table = self::table_name();

        if ($status !== null) {
            return $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$table} WHERE status = %d ORDER BY id DESC",
                    intval($status)
                )
            );
        }

        return $wpdb->get_results("SELECT * FROM {$table} ORDER BY id DESC");
    }

    /**
     * Count total animations
     *
     * @return int
     */
    public static function count_all() {
        global $wpdb;
        $table = self::table_name();
        return intval($wpdb->get_var("SELECT COUNT(*) FROM {$table}"));
    }

    /**
     * Get single animation by ID
     *
     * @param int $id
     * @return object|null
     */
    public static function get_by_id($id) {
        global $wpdb;
        $table = self::table_name();
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE id = %d",
                intval($id)
            )
        );
    }

    /**
     * Get single animation by Slug with fallback alias resolution
     *
     * @param string $slug
     * @return object|null
     */
    public static function get_by_slug($slug) {
        global $wpdb;
        $table     = self::table_name();
        $clean_slug = sanitize_title($slug);

        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE slug = %s",
                $clean_slug
            )
        );

        // Fallback to alias if not found
        if (!$row) {
            $alias = self::resolve_slug_alias($clean_slug);
            if ($alias) {
                $row = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * FROM {$table} WHERE slug = %s",
                        $alias
                    )
                );
            }
        }

        return $row;
    }

    /**
     * Check if slug exists
     *
     * @param string $slug
     * @param int $exclude_id
     * @return bool
     */
    public static function slug_exists($slug, $exclude_id = 0) {
        global $wpdb;
        $table = self::table_name();
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE slug = %s AND id != %d",
            sanitize_title($slug),
            intval($exclude_id)
        );
        return (intval($wpdb->get_var($query)) > 0);
    }

    /**
     * Insert new animation
     *
     * @param array $data
     * @return int|false
     */
    public static function insert($data) {
        global $wpdb;
        $table = self::table_name();

        $slug          = !empty($data['slug']) ? sanitize_title($data['slug']) : sanitize_title($data['title']);
        $original_slug = $slug;
        $i             = 1;

        while (self::slug_exists($slug)) {
            $slug = $original_slug . '-' . $i;
            $i++;
        }

        $insert_data = [
            'title'            => sanitize_text_field($data['title']),
            'slug'             => $slug,
            'description'      => !empty($data['description']) ? sanitize_textarea_field($data['description']) : '',
            'code'             => $data['code'], // Preserve full HTML/SVG/Canvas script code
            'container_width'  => !empty($data['container_width']) ? sanitize_text_field($data['container_width']) : '100%',
            'container_height' => !empty($data['container_height']) ? sanitize_text_field($data['container_height']) : 'auto',
            'align'            => !empty($data['align']) ? sanitize_text_field($data['align']) : 'center',
            'background'       => !empty($data['background']) ? sanitize_text_field($data['background']) : 'transparent',
            'lazy_load'        => isset($data['lazy_load']) ? intval($data['lazy_load']) : 0,
            'isolate_mode'     => isset($data['isolate_mode']) ? intval($data['isolate_mode']) : 1,
            'custom_class'     => !empty($data['custom_class']) ? sanitize_html_class($data['custom_class']) : '',
            'status'           => isset($data['status']) ? intval($data['status']) : 1,
            'created_at'       => current_time('mysql'),
            'updated_at'       => current_time('mysql'),
        ];

        $result = $wpdb->insert($table, $insert_data);
        if ($result) {
            return $wpdb->insert_id;
        }
        return false;
    }

    /**
     * Update animation
     *
     * @param int $id
     * @param array $data
     * @return int|false
     */
    public static function update($id, $data) {
        global $wpdb;
        $table = self::table_name();

        $update_data = [
            'title'            => sanitize_text_field($data['title']),
            'description'      => !empty($data['description']) ? sanitize_textarea_field($data['description']) : '',
            'code'             => $data['code'],
            'container_width'  => !empty($data['container_width']) ? sanitize_text_field($data['container_width']) : '100%',
            'container_height' => !empty($data['container_height']) ? sanitize_text_field($data['container_height']) : 'auto',
            'align'            => !empty($data['align']) ? sanitize_text_field($data['align']) : 'center',
            'background'       => !empty($data['background']) ? sanitize_text_field($data['background']) : 'transparent',
            'lazy_load'        => isset($data['lazy_load']) ? intval($data['lazy_load']) : 0,
            'isolate_mode'     => isset($data['isolate_mode']) ? intval($data['isolate_mode']) : 1,
            'custom_class'     => !empty($data['custom_class']) ? sanitize_text_field($data['custom_class']) : '',
            'status'           => isset($data['status']) ? intval($data['status']) : 1,
            'updated_at'       => current_time('mysql'),
        ];

        if (!empty($data['slug'])) {
            $slug = sanitize_title($data['slug']);
            if (!self::slug_exists($slug, $id)) {
                $update_data['slug'] = $slug;
            }
        }

        return $wpdb->update($table, $update_data, ['id' => intval($id)]);
    }

    /**
     * Delete animation
     *
     * @param int $id
     * @return int|false
     */
    public static function delete($id) {
        global $wpdb;
        $table = self::table_name();
        return $wpdb->delete($table, ['id' => intval($id)]);
    }

    /**
     * Toggle status (Active / Inactive)
     *
     * @param int $id
     * @return int|false
     */
    public static function toggle_status($id) {
        global $wpdb;
        $table = self::table_name();
        $current = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT status FROM {$table} WHERE id = %d",
                intval($id)
            )
        );

        if ($current === null) {
            return false;
        }

        $new_status = (intval($current) === 1) ? 0 : 1;
        $wpdb->update($table, ['status' => $new_status], ['id' => intval($id)]);
        return $new_status;
    }
}
