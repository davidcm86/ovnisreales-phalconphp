<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function initialize()
    {
        $this->view->setTemplateAfter('default');
        $this->assets->addJs('js/common.js');
        // idiomas para subdominios
        $this->view->languages = ['es' => 'Español', 'mx' => 'México'];
        // enviamos idioma seleccionado al select
        $this->tag->setDefault('selectLanguage', DOMINIO_SELECT);
    }
}
