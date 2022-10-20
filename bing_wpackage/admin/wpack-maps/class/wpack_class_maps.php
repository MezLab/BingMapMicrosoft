<?php

/**
 * Plugin Name: WPackage - Bing Maps Microsoft
 * Version: 1.0.0
 * Author: Mez {Massimo Maestri} 
 * Author URI: https://www.massimomaestri.com
 */


class WPACK_bing_maps{

    /** Array contenente le voci menu */
    public $Menu;

    /** Lista elementi coordinate punto/area/linea */
    public $category;
    public $title;
    public $description;
    public $coords;
    public $color;

    /**
     * Voci Menu Opzioni 
     */
    public function nav(){

        $this->Menu = array(
            0 => array(
                "Title" => "Nuova <b>Postazione</b>",
                "Data"  => "data=newPoint",
                "Btn"   => "Entra"
            ),
            1 => array(
                "Title" => "Elimina <b>Postazione</b>",
                "Data"  => "data=updatePoint",
                "Btn"   => "Entra"
            ),
            2 => array(
                "Title" => "Aggiungi <b>Nuova </b><br>Categoria",
                "Data"  => "data=newCategory",
                "Btn"   => "Entra"
            ),
            3 => array(
                "Title" => "Aggiungi <b> Categoria</b><br> Esistente",
                "Data"  => "data=existCategory",
                "Btn"   => "Entra"
            ),
            4 => array(
                "Title" => "Elimina <b>Categoria</b>",
                "Data"  => "data=deleteCategory",
                "Btn"   => "Entra"
            )
        );

        return $this->Menu;
    }

    public function fivePoint($category, $title, $description, $coords, $color){
        $this->category = $category;
        $this->title = $title;
        $this->description = $description;
        $this->coords = $coords;
        $this->color = $color;
    }

    public function GeoJSON($print, $fields, $temp, $res_1 = null, $r = null, $g = null, $b = null){

        /** Apertura File Json */
        $start = '{"type":"FeatureCollection","features":';
        fwrite(fopen($print, 'w'), $start . "\n");

        $array = array(); // Nuovo Array

        for($i = 0; $i < count($fields); $i++){

            $geoJson = $fields[$i]["name_cat"];
            /**
            * Oggetto @var $bing
            * con le proprietà di tipo POINT
            */
            switch ($geoJson) {
                case 'point':
                    $bing = array(
                        "type" => "Feature",
                        "properties" => array(
                        "marker-color" => $fields[$i]["color"],
                        "marker-size" => "large",
                        "icon" => plugin_dir_url( __FILE__ ). "../media/marker/" . $res_1[0]["url_marker"],
                        "title" =>$fields[$i]["title"],
                        "subTitle" => $res_1[0]["name_file"],
                        "description" =>$fields[$i]["description"],
                        "visible" => false
                        ),
                        "geometry" => array(
                            "type" => "Point",
                            "coordinates" => array(
                                $fields[$i]["coords"]
                            )
                        )
                    );
                    break;
                case 'line':
                    $bing = array(
                        "type" => "Feature",
                        "properties" => array(
                            "strokeColor" => $fields[$i]["color"],
                            "fillColor" => $fields[$i]["color"],
                            "strokeThickness" => 3,
                            "strokeDashArray" => "[3,2]",
                            "title" => $fields[$i]["title"],
                            "subTitle" => $res_1[0]["name_file"],
                            "description" => $fields[$i]["description"],
                            "visible" => false
                        ),
                        "geometry" => array(
                            "type" => "LineString",
                            "coordinates" => array(
                                $fields[$i]["coords"]
                            )
                        )
                    );
                    break;
                case 'polygon':

                    /** Trasformazione HEX in RGB */
                    list($r, $g, $b) = sscanf($fields[$i]["color"], "#%02x%02x%02x");

                    $bing = array(
                        "type" => "Feature",
                        "properties" => array(
                            "strokeColor" => $fields[$i]["color"],
                            "strokeThickness" => 0,
                            "fillColor" => "rgba(" . $r .", " . $g . ", " . $b . ", 0.5)",
                            "title" => $fields[$i]["title"],
                            "subTitle" => $res_1[0]["name_file"],
                            "description" => $fields[$i]["description"],
                            "visible" => false
                        ),
                        "geometry" => array(
                            "type" => "Polygon",
                            "coordinates" => array(
                                array($fields[$i]["coords"])
                            )
                        )
                    );
                    break;
                default:
                    break;
            }

            /**
            * Apro il file temporaneo e stampo
            * sul file temp.txt
            * l'oggetto @var $bing con le coordinate
            * e le proprietà
            */
            fwrite(fopen($temp, 'a'), json_encode($bing) . "\n");

            /**
            * Inserisco le @var $bing come lista 
            * nell @var $array
            */
            $array[$i] = json_decode(file($temp)[$i]);

            fclose($temp);
                                
        }

        unlink($temp);
        /**
        * Stampo il file JSON
        * grazie alla lista creata in @var $array 
        */
        fwrite(fopen($print, 'a'), str_replace(array('["', '"]'), array('[', ']'), json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));

        /**
        * Chiusura File Json
        */
        $end = '}';
        fwrite(fopen($print, 'a'), $end . "\n");
        fclose($print);
    }
    

    /**
     * Aggiunge o elimina 
     * la categoria dal JSON 
     * principale della Mappa
     */
    public function CategoryMaps($idMappa, $mapName){
        global $wpdb;
        /** Seleziono tutti gli ID di categoria che corrispondono all'ID della mappa*/
        $ID_MAP =  $wpdb->get_results("select ID_Category from bing_maps_ID where ID_Map = '" . $idMappa . "';", ARRAY_A);

        $arrayIDCat = array();
        $array = array();

        for ($z=0; $z < count($ID_MAP) ; $z++) {
            $arrayIDCat[$z] = intVal($ID_MAP[$z]['ID_Category']);
        }

        for ($a=0; $a < count($arrayIDCat); $a++) {
            /** Inserimento nel file JSON del'ID Mappato nella directory Risorse  */
            $pos = $arrayIDCat[$a];
            $catSlt =  $wpdb->get_results("select * from bing_maps_category where ID=$pos ;" , ARRAY_A);
            /** Percorso del file JSON della mappa che conterrà tutte le categorie inserite */
            $json_Map = RISORSE . $mapName[0]['maps_name'] . ".json";

            // Nuovo Array di inserimento info principali della categoria
            

            // Oggetto ARRAY PHP per la codifica in json

            // $objson->Maps();
                $Obj = array(
                    "NomeFile" => $catSlt[0]['name_file'] . ".json",
                    "Titolo" => $catSlt[0]['title'],
                    "Icona" => $catSlt[0]['url_icon'],
                    "Marker" => $catSlt[0]['url_marker'],
                    "Color" =>  $catSlt[0]['color'],
                );

                $array[$a] = $Obj;
        }

        // Stampo sul file json principale della mappa
        fwrite(fopen($json_Map, 'w'), json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    }
    

    public function zipFileMapsJson(){
        // Crea lo zip file
        $zipFile = "../file/Json-Bing-Maps.zip";
        touch($zipFile);

        // Apre il file zip
        $zip = new ZipArchive();
        $myZip = $zip->open($zipFile);

        if($myZip){
            $folderFile = opendir("../file/geojson");
            if($folderFile){
                while($json = readdir($folderFile)){
                    $file_json = "../file/geojson" . $json;
                    $zip->addFile($file_json);
                }
            }
            closedir($folderFile);
        }

        if(file_exists($zipFile)){
            $demo_name = "Json-Bing-Maps.zip";
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="'. $demo_name .'"');

            readfile($zipFile); // Download
            unlink($zipFile);
        }

    }
}

$WPMJ = new WPACK_bing_maps();
