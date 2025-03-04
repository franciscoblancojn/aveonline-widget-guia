<?php
/**
 * @package WoocommerceAveOnlineShipping
 */
/*
Plugin Name: Aveonline widget guia
Plugin URI: https://gitlab.com/franciscoblancojn/aveonline-widget-guia
Description: Widget para mostrar guias de aveonline en elementor.
Version: 1.0.1
Author: franciscoblancojn
Author URI: https://franciscoblanco.vercel.app/
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wc-aveonline-shipping
*/

if (!function_exists( 'is_plugin_active' ))
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

//AVWG_
define("AVWG_KEY",'AVWG');
define("AVWG_LOG",false);
define("AVWG_DIR",plugin_dir_path( __FILE__ ));
define("AVWG_URL",plugin_dir_url(__FILE__));

if(is_admin()){
    // require_once plugin_dir_path( __FILE__ ) . 'update.php';
}
require_once AVWG_DIR . 'src/component/_.php';

function AVWG_register_AveFormGuias($widgets_manager) {
    require_once AVWG_DIR . 'src/widget.php';
    $widgets_manager->register(new \Elementor\AVWG_AveFormGuias());
}
add_action('elementor/widgets/register', 'AVWG_register_AveFormGuias');

function AVWG_register_AveFormGuias_load() {
    if (!did_action('elementor/loaded')) {
        return;
    }
}
add_action('plugins_loaded', 'AVWG_register_AveFormGuias_load');