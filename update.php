<?php
function AVWG_updater($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $plugin_slug = AVWG_SLUG;
    $plugin_file = $plugin_slug . '/index.php'; // Asegúrate de que esta ruta sea correcta
    $github_api_url = 'https://api.github.com/repos/franciscoblancojn/aveonline-widget-guia/releases/latest';

    $github_token = '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';

    $response = wp_remote_get($github_api_url, [
        'headers' => [
            'User-Agent' => 'WordPress-Updater',
            'Authorization' => 'token ' . $github_token, // 🔑 Autenticación con token
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

    $plugin_data = get_plugin_data(AVWG_DIR . 'index.php' );
    $current_version = $plugin_data['Version'];


    if (version_compare($current_version, $latest_version, '<')) {
        // Buscar el archivo zip en los assets
        $download_url = 'https://github.com/franciscoblancojn/aveonline-widget-guia/archive/refs/heads/master.zip';
        // if (!empty($release->assets)) {
        //     foreach ($release->assets as $asset) {
        //         if (strpos($asset->name, '.zip') !== false) {
        //             $download_url = $asset->browser_download_url;
        //             break;
        //         }
        //     }
        // }
        // // Si no hay assets, usar el zipball_url (menos confiable)
        // if (empty($download_url)) {
        //     $download_url = $release->zipball_url;
        // }
        // var_dump($download_url);

        $transient->response[$plugin_file] = (object) [
            'new_version' => $latest_version,
            'package'     => $download_url,
            'slug'        => $plugin_slug,
            'url'         => 'https://github.com/franciscoblancojn/aveonline-widget-guia',
        ];
    }

    return $transient;
}
add_filter('site_transient_update_plugins', 'AVWG_updater');
// add_filter('pre_set_site_transient_update_plugins', 'AVWG_updater');

function AVWG_add_update_button($links, $file) {
    if ($file == AVWG_BASENAME) {
        $actualizar_url = wp_nonce_url(
            admin_url('update.php?action=upgrade-plugin&plugin=' . $file),
            'upgrade-plugin_' . $file
        );

        $boton_actualizar = '<a href="' . esc_url($actualizar_url) . '" style="color: #0073aa; font-weight: bold;">Actualizar</a>';

        $links[] = $boton_actualizar;
    }
    return $links;
}
add_filter('plugin_action_links_' . AVWG_BASENAME, 'AVWG_add_update_button', 10, 2);
