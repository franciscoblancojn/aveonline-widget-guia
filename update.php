<?php
function AVWG_updater($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $github_api_url = 'https://github.com/franciscoblancojn/aveonline-widget-guia/releases/latest';
    $response = wp_remote_get($github_api_url);

    if (is_wp_error($response)) {
        return $transient;
    }

    $release = json_decode(wp_remote_retrieve_body($response));

    if (!isset($release->tag_name)) {
        return $transient;
    }

    $latest_version = ltrim($release->tag_name, 'v');
    $current_version = get_plugin_data(__FILE__)['Version'];

    if (version_compare($current_version, $latest_version, '<')) {
        $transient->response['aveonline-widget-guia/index.php'] = (object) [
            'new_version' => $latest_version,
            'package'     => $release->zipball_url,
            'slug'        => 'aveonline-widget-guia',
            'url'         => 'https://github.com/franciscoblancojn/aveonline-widget-guia',
        ];
    }

    return $transient;
}
add_filter('site_transient_update_plugins', 'AVWG_updater');
