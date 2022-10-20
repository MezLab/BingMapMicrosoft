/**
 * Plugin Name: WPackage - Bing Maps Microsoft
 * Version: 1.0.0
 * Author: Mez {Massimo Maestri} 
 * Author URI: https://www.massimomaestri.com
 */

var btnW;
var dep = 0;
var numeroBtn = 3;
var larghezzaBtn = 150;

window.addEventListener("keydown", scorriKeys);
document.querySelector(".container").addEventListener("mousemove", muoviMouse);
document.querySelector(".container").addEventListener("mouseup", scorriMouse);
window.addEventListener("resize", ridimensiona);

// registra i movimenti del mouse sopra il container dei bottoni
// e quando viene premuto il tasto sinistro lo muove
function muoviMouse(e){
    //console.log(dep);
    if(e.buttons == 1){
        //console.log(dep);
        dep += e.movementX;
        document.querySelector(".container").style.transform = "translateX(" + dep + "px)";
    }
}

// quando viene rilasciato il tasto sinistro
// calcola la posizion dei bottoni in modo che non vengano tagliati
function scorriMouse(e){
    var set = Math.floor(dep/btnW) * btnW;
    if(set <= 0 && set >= -((nomiFile.length) * btnW) + (numeroBtn*btnW))
        dep = (Math.floor(dep/btnW) * btnW);
    else if(set > 0)
        dep = 0;
    else
        dep = -((nomiFile.length) * btnW) + (numeroBtn*btnW);    
    document.querySelector(".container").style.transform = "translate(" + dep + "px)";
}

// calcola la larghezza dei bottoni in base al numero di essi e alla larghezza
// del contenitore
function largBtn(nBtn, larg){
    return Math.round(larg/nBtn);
}

// funzione che calcola la grandezza dei bottoni
// calcola anche il numero di bottoni nella pagina in base alla larghezza
// della finestra

function setLarg(){
    var btn = document.querySelectorAll(".btn_maps");
    var largCont = document.querySelector(".section").style.width = numeroBtn * larghezzaBtn;

    if (document.documentElement.clientWidth < 630) {
        numeroBtn = 2;
    } else if (document.documentElement.clientWidth > 630 && document.documentElement.clientWidth < 1100) {
        numeroBtn = 3;
    } else {
        numeroBtn = numeroBtn;
    }
    
    btnW = largBtn(numeroBtn, largCont);
    for(i = 0; i < btn.length; i++){
        btn[i].style.width = btnW + "px";
    }
}


// scorre il contenitore verso destra o verso sinistra della dimensione
// di un singolo bottone in modo che sembri stia scorrendo
function scorri(direzione){
    switch(direzione){
        case 'L':
            if(dep < 0){
                dep += btnW;
                document.querySelector(".container").style.transform = "translateX(" + dep + "px)";
            }else{
                console.log("fine linea");
            }
            break;
        case 'R':
            if(dep > -((nomiFile.length) * btnW) + (numeroBtn*btnW)){
                dep -= btnW;
                document.querySelector(".container").style.transform = "translateX(" + dep + "px)";
            }else{
                console.log("fine linea");
            }   
            break;
    }
}

// calcola la larghezza dei bottoni e la loro posizione in modo
// che non vengano tagliati quando viene ridimensionata la finestra
function ridimensiona(){
    if(btnW){
        var app = btnW;
        setLarg();

        dep = (dep*btnW)/app;
        document.querySelector(".container").style.transform = "translateX(" + dep + "px)";
    }
}

// scorre lo slider quando vengono premute la freccia destra e sinistra
function scorriKeys(e){
    var key = e.key;
    if(key == "ArrowLeft")
        scorri('L');
    else if(key == "ArrowRight")
        scorri('R');
}


