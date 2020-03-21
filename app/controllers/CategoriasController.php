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
        // TODO: breadcrumbs
        $productos = Productos::find(["conditions" => "categoria_id = " . $categoria->id]);
        $this->view->productos = $productos;
        $this->view->titleSeo = $categoria->title_seo;
        $this->view->descriptionSeo = $categoria->description_seo;
    }

}

