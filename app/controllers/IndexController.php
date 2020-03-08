<?php

namespace Ovnisreales\Controllers;

use Ovnisreales\Models\CategoriaPrincipal;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $categoriasPrincipales = CategoriaPrincipal::find(["conditions" => "pais = '" . DOMINIO_SELECT . "'"]);
        $this->view->categoriasPrincipales = $categoriasPrincipales;
    }

}

