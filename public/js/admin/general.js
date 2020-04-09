$(document).ready(function(){
    $("#selectLanguage").change(function(){
        window.location.replace("/admin/categorias/cambiarIdioma/" + $(this).val());
    });
});