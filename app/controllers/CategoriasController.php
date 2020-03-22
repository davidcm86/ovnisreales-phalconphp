<?php

namespace Ovnisreales\Controllers;

use Ovnisreales\Models\Productos;
use Ovnisreales\Models\Categorias;

class CategoriasController extends ControllerBase
{

    public function listarAction()
    {
        $categoriaSlug = $this->dispatcher->getParam('categoriaSlug');
        $categoria = Categorias::findFirst(["conditions" => "pais = '" . DOMINIO_SELECT . "' AND slug = '" . $categoriaSlug . "'"]);
        $this->view->categoria = $categoria;

        $productos = Productos::find(["conditions" => "categoria_id = " . $categoria->id]);
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
    }

}

