<?php

namespace Ovnisreales\Controllers;

use Ovnisreales\Models\Categorias;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $categoriasPrincipales = Categorias::find(["conditions" => "pais = '" . DOMINIO_SELECT . "'"]);
        $this->view->categoriasPrincipales = $categoriasPrincipales;
    }

}

