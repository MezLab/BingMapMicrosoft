<?php

/**
 * Plugin Name: WPackage - Bing Maps Microsoft
 * Version: 1.0.0
 * Author: Mez {Massimo Maestri} 
 * Author URI: https://www.massimomaestri.com
 */


/** L'utente non potra accedere al file php */
if (!defined('ABSPATH')) exit;

function microsoft_maps_bings($atts){

  $blockContent;

  $a = shortcode_atts( array(
    "style" => "button", //slider o button
    "maps" => "",
  ), $atts);

  

  switch ($a['style']) {  
    case 'button':
      $blockContent = '<div class="button"><section class="section"><div class="container" id="cont"></div></section></div></section>';
      break;
    case 'slider':
      $blockContent = '<div class="slider"><div class="freccia" onclick="scorri(\'L\')" ontouchstart="scorri(\'L\')"><div class="arrow sx"></div></div><section class="section"><div class="container" id="cont"></div></section><div class="freccia" onclick="scorri(\'R\')" ontouchstart="scorri(\'R\')"><div class="arrow dx"></div></div></div></section><script src="https://[LINK-URL]wp-content/plugins/bing_wpackage/admin/wpack-maps/../library/js/slider.js"></script>';

    default:
      break;
  }

  return '
  <section style="position:relative;" class="wrapper_">
    <div class="overlay_">
      <div class="cerchio"></div>
    </div>
    <div id="mappa"></div>
    ' . $blockContent . '
  <script src="https://[LINK-URL]wp-content/plugins/bing_wpackage/admin/wpack-maps/../library/js/bingMaps.js"></script>
  <script>
    function back(){
      carica("https://[LINK-URL]wp-content/plugins/bing_wpackage/admin/", "'. $a['maps'] .'", "'. $a['style'] .'");
      document.querySelector(".overlay_").classList.add("scompari");
      setTimeout(() => {
        document.querySelector(".overlay_").style.display = "none";
      }, 1500);
    }
    </script><script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?key=[CODE-API]&callback=back"></script>';
}

add_shortcode("microsoft_maps", "microsoft_maps_bings");