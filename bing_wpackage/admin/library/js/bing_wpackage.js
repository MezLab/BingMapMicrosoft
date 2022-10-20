/**
 * Plugin Name: WPackage - Bing Maps Microsoft
 * Version: 1.0.0
 * Author: Mez {Massimo Maestri} 
 * Author URI: https://www.massimomaestri.com
 */


document.addEventListener("DOMContentLoaded", function() {

    var shortCodeElem = document.querySelectorAll('i[data-class="copy"]');

    function unsocials_copy() {
        /* Ottieni il campo dello shortcode */
        var shortcode = this.querySelector(".shortcode");
        /* Copia lo shortcode all'interno del campo di testo */
        navigator.clipboard.writeText(shortcode.innerHTML);
        /* Avvisa che lo shortcode è stato copiato */
        alert("Shortcode Copiato: " + shortcode.innerHTML);
    }
    
    for (let index = 0; index < shortCodeElem.length; index++) {
        shortCodeElem[index].addEventListener("click", unsocials_copy);
    }


    var boxColor = document.querySelector('.esterno input[type="color"]');
    var inputColor = document.querySelector('.esterno input[name="esadecimale"]');
    var pButton = document.querySelector('p.btn_maps');

    function colorMy() { boxColor.value = inputColor.value; }
    pButton.addEventListener("click", colorMy);

});

function openBox(x) {
    var elem = document.getElementById(x);
    if (elem.style.display == 'block') {
        elem.style.display = 'none';
    } else {
        elem.style.display = 'block';
    }
}

function cleanCoords(){
    var coords = document.querySelector("textarea[name='coords']");
    coords.value = coords.value.replace(/\s+/g, '');
    alert("Spazi vuoti eliminati");
}



// window.addEventListener("load", function(){})









 
