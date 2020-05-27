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
        $this->view->titleSeo = 'Tienda online de artículos ovnis y extraterrestres';
        $this->view->descriptionSeo = 'Tienda online de artículos de ovnis reales y extraterrestres. Camisetas, tazas, ropa, colgantes...';
        // metas jsonld
        $doc = (object)array(
            "@context" => "http://schema.org",
            "@type" => "WebPage",
            "name" => 'Tienda online de artículos ovnis y extraterrestres',
            "description" => 'Tienda online de artículos de ovnis reales y extraterrestres. Camisetas, tazas, ropa, colgantes...',
        );
        $this->view->h1 = "Tienda online de artículos ovnis y extraterrestres";
        $this->view->jsonld = json_encode($doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->view->imagenOg = '/img/common/ovni-logo.svg';
        $this->view->textoInicio = $this->__textoInicio();
        $this->view->metakeys = "ovnis reales,tienda online ovnis,tienda online extraterrestres,comprar productos ovnis,comprar productos extraterrestes";
        if ($this->dispatcher->getParam("extension") == 'amp') $this->view->pick('index/amp/index');
    }

    private function __textoInicio() 
    {
        if (DOMINIO_SELECT == 'es') {
            $texto = 'Si te gustan los 🛸 OVNIS y los 👽 EXTRATERRESTRES esta es tu web para comprar artículos de ufología al mejor precio. Los productos han sido elegidos especialmente para ti 💜 atrevete a ser diferente.';
        } else {
            $texto = 'En 🛸 OVNIS REALES tienes los mejores productos de ufología y 👽 extraterrestres, con los precios más económicos y los mejores artículos solo para ti 💜 ¿te atreves?.';
        }
        return $texto;
    }

}

