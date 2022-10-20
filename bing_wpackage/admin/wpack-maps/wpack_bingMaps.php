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
// if (!defined('ABSPATH')) exit;
global $wpdb;
$res = NULL;
$s = "";

/** $_GET che visualizza
 * la scelta dell'utente
 */
$data = $_GET["data"];
/** ---------------------*/

require_once(MY_PLUGIN_PATH . 'admin/wpack_function.php');
?>

<?php my_plugin_header(); ?>

<section class="home">
    <h1 class="titlePage">Microsoft Bing Maps</h1>

    <?php
    /**
     * Controllo che le tabelle 
     * sul database siano state 
     * correttamente installate
     */
    ?>

    <?php
    $apiBingMaps = $wpdb->get_results("select token from bing_api");
    $fileDB = MY_PLUGIN_PATH . 'admin/wpack-maps/db.txt';
    switch ($_GET["db"]) {
            // Crea Tabella
        case 'create':
            include_once(MY_PLUGIN_PATH . 'admin/wpack-maps/include/wpack_dbMaps.php');
            fwrite(fopen($fileDB, 'w'), '1');
            fclose($fileDB);
            break;
            // Memorizzo Api
        case 'api':
            $wpdb->insert("bing_api", array("token" => $_POST['api']));
            break;
            // Aggiungi Mappa
        case 'addMap':
            // $apiBingMaps = "Ao4EAuMEj_NObd3zTCqDOPM2gwsqoAmBi4VCLKxH5KW1rC5Hwga_r0IHrCSufd8S";
            $file_shortcode = MY_PLUGIN_PATH . "admin/wpack_shortcode.php";

            if (file_exists($file_shortcode) && $apiBingMaps[0]->token != NULL) {

                $newString = $_POST['nameMap'];
                $newString = str_replace(' ', '', $newString);
                $wpdb->insert("bing_maps", array("maps_name" => $_POST['nameMap'], "maps_shortcode" => "[microsoft_maps style='" . $_POST['style'] . "' maps='" . strtolower($newString) . "]"));
                /**
                 * Crea il file in risorse
                 * per la creazione delle categorie
                 * all'interno della Mappa selezionata
                 */

                $risorse = MY_PLUGIN_PATH . "admin/wpack-maps/file/risorse/" . $_POST['nameMap'] . ".json";
                touch($risorse);
            } else {
                echo "<div class='bad'><h1 align='center'>Attenzione - API non inserito</h1></div>";
            }
            break;
        default:
            break;
    }
    ?>

    <?php if (!file_exists($fileDB)) { ?>

        <div class="description_code">
            <p class="subTitle"><b>Aggiungi</b> le <b>tabelle</b> al database.</p>
            <a href="?page=wpackage_maps&db=create" class="btn">Aggiungi</a>
        </div>

    <?php } else { ?>

        <div class="description_code">
            <p class="subTitle">Benvenuto nella creazione della tua mappa personalizzata.</p>
            <p class="subTitle"><b>Copia</b> e <b>inserisci</b> lo <em>shortcode</em> nella <b>pagina/sezione</b> che preferisci per visualizzare la mappa.</p>
            <button class="btn" onclick="openBox('addMap');">Aggiungi Mappa</button>
            <div id="addMap" class="esterno">
                <form action="?page=wpackage_maps&db=addMap" method='POST'>
                    <h1 align="center">Inserisci il nome della Mappa</h1>
                    <input class='riga' type="text" name="nameMap" required>
                    <label style='color:#ff8091;'>Scegli lo Stile di visualizzazione</label>
                    <div>
                        <input type="radio" id="slider" name="style" value="slider">
                        <label style="padding:0;color:#fff;" for="slider"> Slider</label><br>
                        <input type="radio" id="button" name="style" value="button">
                        <label style="padding:0;color:#fff;" for="button"> Classic Button</label><br>
                    </div>
                    <input class='riga' type="submit" value="Aggiungi">
                    <div class='menu_bot'><a href='?page=wpackage_maps'>Chiudi</a><input type='reset' value='Reset'></div>
                </form>
            </div>
        </div>
        <div style="margin-top:20px;" class="description_code">
            <p class="subTitle">Inserisci API Microsoft Bing</p>
            <div id="api">
                <form style="display:flex;align-items:middle;" action="?page=wpackage_maps&db=api" method='POST'>
                    <input style="width:450px;" class='riga' type="password" name="api" value="<?php echo $apiBingMaps[0]->token ?>" required>
                    <input class='riga' type="submit" value="Inserisci">
                </form>
            </div>
        </div>

        <?php
        /** Qui dovrà essere inserito
         *  un ciclo while che mostra
         *  i shortcode creati */
        $risultato = "";
        $res = $wpdb->get_results("select * from bing_maps;", ARRAY_A);
        for ($i = 0; $i < count($res); $i++) {
            $risultato .= "
             <tr>
                <td>
                    <p class='settingSingleMaps'>
                        <a href='?page=settingMaps&idMap=" . $res[$i]["ID"] . "'><i class='fa fa-pencil3 fa-2x fa-fw'></i></a>
                    </p>
                </td>
                <td>
                    <p class='nameMaps'>" . $res[$i]["maps_name"] . "</p>
                </td>
                <td>
                    <p class='byFlex'>
                        <i data-class='copy' class='fa fa-copy fa-2x fa-fw'>
                            <span class='shortcode'>" . $res[$i]["maps_shortcode"] . "</span>
                        </i>
                    </p>
                </td>
            <tr>";
        }

        ?>

        <table class="shortBox">
            <tr>
                <th></th>
                <th>Nome</th>
                <th>Shortcode</th>
            </tr>
            <?php echo $risultato; ?>
        </table>
    <?php } ?>
</section>

<?php my_plugin_footer(); ?>