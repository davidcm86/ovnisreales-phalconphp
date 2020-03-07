/**
 * Escuchamos el elemento del selector del lenguage y redireccionamos al idioma elegido
 */
var selectlanguage = document.getElementById("selectLanguage");
selectlanguage.addEventListener("change", changeLanguage);
function changeLanguage() {
    var language = selectlanguage.options[selectlanguage.selectedIndex].value;
    window.location.href = 'https://www.' + language + '.ovnisreales.loc';
}