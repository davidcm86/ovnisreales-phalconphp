<?php

namespace Ovnisreales\Controllers;

use Ovnisreales\Models\Productos;
use Ovnisreales\Models\Categorias;

class CategoriasController extends ControllerBase
{

    public function listarAction()
    {
        $categoriaSlug = $this->dispatcher->getParam('categoriaSlug');
        $categoria = $this->modelsCache->get('categorias-listado-' . DOMINIO_SELECT);
        if (empty($categoria)) {
            $categoria = Categorias::findFirst(["conditions" => "pais = '" . DOMINIO_SELECT . "' AND slug = '" . $categoriaSlug . "'"]);
            $this->modelsCache->save('categorias-listado-' . DOMINIO_SELECT, $categoria);
        }
        $this->view->categoria = $categoria;

        $productos = $this->modelsCache->get('categorias-productos-' . DOMINIO_SELECT);
        if (empty($productos)) {
            $productos = Productos::find(["conditions" => "categoria_id = " . $categoria->id]);
            $this->modelsCache->save('categorias-productos-' . DOMINIO_SELECT, $productos);
        }
        $this->view->productos = $productos;

        $this->view->titleSeo = $categoria->title_seo;
        $this->view->descriptionSeo = $categoria->description_seo;
        // breadcrumbs
        $this->Breadcrumbs->add('Inicio', '/');
        $breadCrumbJsonld[] = ['nombre' => 'Inicio', 'url' => '/'];

        $this->Breadcrumbs->add($categoria->nombre, '/' . $categoria->slug);
        $breadCrumbJsonld[] = ['nombre' => $categoria->nombre, 'url' => '/' . $categoria->slug];


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

}

