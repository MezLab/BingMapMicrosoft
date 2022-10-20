<?php

/**
 * Plugin Name: WPackage - Bing Maps Microsoft
 * Version: 1.0.0
 * Author: Mez {Massimo Maestri} 
 * Author URI: https://www.massimomaestri.com
 */


/** L'utente non potra accedere al file php */
if (!defined('ABSPATH')) exit;


/**
 * Funzione che richiama l'header
 */
function my_plugin_header($header = 'header'){
return include(MY_PLUGIN_PATH . 'admin/' . $header . '.php');
}

/**
 * Funzione che richiama il footer
 */
function my_plugin_footer($footer = 'footer'){
return include(MY_PLUGIN_PATH . 'admin/' . $footer . '.php');
}

?>