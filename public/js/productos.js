function estadisticaProducto(idProducto)
{
    if (idProducto != undefined) {
        var r = new XMLHttpRequest();
        r.open("POST", "/categorias/estadisticaProductoAjax", true);
        r.onreadystatechange = function () {};
        r.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        r.setRequestHeader('Content-Type', 'application/json');
        r.send(JSON.stringify({
            idProducto: idProducto
        }));
    }
}