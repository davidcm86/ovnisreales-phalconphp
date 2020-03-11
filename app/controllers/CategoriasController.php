<?php

namespace Ovnisreales\Controllers;

use Ovnisreales\Models\Categorias;

class CategoriasController extends ControllerBase
{

    public function listarAction()
    {
        $this->logger->info('mipox');
        //$categoriasPrincipales = Categorias::find(["conditions" => "pais = '" . DOMINIO_SELECT . "'"]);
        $categoriaSlug = $this->dispatcher->getParam('categoriaSlug');
        $this->logger->info('$categoriaSlug: ' . $categoriaSlug);
        $this->view->categoriasPrincipales = $categoriasPrincipales;
        $this->view->titleSeo = 'Ovnis reales';
        $this->view->descriptionSeo = 'Ovnis reales';
    }

}

