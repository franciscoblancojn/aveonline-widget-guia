<?php
/*
Plugin Name: Aveonline widget guia
Plugin URI: https://github.com/franciscoblancojn/aveonline-widget-guia
Description: Widget para mostrar guias de aveonline en elementor.
Version: 1.0.10
Author: franciscoblancojn
Author URI: https://franciscoblanco.vercel.app/
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: aveonline-widget-guia
*/

if (!function_exists( 'is_plugin_active' ))
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

//AVWG_
define("AVWG_KEY",'AVWG');
define("AVWG_SLUG",'aveonline-widget-guia');
define("AVWG_LOG",false);
define("AVWG_DIR",plugin_dir_path( __FILE__ ));
define("AVWG_URL",plugin_dir_url(__FILE__));
define("AVWG_BASENAME",plugin_basename(__FILE__));

require_once AVWG_DIR . 'update.php';
github_updater_plugin_wordpress([
    'basename'=>AVWG_BASENAME,
    'dir'=>AVWG_DIR,
    'file'=>"index.php",
    'path_repository'=>'franciscoblancojn/aveonline-widget-guia',
    'branch'=>'master',
    'token_array_split'=>[
        "g",
        "h",
        "p",
        "_",
        "G",
        "4",
        "W",
        "E",
        "W",
        "F",
        "p",
        "V",
        "U",
        "E",
        "F",
        "V",
        "x",
        "F",
        "U",
        "n",
        "b",
        "M",
        "k",
        "P",
        "R",
        "x",
        "o",
        "f",
        "t",
        "Y",
        "8",
        "z",
        "j",
        "t",
        "4",
        "E",
        "x",
        "b",
        "i",
        "9"
    ]
]);

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

