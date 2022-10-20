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
global $wpdb;
$res = NULL;
$s = "";

/** $_GET che visualizza
 * la scelta dell'utente
 */
$data = $_GET["data"];
$geoJson = $_GET["geo"];
$idMappa = $_GET["idMap"];
$idMappaInt = intval($idMappa);
/** ---------------------*/

/** 
 * Funzione che riporta lo shortcode
 * grazie al campo ID
 */
$code = $wpdb->get_results("select maps_shortcode from bing_maps where ID=$idMappaInt");
/** ---------------------*/

require_once(MY_PLUGIN_PATH . 'admin/wpack_function.php');
require_once(MY_PLUGIN_PATH . 'admin/wpack-maps/include/wpack_path.php');
require(MY_PLUGIN_PATH . 'admin/wpack-maps/class/wpack_class_maps.php');
?>

<?php my_plugin_header(); ?>

<section class="home">
    <h1 class="titlePage">Microsoft Bing Maps</h1>
    <div class="description_code">
        <p class="subTitle"><b>Copia</b> e <b>inserisci</b> lo <em>shortcode</em> nella <b>pagina/sezione</b> che preferisci per visualizzare la mappa.</p>
    </div>
    <!-- Qui dovrà essere inserito un ciclo while che mostra i shortcode creati -->
    <p class='byFlex'><i data-class='copy' class='fa fa-copy fa-2x fa-fw'><span class='shortcode'><?php echo $code[0]->maps_shortcode ?></span></i></p>
    <div class="description_code">
        <h2>Menu Opzioni</h2>
        <p class="subTitle">Scegli una delle opzioni presenti</p>
    </div>
    <div class="wpack_box bingMaps">
        <?php for ($i=0; $i < count($WPMJ->nav()); $i++) { ?>
            <div class="box">
                <h3><?php echo $WPMJ->nav()[$i]["Title"]; ?></h3>
                <a href="?page=settingMaps&idMap=<?php echo $idMappa?>&<?php echo $WPMJ->nav()[$i]["Data"]; ?>"?><?php echo $WPMJ->nav()[$i]["Btn"]; ?></a>
            </div>
        <?php } ?>
    </div>
    <?php 
        switch ($data) {
            case 'existCategory':
            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                $s = "";
                echo    "<div class='esterno'>
                            <form action='' method='post'>
                            <h1 align='center'>Scegli il file da aggiungere</h1>
                            <select class='riga' name='categoria' required>
                            <option value=''>Categoria</option>";


                $res = $wpdb->get_results("select ID, name_file, title from bing_maps_category; ", ARRAY_A);

                $arrayIDCat = array(
                    array(
                        "ID" => 0,
                        "name" => "",
                        "title" => ""

                    )
                );
                for ($i=0; $i < count($res); $i++) {
                    $arrayIDCat[$i]["ID"] = $res[$i]["ID"];
                    $arrayIDCat[$i]["name"] = $res[$i]["name_file"];
                    $arrayIDCat[$i]["title"] = $res[$i]["title"];
                }

                $res2 = $wpdb->get_results("select ID_Category from bing_maps_ID where ID_Map = $idMappa; ", ARRAY_A);

                for($a = 0; $a < count($arrayIDCat); $a++){
                    $boolean = false;

                    for ($i = 0; $i < count($res2); $i++) {

                        if($arrayIDCat[$a]["ID"] == $res2[$i]["ID_Category"]){
                            $boolean = true;
                        }    
                    }

                    if(!$boolean){
                        $s .= "<option value='" . $arrayIDCat[$a]["name"] . "'>" . $arrayIDCat[$a]["title"] . "</option>\n";
                    }
                }

                echo $s . "</select>
                    <input class='riga' type='submit' value='Aggiungi file'>
                    <div class='menu_bot'><a href='?page=settingMaps&idMap=$idMappa'>Chiudi</a><input type='reset' value='Reset'>
                    </form>";
            } else {
                // Nome Categoria 
                $name_title = $_POST['categoria'];

                /** Seleziono il nome della mappa su cui sto creando al categoria */
                $mapName =  $wpdb->get_results("select maps_name from bing_maps where ID=$idMappa ;", ARRAY_A);

                $selectIDCat = $wpdb->get_results("select ID from bing_maps_category where name_file = '" . $name_title . "';", ARRAY_A);

                $associazioneID = $wpdb->insert("bing_maps_ID", array("ID_Category" => intval($selectIDCat[0]["ID"]) , "ID_Map" => $idMappa));

                $WPMJ->CategoryMaps($idMappa, $mapName);
                echo "<div class='good'><h1 align='center'>Categoria aggiunta</h1></div>";
            }

            break;
            /**
             * -.-.-.-.-.-.-.-
             * COORDINATE
             * Inserimento delle coordinate
             * {implementation} @author Lorenzo Sartori
             * {update} @author Massimo Maestri
             * -.-.-.-.-.-.-.-
             */
            case 'newPoint':
                $res = $wpdb->get_results("select * from bing_maps_category;", ARRAY_A);
                for($i = 0; $i < count($res); $i++){
                    $s .= "<option value='" . $res[$i]["name_file"] . "'>" . $res[$i]["title"] . "</option>\n";
                }
                //echo $_SERVER['REQUEST_METHOD'];
                if($_SERVER['REQUEST_METHOD'] != 'POST' && $geoJson == NULL){
                            echo "<div class='esterno'>
                            <div class='container'>
                            <h1 align='center'>Scegli cosa inserire</h1>
                                <a class='linkGeoJson' href='?page=settingMaps&idMap=$idMappa&data=newPoint&geo=point''>Punto Cardinale</a>
                                <a class='linkGeoJson' href='?page=settingMaps&idMap=$idMappa&data=newPoint&geo=line''>Linea</a>
                                <a class='linkGeoJson' href='?page=settingMaps&idMap=$idMappa&data=newPoint&geo=polygon''>Area</a>
                            </div> 
                            </div>";
                }else{
                    switch ($geoJson) {
                        case 'point':
                        if($_POST["categoria"] == NULL){
                            echo "<div class='esterno'>
                                <form method='POST' action=''>
                                <a class='linkGeoJson' href='https://geojson.io/#map=13/44.7949/-349.6819' target='_blank'>Inserisci il punto</a>
                                <label style='color:#fff;padding:0;'>Scegli la categoria</label>
                                <select class='riga' name='categoria' required>
                                    <option value='' disabled selected>Categoria</option>" . $s . "
                                </select>
                                <input class='riga' name='title' type='text' value='' placeholder='Titolo' required>
                                <input class='riga' name='description' type='text' value='' placeholder='Descrizione' required>
                                <textarea name='coords' id='' cols='30' rows='10' placeholder='Inserisci le coordinate'></textarea>
                                <input class='riga' type='button' value='Elimina Spazi' onclick='cleanCoords();'>
                                <input class='riga' type='submit' value='Aggiorna File'>
                                <div class='menu_bot'><a href='?page=settingMaps&idMap=$idMappa'>Chiudi</a><input type='reset' value='Reset'></div>
                                </form>
                                </div>";
                        }else{

                            $WPMJ->fivePoint($_POST["categoria"], $_POST["title"], $_POST["description"], $_POST["coords"], $_POST["colore"]);

                            $res_1 = $wpdb->get_results("select name_file, url_marker, color from bing_maps_category where name_file = '" . $WPMJ->category . "';", ARRAY_A);

                            $idCAT = $wpdb->get_results("select ID from bing_maps_category where name_file = '" . $WPMJ->category . "';", ARRAY_A);

                            $wpdb->insert("bing_maps_point", array(
                                "title" => $WPMJ->title,
                                "description" => $WPMJ->description,
                                "coords" => $WPMJ->coords,
                                "color" => $res_1[0]["color"],
                                "id_cat" => intval($idCAT[0]["ID"]),
                                "name_cat" => $geoJson
                            ));

                            $fields = $wpdb->get_results("select * from bing_maps_point where id_cat = '" . intval($idCAT[0]["ID"]) . "';", ARRAY_A);

                            $print = GEOJSON . $WPMJ->category . ".json"; // File Json richiesto per la stampa
                            $temp = GEOJSON . "temp.txt"; // File TXT Temporaneo per la creazione del JSON

                            $WPMJ->GeoJSON($print, $fields, $temp, $res_1);

                            
                        }
                        break;
                        case 'line' :
                        if($_POST["categoria"] == NULL){
                            echo "<div class='esterno'>
                                <form method='POST' action=''>
                                <a class='linkGeoJson' href='https://geojson.io/#map=13/44.7949/-349.6819' target='_blank'>Inserisci la linea</a>
                                <label style='color:#fff;padding:0;'>Scegli la categoria</label>
                                <select class='riga' name='categoria' required>
                                    <option value='' disabled selected>Categoria</option>" . $s . "
                                </select>
                                <input class='riga' name='title' type='text' value='' placeholder='Titolo' required>
                                <input class='riga' name='description' type='text' value='' placeholder='Descrizione' required>
                                <textarea name='coords' id='' cols='30' rows='10' placeholder='Inserisci le coordinate'></textarea>
                                <input class='riga' type='button' value='Elimina Spazi' onclick='cleanCoords();'>
                                <label style='color:#fff;padding:0;'>Scegli il colore dell'area</label>
                                <div class='catColor'>
                                    <input class='riga' name='colore' type='color' required>
                                    <input style='text-align:center;' class='riga' type='text' name='esadecimale' placeholder='#000000'>
                                    <p class='btn_maps'>Inserisci</p>
                                </div>
                                <input class='riga' type='submit' value='Aggiorna File'>
                                <div class='menu_bot'><a href='?page=settingMaps&idMap=$idMappa'>Chiudi</a><input type='reset' value='Reset'></div>
                                </form>
                                </div>";
                            }else{

                                $WPMJ->fivePoint($_POST["categoria"],$_POST["title"],$_POST["description"],$_POST["coords"],$_POST["colore"]);

                                $res_1 = $wpdb->get_results("select name_file, url_marker, color from bing_maps_category where name_file = '" . $WPMJ->category . "';", ARRAY_A);

                                $idCAT = $wpdb->get_results("select ID from bing_maps_category where name_file = '" . $WPMJ->category . "';", ARRAY_A);

                                $wpdb->insert("bing_maps_point", array(
                                    "title" => $WPMJ->title,
                                    "description" => $WPMJ->description,
                                    "coords" => $WPMJ->coords,
                                    "color" => $WPMJ->color,
                                    "id_cat" => intval($idCAT[0]["ID"]),
                                    "name_cat" => $geoJson
                                ));

                                $fields = $wpdb->get_results("select * from bing_maps_point where id_cat = '" . intval($idCAT[0]["ID"]) . "';", ARRAY_A);

                                $print = GEOJSON . $WPMJ->category . ".json"; // File Json richiesto per la stampa
                                $temp = GEOJSON . "temp.txt"; // File TXT Temporaneo per la creazione del JSON

                                $WPMJ->GeoJSON($print, $fields, $temp, $res_1);
                                
                            }                       
                        break;
                        case 'polygon' :
                        if($_POST["categoria"] == NULL){
                            echo "<div class='esterno'>
                                <form method='POST' action=''>
                                <a class='linkGeoJson' href='https://geojson.io/#map=13/44.7949/-349.6819' target='_blank'>Inserisci l'area</a>
                                <label style='color:#fff;padding:0;'>Scegli la categoria</label>
                                <select class='riga' name='categoria' required>
                                    <option value='' disabled selected>Categoria</option>" . $s . "
                                </select>
                                <input class='riga' name='title' type='text' value='' placeholder='Titolo' required>
                                <input class='riga' name='description' type='text' value='' placeholder='Descrizione' required>
                                <textarea name='coords' id='' cols='30' rows='10' placeholder='Inserisci le coordinate'></textarea>
                                <input class='riga' type='button' value='Elimina Spazi' onclick='cleanCoords();'>
                                <label style='color:#fff;padding:0;'>Scegli il colore dell'area</label>
                                <div class='catColor'>
                                    <input class='riga' name='colore' type='color' required>
                                    <input style='text-align:center;' class='riga' type='text' name='esadecimale' placeholder='#000000'>
                                    <p class='btn_maps'>Inserisci</p>
                                </div>
                                <input class='riga' type='submit' value='Aggiorna File'>
                                <div class='menu_bot'><a href='?page=settingMaps&idMap=$idMappa'>Chiudi</a><input type='reset' value='Reset'></div>
                                </form>
                                </div>";
                        }else{
                                $WPMJ->fivePoint($_POST["categoria"],$_POST["title"],$_POST["description"],$_POST["coords"],$_POST["colore"]);

                                $res_1 = $wpdb->get_results("select name_file, url_marker, color from bing_maps_category where name_file = '" . $WPMJ->category . "';", ARRAY_A);

                                $idCAT = $wpdb->get_results("select ID from bing_maps_category where name_file = '" . $WPMJ->category . "';", ARRAY_A);

                                $wpdb->insert("bing_maps_point", array(
                                    "title" => $WPMJ->title,
                                    "description" => $WPMJ->description,
                                    "coords" => $WPMJ->coords,
                                    "color" => $WPMJ->color,
                                    "id_cat" => intval($idCAT[0]["ID"]),
                                    "name_cat" => $geoJson
                                ));

                                $fields = $wpdb->get_results("select * from bing_maps_point where id_cat = '" . intval($idCAT[0]["ID"]) . "';", ARRAY_A);

                                $print = GEOJSON . $WPMJ->category . ".json"; // File Json richiesto per la stampa
                                $temp = GEOJSON . "temp.txt"; // File TXT Temporaneo per la creazione del JSON

                                $WPMJ->GeoJSON($print, $fields, $temp, $res_1, $r, $g, $b);

                        }
                        break;
                        default:
                        # code...
                        break;
                    }
                }                   
            break;
            /**
             * -.-.-.-.-.-.-.-
             * NUOVA CATEGORIA
             * Inserimento della categoria
             * Tabella CATEGORIA
             * {implementation} @author Lorenzo Sartori
             * {update} @author Massimo Maestri
             * -.-.-.-.-.-.-.-
             */
            case 'newCategory':
                if($_SERVER['REQUEST_METHOD'] != 'POST'){
                    echo "<div class='esterno'>
                        <form action='' method='POST' enctype='multipart/form-data'>
                        <h1 align='center'>Crea una nuova categoria</h1>
                        <input type='text' class='riga' name='categoria' placeholder='Categoria' required>
                        <label style='color:#fff;padding:0;'>Inserisci l'icona</label>
                        <input id='file-upload' class='riga' name='icona' type='file' accept='image/png' required>
                        <label style='color:#fff;padding:0;'>Inserisci Marker</label>
                        <input id='marker-upload' class='riga' name='marker' type='file' accept='image/png' required>
                        <label style='color:#fff;padding:0;'>Scegli il colore</label>
                        <div class='catColor'>
                            <input class='riga' name='colore' type='color' required>
                            <input style='text-align:center;' class='riga' type='text' name='esadecimale' placeholder='#000000'>
                            <p class='btn_maps'>Inserisci</p>
                        </div>
                        <input type='submit' class='riga' value='crea categoria'>
                        <div class='menu_bot'><a href='?page=settingMaps&idMap=" . $idMappa . "'>Chiudi</a><input type='reset' value='Reset'></div>
                        </div>";
                }else{
                    /** @var boolena */
                    $bool = false;

                    /** Seleziono tutti i titoli esistenti nella tabella categorie */
                    $ctrlCat = $wpdb->get_results("select title from bing_maps_category;", ARRAY_A);

                    /** Seleziono il nome della mappa su cui sto creando al categoria */
                    $mapName =  $wpdb->get_results("select maps_name from bing_maps where ID=$idMappaInt ;", ARRAY_A);

                    /**
                     * Se il controllo della categoria risulta NULL la @var bool diventa true,
                     * altrimenti in modo ciclico controllo se la nuova categoria inserita
                     * non sia già presente nell'elenco, se la categoria risulta presente
                     * stampo su stream di output il messaggio di errore e la @var bool
                     * rimane false, se non fosse presente processo la richiesta.
                     */
                    if(empty($ctrlCat)){
                        $bool = true;
                    }else{
                        for($i = 0; $i < count($ctrlCat); $i++){
                            if($ctrlCat[$i]["title"] == $_POST["categoria"]){
                                echo "<div class='bad'><h1 align='center'>Questa categoria è già presente</h1></div>";
                                $bool = false;
                            }else{
                                $bool = true;
                            }
                        }
                    }

                    /** Inserimento categoria */
                    if($bool){

                        // Nome Categoria 
                        $name_title = $_POST['categoria'];

                        // Nome Categoria senza backspace, il risultato verrà utilizzato per il nome del file json
                        $name_title = str_replace(' ', '', $name_title);
                        $name_file = GEOJSON . $name_title . ".json";

                        /**
                         * Controllo sull'esistenza del file json
                         * se esistente stampa l'opportuno messaggio di errore
                         */
                        if(file_exists($name_file)){
                            echo "<div class='bad'><h1 align='center'>Esiste gia' un file di questa categoria</h1></div>";
                        }else{
                            
                            /** Crea il file JSON nella cartella GEOJSON e restituisce il messaggio di avvenuta creazione */
                            touch($name_file);
                            echo "<div class='good'><h1 align='center'>Il file e' stato creato</h1></div>";

                            /** Funzione sul caricamento dei file immagine essere nelle directory prefissate */
                            function createSymbol($name, $directory){

                                //percorso della cartella dove mettere i file caricati dagli utenti
                                $directory = MEDIA . $directory .'/';

                                //Recupero il percorso temporaneo del file
                                $iconaTemp = $_FILES[$name]['tmp_name'];

                                //recupero il nome originale del file caricato
                                $icona_name = $_FILES[$name]['name'];

                                //copio il file dalla sua posizione temporanea alla mia cartella upload
                                if(file_exists($directory . $icona_name)){
                                    echo "<div class='bad'><h1 align='center'>Esiste gia' questa icona</h1></div>";
                                }else{
                                    if(move_uploaded_file($iconaTemp, $directory . $icona_name)){
                                        echo "<div class='good'><h1 align='center'>Icona inserita correttamente</h1></div>";
                                    }else{
                                        echo "<div class='bad'><h1 align='center'>OPS!! icona non inserita</h1></div>";
                                    }
                                }
                            }

                            // upload dei file immagine
                            createSymbol('icona', 'icon');
                            createSymbol('marker', 'marker');
                        }

                        /** Inserimento nel Database della categoria */
                        $risultato = $wpdb->insert("bing_maps_category", array("title" => $_POST["categoria"], "name_file" => $name_title, "url_icon" => $_FILES['icona']['name'], "url_marker" => $_FILES['marker']['name'], "color" => $_POST["esadecimale"]));

                        $selectIDCat = $wpdb->get_results("select ID from bing_maps_category where name_file = '" . $name_title . "';", ARRAY_A);

                        $associazioneID = $wpdb->insert("bing_maps_ID", array("ID_Category" => intval($selectIDCat[0]["ID"]) , "ID_Map" => $idMappa));

                        $WPMJ->CategoryMaps($idMappa, $mapName);
                        echo "<div class='good'><h1 align='center'>Categoria inserita</h1></div>";

                    }else{
                        echo "<div class='bad'><h1 align='center'>Categoria non inserita</h1></div>";
                    }
                    
                }
            break;
            /**
             * -.-.-.-.-.-.-.-
             * ELIMINA CATEGORIA
             * Cancellazione della categoria
             * {implementation} @author Lorenzo Sartori
             * {update} @author Massimo Maestri
             * -.-.-.-.-.-.-.-
             */
            case "deleteCategory":
                if($_SERVER["REQUEST_METHOD"] != "POST"){
                    $s = "";
                    echo    "<div class='esterno'>
                            <form action='' method='post'>
                            <h1 align='center'>Scegli il file da eliminare</h1>
                            <select class='riga' name='categoria' required>
                            <option value=''>Categoria</option>";

                    $res = $wpdb->get_results("select * from bing_maps_category;", ARRAY_A);
                    for($i = 0; $i < count($res); $i++){
                        $s .= "<option value='" . $res[$i]["name_file"] . "'>" . $res[$i]["title"] . "</option>\n";
                    }

                    echo $s . "</select>
                    <input class='riga' type='submit' value='Elimina file'>
                    <div class='menu_bot'><a href='?page=settingMaps&idMap=$idMappa'>Chiudi</a><input type='reset' value='Reset'>
                    </form>";
                }else{

                    /** Seleziono il nome della mappa su cui sto creando al categoria */
                    $mapName =  $wpdb->get_results("select maps_name from bing_maps where ID=$idMappaInt ;", ARRAY_A);

                    // Nome Categoria 
                    $categoria = $_POST["categoria"];

                    // Seleziono nome del file, icona e marker della categoria che voglio eliminare                        
                    $res = $wpdb->get_results("select ID, name_file, url_icon, url_marker from bing_maps_category where name_file = '" . $categoria . "';", ARRAY_A);

                    // File Json della categoria selezionata
                    $name_json = GEOJSON . $res[0]["name_file"] . ".json";

                    // Icona della categoria selezionata
                    $name_img = ICON . $res[0]["url_icon"];

                    // Marker della categoria selezionata
                    $name_marker = MARKER . $res[0]["url_marker"];


                    /** Elimina i file nelle directory dal server */
                    unlink($name_json);
                    unlink($name_img);
                    unlink($name_marker);

                    /**
                     * Elimina il file dal database e dal filesystem
                     * stampando su stream di output l'opportuno messaggio  
                     */
                    if($wpdb->delete("bing_maps_category", array("name_file" => $categoria))){
                        $wpdb->delete("bing_maps_point", array("id_cat" => intval($res[0]["ID"])));
                        $wpdb->delete("bing_maps_ID", array("ID_Category" => intval($res[0]["ID"])));
                        echo "<div class='good'><h1 align='center'>Il file e' stato eliminato</h1></div>";
                    }else{
                        echo "<div class='bad'><h1 align='center'>Si e' verificato un errore durante l'eliminazione del file.</h1></div>";
                    }
                    $WPMJ->CategoryMaps($idMappa, $mapName);
            }
            break;
            case "updatePoint":
            $s = "";
            if($_SERVER["REQUEST_METHOD"] != "POST"){

                $res = $wpdb->get_results("select * from bing_maps_category;", ARRAY_A);
                for($i = 0; $i < count($res); $i++){
                    $s .= "<option value='" . $res[$i]["name_file"] . "'>" . $res[$i]["title"] . "</option>\n";
                }

                echo    "<div class='esterno'>
                        <form action='' method='POST' enctype='multipart/form-data'>
                        <h1 align='center'>Modifica un file</h1>
                        <select class='riga' name='categoria' required>
                        <option value='' disabled selected>Categoria</option>" . $s . "
                        </select>
                        <input type='submit' class='riga' value='Vedi risultati'>
                        <div class='menu_bot'><a href='?page=settingMaps&idMap=$idMappa'>Chiudi</a><input type='reset' value='Reset'>
                        </form>
                        </div>";
            }else{

                $categoria = $_POST["categoria"];
                $id = $wpdb->get_results("select ID from bing_maps_category where name_file = '" . $categoria . "';", ARRAY_A);
                $shape = $wpdb->get_results("select * from bing_maps_point where id_cat = '" . $id[0]["ID"] . "';", ARRAY_A);
                $intestazione = ["", "Titolo", "Descrizione"];
                $heading = "";
                $x = "";

                echo "<div class='esterno'>
                        <div class='tabellaFile'>
                            <div class='app'>
                            <h1 align='center'>Tabella Categoria [ " . $categoria . " ]</h1>
                                <form action='?page=settingMaps&idMap=$idMappa&data=myShape' method='POST' enctype='multipart/form-data'>
                                <select class='riga' name='choose' required>
                                    <option value=''>...</option>
                                    <!-- <option value='update'>Modifica</option> -->
                                    <!-- <option value='suspend'>Sospendi</option> -->
                                    <option value='delete'>Elimina</option>
                                </select>
                                    <table class='table'>
                                    <input type='hidden' name='isCategory' value='" . $categoria . "'>
                                        <tr>";
                                            for($i = 0; $i < count($intestazione); $i++){
                                                $heading .= "<th>" . $intestazione[$i] . "</th>";
                                            }
                                        echo $heading . "
                                        </tr>";
                                            for($i = 0; $i < count($shape); $i++){
                                                $x .= "<tr><td><input type='checkbox' name='row' value='" . $shape[$i]["ID"] . "' onclick='aggiornaBottone()'></td>" . "<td>" . $shape[$i]["title"] . "</td><td>" . $shape[$i]["description"] . "</td><tr>";
                                            }
                                            echo $x . "
                                    </table>
                                    <div class='menu_bot'><a href='?page=settingMaps&idMap=$idMappa'>Chiudi</a><input type='submit' class='riga' value='Procedi'></div>
                                </form>
                            </div>
                    </div>";
            }
            break;
            case "myShape":
                switch ($_POST["choose"]) {
                    case 'delete':
                        $id = $wpdb->delete("bing_maps_point", array("ID" => intval($_POST["row"])));

                        $res_1 = $wpdb->get_results("select ID, name_file, url_marker, color from bing_maps_category where name_file = '" . $_POST["isCategory"] . "';", ARRAY_A);
                        $fields = $wpdb->get_results("select * from bing_maps_point where id_cat = '" . intval($res_1[0]["ID"]) . "';", ARRAY_A);
                        $print = GEOJSON . $_POST["isCategory"] . ".json"; // File Json richiesto per la stampa
                        $temp = GEOJSON . "temp.txt"; // File TXT Temporaneo per la creazione del JSON
                        $WPMJ->GeoJSON($print, $fields, $temp, $res_1, $r, $g, $b);

                        break;
                    case 'update':
                        # code...
                        break;
                    case 'suspend':
                        # code...
                        break;
                    default:
                        # code...
                        break;
                }
            break;
            default:
            break;
        }
?>
