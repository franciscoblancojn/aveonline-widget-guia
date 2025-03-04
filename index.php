<?php
/**
 * @package WoocommerceAveOnlineShipping
 */
/*
Plugin Name: Aveonline widget guia
Plugin URI: https://gitlab.com/franciscoblancojn/aveonline-widget-guia
Description: Widget para mostrar guias de aveonline en elementor.
Version: 1.0.0
Author: franciscoblancojn
Author URI: https://franciscoblanco.vercel.app/
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wc-aveonline-shipping
*/

if (!function_exists( 'is_plugin_active' ))
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

if(is_admin()){
    require 'plugin-update-checker/plugin-update-checker.php';
    $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
        'https://github.com/franciscoblancojn/aveonline-widget-guia',
        __FILE__,
        'aveonline-widget-guia'
    );
    $myUpdateChecker->setAuthentication('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');
    $myUpdateChecker->setBranch('master');
}
//AVWG_
define("AVWG_KEY",'AVWG');
define("AVWG_LOG",false);
define("AVWG_DIR",plugin_dir_path( __FILE__ ));
define("AVWG_URL",plugin_dir_url(__FILE__));
