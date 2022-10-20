<?php

/**
 * Plugin Name: WPackage - Bing Maps Microsoft
 * Version: 1.0.0
 * Author: Mez {Massimo Maestri} 
 * Author URI: https://www.massimomaestri.com
 */

define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * L'utente non potra accedere al file php
 */
if(!defined('ABSPATH')){
    echo "Hello Friend, whats up?";
    exit;
}

class WPackage{

    function __construct(){
        add_action( 'admin_menu', array( $this , 'wpackage_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this , 'wpackage_all_styles'), 20 );
    }

    public function wpackage_menu()
    {
        add_menu_page(__('Bing WPackage', 'bing_wpackage'), __('WPackage bing', 'bing_wpackage'), 'manage_options', 'unwpackage', array($this, 'wpackage'), plugin_dir_url(__FILE__) . 'admin/media/img/cube_min.png', 30);

        add_submenu_page('unwpackage', __('Aggiornamento Mappa', 'bing_wpackage'), __('Aggiornamento Mappa', 'bing_wpackage'), 'manage_options', 'wpackage_update', array($this, 'wpackage_update'));

        add_submenu_page('unwpackage', __('Microsoft Bing Maps', 'bing_wpackage'), __('Microsoft Bing Maps', 'bing_wpackage'), 'manage_options', 'wpackage_maps', array($this, 'wpackage_maps'));

        add_submenu_page('wpackage_maps', __('Impostazioni Mappe', 'bing_wpackage'), __('Impostazioni Mappe', 'bing_wpackage'), 'manage_options', 'settingMaps', array($this, 'wpackage_settingMaps'));

        add_submenu_page('wpackage_maps', __('Ultime Modifiche', 'bing_wpackage'), __('Ultime modifiche', 'bing_wpackage'), 'manage_options', 'wpackage_lastAlter', array($this, 'wpackage_lastAlter'));
    }

    public function wpackage(){
        require_once(MY_PLUGIN_PATH . 'admin/wpack_admin.php');
    }

    // Shortcode
    public function wpackage_shortcodeMaps(){
        require_once(MY_PLUGIN_PATH . 'admin/wpack_shortcodeMaps.php');
    }

    // Update Website
    public function wpackage_update(){
        require_once(MY_PLUGIN_PATH . 'admin/wpack-update/wpack_update.php');
    }

    // Bing Maps Microsoft
    public function wpackage_maps(){
        require_once(MY_PLUGIN_PATH . 'admin/wpack-maps/wpack_bingMaps.php');
    }
    public function wpackage_settingMaps(){
        require_once(MY_PLUGIN_PATH . 'admin/wpack-maps/wpack_settingMaps.php');
    }

    // CSS Style Plugin
    public function wpackage_all_styles(){
        wp_enqueue_style('bing_wpackage_css', plugins_url('admin/library/css/bing_wpackage.css', __FILE__) );
        wp_enqueue_script( 'bing_wpackage_js', plugins_url('admin/library/js/bing_wpackage.js', __FILE__)  );
    }
}

$wpck = new WPackage();
$wpck->wpackage_shortcodeMaps();
$wpck->wpackage_shortcodeDate();

?>	