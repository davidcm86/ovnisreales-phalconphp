<?php

namespace OvnisReales\Controllers;

use OvnisReales\Models\Categorias;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $categoriasPrincipales = $this->modelsCache->get('categorias-principales-' . DOMINIO_SELECT);
        if (empty($categoriasPrincipales)) {
            $categoriasPrincipales = Categorias::find(["conditions" => "pais = '" . DOMINIO_SELECT . "'", "order" => "rand()"]);
            $this->modelsCache->save('categorias-principales-' . DOMINIO_SELECT, $categoriasPrincipales);
        }
        $this->view->categoriasPrincipales = $categoriasPrincipales;
        $this->view->titleSeo = 'Tienda online de artículos de ovnis y extraterrestres | OVNIS REALES';
        $this->view->descriptionSeo = 'Tienda online de artículos de ovnis reales y extraterrestres. Camisetas, tazas, ropa, colgantes...';
        // metas jsonld
        $doc = (object)array(
            "@context" => "http://schema.org",
            "@type" => "WebPage",
            "name" => 'Tienda online de artículos de ovnis y extraterrestres | OVNIS REALES',
            "description" => 'Tienda online de artículos de ovnis reales y extraterrestres. Camisetas, tazas, ropa, colgantes...',
        );
        $this->view->jsonld = json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

}

