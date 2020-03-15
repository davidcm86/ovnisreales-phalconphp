<?php

namespace Ovnisreales\Controllers;

use Ovnisreales\Models\Categorias;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $categoriasPrincipales = Categorias::find(["conditions" => "pais = '" . DOMINIO_SELECT . "'"]);
        $this->view->categoriasPrincipales = $categoriasPrincipales;
        $this->view->titleSeo = 'Tienda online de artículos de ovnis y extraterrestres | OVNIS REALES';
        $this->view->descriptionSeo = 'Tienda online de artículos de ovnis reales y extraterrestres. Camisetas, tazas, ropa...';
        // metas jsonld
        $doc = (object)array(
            "@context" => "http://schema.org",
            "@type" => "WebPage",
            "name" => 'Tienda online de artículos de ovnis y extraterrestres | OVNIS REALES',
            "description" => 'Tienda online de artículos de ovnis reales y extraterrestres. Camisetas, tazas, ropa...',
        );
        $this->view->jsonld = json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

}

