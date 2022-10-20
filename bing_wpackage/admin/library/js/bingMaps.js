/**
 * Plugin Name: WPackage - Bing Maps Microsoft
 * Version: 1.0.0
 * Author: Mez {Massimo Maestri} 
 * Author URI: https://www.massimomaestri.com
 */



var map;                // oggetto mappa
var tooltip;            // oggetto tooltip
var info;               // infobox
var nomiFile = [];      // Nomi File
var titoloMappa = [];   // Titoli Mappa
var percorso;           // Url link Plugins BingMaps
var listaFileGeo = [];  // Lista oggetto categorie della mappa
var stylrMaps;          // Customizzazione della Mappa
var shortCode;          // Nome dello shortcode della mappa
var myPath;
var titolo = undefined;
var key;


/**
 * Style Grafico della 
 * Mappa visualizzata
 */
myStyle = {
    "elements": {
        "water": { "fillColor": "#9cc0f9" },
        "waterPoint": { "iconColor": "#9cc0f9" },
        "transportation": { "strokeColor": "#dadce0" },
        "road": { "fillColor": "#fff" },
        "railway": { "strokeColor": "#fde293" },
        "structure": { "fillColor": "#grey" },
        "runway": { "fillColor": "#575756" },
        "area": { "fillColor": "#b4dfc0" },
        "political": { "borderStrokeColor": "#fef9e8", "borderOutlineColor": "#f9b520" },
        "point": { "iconColor": "#f06292", "fillColor": "#f06292", "strokeColor": "#f06292" },
        "transit": { "fillColor": "#fff" },
        "vegetation": {"fillColor": "#c5dea2"}
    },
    "settings": {"landColor": "#e8eaed"},
    "version": "1.0"
};

/**
 * 
 * @param {Link } path 
 * @param {*Nome shortcode} nameMap
 * 
 * Funzione principale
 * che richiama varie funzioni
 * necessarie per la visualizzazione
 */
function carica(path, nameMap, key) {
    shortCode = nameMap;
    myPath = path;
    percorso = path + "/wpack-maps";
    creaMappa(nameMap);
   
    for (var index = 0; index < listaFileGeo.length; index++) {
        titoloMappa[index] = listaFileGeo[index]["Titolo"].split(" ").join("");
        nomiFile[index] = listaFileGeo[index]["NomeFile"];
    }

    switch (key) {
        case 'slider':
            bottoniSlider();  //crea i bottoni con i colori, event, icone, testi
            setLarg();      //imposta la larghezza dei bottoni
            break;
        case 'button':
            bottonInerith();  //crea i bottoni con i colori, event, icone, testi
            break;
        case 'point':
            bottoniPoint();  //crea i bottoni con i colori, event, icone, testi
            setLarg();      //imposta la larghezza dei bottoni
            break;
        default:
            break;
    }
}

function creaMappa(x, titolo) {
    map = new Microsoft.Maps.Map(document.getElementById("mappa"), {
        center: new Microsoft.Maps.Location(44.8021826, 10.3240791),
        zoom: 14,
        // showLocateMeButton: false,
        showMapTypeSelector: false,
        minZoom: 10,
        showLocateMeButton: true,
        showTrafficButton: true,
        customMapStyle: myStyle,
        disableZooming: false,
        enableClickableLogo: false,
        showBreadcrumb: false,
        showScalebar: false,
        showTermsLink: false,
    });


    /**
 * @var {*Oggetto Dati JSON} listaFileGeo 
 * Richiesta Sincrona del File Json
 * che contiene i dati delle categorie
 * inserite nella mappa
 */
    var fileGeo = new XMLHttpRequest();
    fileGeo.open("GET", percorso + "/file/risorse/" + x + ".json", false);
    fileGeo.setRequestHeader("Content-Type", "application/json");
    fileGeo.onload = function () {
        listaFileGeo = JSON.parse(this.responseText);
    }
    fileGeo.send();

    bingMaps(titolo);

    //crea oggetto infobox
    //in questo modo quando si clicca su un pin
    //si limita a spostarsi
    info = new Microsoft.Maps.Infobox(map.getCenter(), {
        maxWidth: 500,
        maxHeigth: 300,
        visible: false
    })
    info.setMap(map);

    tooltip = new Microsoft.Maps.Infobox(map.getCenter(), {
        visible: false,
        showPointer: false,
        showCloseButton: false,
        offset: new Microsoft.Maps.Point(-75, 20)
    })
    tooltip.setMap(map);

}

function bingMaps(titolo){
    Microsoft.Maps.loadModule("Microsoft.Maps.GeoJson", function () {
        for (i = 0; i < listaFileGeo.length; i++) {
            // console.log(listaFileGeo[i]["Titolo"]);
            Microsoft.Maps.GeoJson.readFromUrl(percorso + "/file/geojson/" + listaFileGeo[i]["NomeFile"],
                function (shapes) {
                    // console.log(shapes);
                    for (i = 0; i < shapes.length; i++) {
                        Microsoft.Maps.Events.addHandler(shapes[i], "click", shapeClick);
                        if(titolo == undefined){
                            shapes[i]["_options"]["visible"] = false;
                        }else{
                            if (shapes[i]["_options"]["subTitle"] == titolo || shapes[i]["metadata"]["subTitle"] == titolo){
                                shapes[i]["_options"]["visible"] = true;
                            }else{
                                shapes[i]["_options"]["visible"] = false;
                            }
                        }
                    }
                    map.entities.push(shapes);                    
                }
            )
        }
    });
}


function shapeClick(e) {
    info.setOptions({
        location: e.location,
        icon: e.target.metadata.icon,
        title: e.target.metadata.title,
        description: e.target.metadata.description,
        visible: true
    })
}

//originariamente fatto per dividere i bottoni su 2 righe.

//scorre il vettore di filejson e crea un bottone div
//per ogni categoria di postazione associando il colore,
//testo, eventi e icona
function bottoniSlider(){
        // crea i bottoni dinamicamente in base alle categoria presenti
        for (i = 0; i < listaFileGeo.length; i++){
            var div = document.createElement("div");
            var par = document.createElement("p");
            par.appendChild(document.createTextNode(listaFileGeo[i]["Titolo"]));
            par.classList.add("descrizione");
            // controlla che sia presente un'icona associata alla categoria
            // e se c'e' la inserisce
            pathImg = listaFileGeo[i]["Icona"];
            if(pathImg != ""){
                var img = document.createElement("img");
                img.setAttribute("src", percorso + "/media/icon/" + pathImg);
                img.setAttribute("draggable", false);
                img.setAttribute("alt", "");
                img.setAttribute("data-title", titoloMappa[i]);
                img.addEventListener("click", selectCategory);
                div.append(img);
            }
            par.setAttribute("data-title", titoloMappa[i]);
            par.addEventListener("click", selectCategory);
            div.appendChild(par);
            var classe = "btn_maps ";
            div.style.backgroundColor = listaFileGeo[i]["Color"];
            div.setAttribute("class", classe);
            div.setAttribute("data-title", titoloMappa[i]);
            div.addEventListener("click", selectCategory);
            document.getElementById("cont").appendChild(div);
        }
    }

//originariamente fatto per dividere i bottoni su 2 righe.

//scorre il vettore di filejson e crea un bottone div
//per ogni categoria di postazione associando il colore,
//testo, eventi e icona
function bottonInerith() {
    // crea i bottoni dinamicamente in base alle categoria presenti
    for (i = 0; i < listaFileGeo.length; i++) {
        var div = document.createElement("div");
        var par = document.createElement("p");
        par.appendChild(document.createTextNode(listaFileGeo[i]["Titolo"]));
        par.classList.add("title");
        par.style.color = listaFileGeo[i]["Color"];
        par.style.textAlign = "center";
        par.setAttribute("data-title", titoloMappa[i]);
        par.addEventListener("click", selectCategory);
        div.appendChild(par);
        var classe = "inerith";
        div.style.borderColor = listaFileGeo[i]["Color"];
        div.setAttribute("class", classe);
        div.setAttribute("data-title", titoloMappa[i]);
        div.addEventListener("click", selectCategory);
        document.getElementById("cont").appendChild(div);
    }
}


//scorre il vettore di filejson e crea un bottone div
//per ogni categoria di postazione associando il colore,
//testo, eventi e icona
function bottoniSlider() {
    // crea i bottoni dinamicamente in base alle categoria presenti
    for (i = 0; i < listaFileGeo.length; i++) {
        var div = document.createElement("div");
        var par = document.createElement("p");
        par.appendChild(document.createTextNode(listaFileGeo[i]["Titolo"]));
        par.classList.add("descrizione");
        // controlla che sia presente un'icona associata alla categoria
        // e se c'e' la inserisce
        pathImg = listaFileGeo[i]["Icona"];
        if (pathImg != "") {
            var img = document.createElement("img");
            img.setAttribute("src", percorso + "/media/icon/" + pathImg);
            img.setAttribute("draggable", false);
            img.setAttribute("alt", "");
            img.setAttribute("data-title", titoloMappa[i]);
            img.addEventListener("click", selectCategory);
            div.append(img);
        }
        par.setAttribute("data-title", titoloMappa[i]);
        par.addEventListener("click", selectCategory);
        div.appendChild(par);
        var classe = "btn_maps ";
        div.style.backgroundColor = listaFileGeo[i]["Color"];
        div.setAttribute("class", classe);
        div.setAttribute("data-title", titoloMappa[i]);
        div.addEventListener("click", selectCategory);
        document.getElementById("cont").appendChild(div);
    }
}


function selectCategory() {
    var x = this.getAttribute("data-title");
    creaMappa(shortCode, x);
    bingReload();
}

function bingReload() {
    document.querySelector('.overlay_').classList.remove('scompari');
    document.querySelector('.overlay_').style.display = 'block';
    document.querySelector('.overlay_').classList.add('scompari');
    setTimeout(() => {
        document.querySelector('.overlay_').style.display = 'none';
    }, 1500);
}