<?php
function AVWG_updater($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    // Definir constantes
    $plugin_slug =  basename(rtrim(AVWG_DIR, '/'));
    $plugin_file = $plugin_slug . '/index.php';
    $github_api_url = 'https://api.github.com/repos/franciscoblancojn/aveonline-widget-guia/releases/latest';
    
    // ⚠️ Asegúrate de almacenar el token de manera segura
    $github_token = 's';

    // Llamada a la API de GitHub
    $response = wp_remote_get($github_api_url, [
        'headers' => [
            'User-Agent'    => 'WordPress-Updater',
            'Authorization' => 'token ' . $github_token,
        ]
    ]);
    
    if (is_wp_error($response)) {
        return $transient;
    }

    $release = json_decode(wp_remote_retrieve_body($response));

    if (!isset($release->tag_name)) {
        return $transient;
    }

    $latest_version = ltrim($release->tag_name, 'v');

    // Obtener la versión actual del plugin
    if (!function_exists('get_plugin_data')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugin_path = defined('AVWG_DIR') ? AVWG_DIR . 'index.php' : WP_PLUGIN_DIR . '/' . $plugin_file;
    $plugin_data = get_plugin_data($plugin_path);
    $current_version = $plugin_data['Version'];


    // Comparar versiones
    if (version_compare($current_version, $latest_version, '<')) {
        $download_url = '';

        // Buscar el archivo ZIP en los assets
        if (!empty($release->assets)) {
            foreach ($release->assets as $asset) {
                if (strpos($asset->name, '.zip') !== false) {
                    $download_url = $asset->browser_download_url;
                    break;
                }
            }
        }

        // Si no hay ZIP en assets, usar zipball_url como última opción
        if (empty($download_url) && isset($release->zipball_url)) {
            $download_url = $release->zipball_url;
        }

        if (!empty($download_url)) {
            $transient->response[$plugin_file] = (object) [
                'new_version' => $latest_version,
                'package'     => "https://github.com/franciscoblancojn/aveonline-widget-guia/archive/refs/heads/master.zip",
                'slug'        => $plugin_slug,
                'url'         => 'https://github.com/franciscoblancojn/aveonline-widget-guia',
            ];
        }
    }

    return $transient;
}
add_filter('site_transient_update_plugins', 'AVWG_updater');

/**
 * Agregar botón de actualización manual al listado de plugins
 */
function AVWG_add_update_button($links, $file) {
    if ($file == AVWG_BASENAME) {
        $actualizar_url = wp_nonce_url(
            admin_url('update.php?action=upgrade-plugin&plugin=' . $file),
            'upgrade-plugin_' . $file
        );

        $links[] = '<a href="' . esc_url($actualizar_url) . '" style="color: #0073aa; font-weight: bold;">Actualizar</a>';
    }
    return $links;
}
add_filter('plugin_action_links_' . AVWG_BASENAME, 'AVWG_add_update_button', 10, 2);

// Forzar actualización de plugins
delete_site_transient('update_plugins');

