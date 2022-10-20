<?php

/**
 * Plugin Name: WPackage - Bing Maps Microsoft
 * Version: 1.0.0
 * Author: Mez {Massimo Maestri} 
 * Author URI: https://www.massimomaestri.com
 */


/**
 * L'utente non potra accedere al file php
 */
if (!defined('ABSPATH')) exit;

require_once(MY_PLUGIN_PATH . 'admin/wpack_function.php');

?>

<?php my_plugin_header(); ?>

<section class="home">
    <h1 class="titlePage">Ciao <b><?php echo wp_get_current_user()->user_nicename; ?></b>,</h1>
    <h3 class="subTitle"><b>Servizio</b> Microsoft Bing Maps</b>.</h3>
</section>
<div class="wpack_box">
    <div class="box">
        <h3><b>Bing Map</b> Microsoft</h3>
        <a href="?page=wpackage_maps" ?>Entra</a>
    </div>
</div>

<?php my_plugin_footer(); ?>