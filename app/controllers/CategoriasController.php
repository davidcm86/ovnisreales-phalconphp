<?php

namespace OvnisReales\Controllers;

use OvnisReales\Models\Productos;
use OvnisReales\Models\Categorias;
use OvnisReales\Models\EstadisticaProductos;

use Phalcon\Http\Request;

class CategoriasController extends ControllerBase
{

    public function listarAction()
    {
        $this->assets->addJs('js/productos.js');
        $categoriaSlug = $this->dispatcher->getParam('categoriaSlug');
        //$categoria = $this->modelsCache->get('categorias-listado-' . DOMINIO_SELECT);
        //if (empty($categoria)) {
            $categoria = Categorias::findFirst(["conditions" => "pais = '" . DOMINIO_SELECT . "' AND slug = '" . $categoriaSlug . "'"]);
            //$this->modelsCache->save('categorias-listado-' . DOMINIO_SELECT, $categoria);
        //}
        $this->view->imagenOg = $categoria->imagen;
        $this->view->categoria = $categoria;
        $this->view->h1 = $categoria->nombre;
        $this->view->contenidoRelacionado = $this->__getCategoriasRandom($categoria->id);

        //$productos = $this->modelsCache->get('categorias-productos-' . DOMINIO_SELECT);
        //if (empty($productos)) {
            $productos = Productos::find(["conditions" => "categoria_id = " . $categoria->id]);
            $this->modelsCache->save('categorias-productos-' . DOMINIO_SELECT, $productos);
        //}
        $this->view->productos = $productos;
        $this->view->titleSeo = $categoria->title_seo;
        $this->view->descriptionSeo = $categoria->description_seo;
        $this->view->keywords = $categoria->keywords;
        // breadcrumbs
        $this->Breadcrumbs->add('Inicio', '/');
        $breadCrumbJsonld[] = ['nombre' => 'Inicio', 'url' => '/'];

        $this->Breadcrumbs->add($categoria->nombre, '/' . $categoria->slug);
        $breadCrumbJsonld[] = ['nombre' => $categoria->nombre, 'url' => '/' . $categoria->slug];

        //$categoriasPrincipales = $this->modelsCache->get('categorias-principales-' . DOMINIO_SELECT);
        //if (empty($categoriasPrincipales)) {
            $categoriasPrincipales = Categorias::find(["conditions" => "pais = '" . DOMINIO_SELECT . "'", "order" => "rand()"]);
            $this->modelsCache->save('categorias-principales-' . DOMINIO_SELECT, $categoriasPrincipales);
        //}
        $this->view->categoriasPrincipales = $categoriasPrincipales;

        // json datos
        $itemListElementBread = [];
        foreach ($breadCrumbJsonld as $key => $bread) {
            $itemListElementBread[] = [
                "@type" => "ListItem",
                "position" => $key+1,
                    "item" => [
                        "@id" => DOMINIO . $bread['url'],
                        "name" => $bread['nombre']
                    ]
                ];
        }
        $docBreadCrumbs = (object)array(
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "itemListElement" => $itemListElementBread
        );
        $this->view->jsonldBreadCrumbs = json_encode($docBreadCrumbs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $itemListElement = [];
        foreach ($productos as $key => $producto) {
            $itemListElement[] = [
                "@type" => "ListItem",
                "position" => $key+1,
                "url" =>  $producto->enlace
                ];
        }
        $doc = (object)array(
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "itemListElement" => $itemListElement
        );

        $this->view->jsonld = json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Obtener categorias al azar obviando la actual
     */
    private function __getCategoriasRandom($categoriaId)
    {
        //$categoriasRelacionadas = $this->modelsCache->get('categorias-azar-' . DOMINIO_SELECT . '-' . $categoriaId);
        //if (empty($categoriasRelacionadas)) {
            $categoriasRelacionadas = Categorias::find([
                "conditions" => "id != " . $categoriaId . " AND pais = '" . DOMINIO_SELECT . "'",
                "order" => "rand()",
                "limit" => 3]);
            $this->modelsCache->save('categorias-azar-' . DOMINIO_SELECT . '-' . $categoriaId, $categoriasRelacionadas);
        //}
        return $categoriasRelacionadas;
    }

    /**
     * Guardamos la cantidad de veces que hacen clicks en los productos
     */
    public function estadisticaProductoAjaxAction()
    {
        $this->view->disable();
        $this->view->setTemplateAfter('vacio');
        $request = new Request();
        $idProducto = $request->getPut('idProducto');
        if (!empty($idProducto)) EstadisticaProductos::saveEstadistica($idProducto, date('m'), date('Y'));
    }

}

