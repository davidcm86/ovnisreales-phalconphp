<?php

namespace Ovnisreales\Controllers;

use Ovnisreales\Models\Categorias;

class CategoriasController extends ControllerBase
{

    public function listarAction()
    {
        $this->logger->info('mipox');
        $categoriaSlug = $this->dispatcher->getParam('categoriaSlug');
        $categoria = Categorias::findFirst(["conditions" => "pais = '" . DOMINIO_SELECT . "' AND slug = '" . $categoriaSlug . "'"]);
        $this->view->categoriaSlug = $categoriaSlug;
        $this->view->titleSeo = $categoria->title_seo;
        $this->view->descriptionSeo = $categoria->description_seo;
    }

}

