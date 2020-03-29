<?php

namespace OvnisReales\Controllers;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class ControllerBase extends Controller
{
    public function initialize()
    {
        date_default_timezone_set('Europe/Madrid');
        setlocale(LC_ALL, 'es_ES.UTF-8');
        $this->view->setTemplateAfter('default');
        $this->assets->addJs('js/common.js');
        // idiomas para subdominios
        $this->view->languages = ['es' => 'Español', 'mx' => 'México'];
        // enviamos idioma seleccionado al select
        $this->tag->setDefault('selectLanguage', DOMINIO_SELECT);
    }
}
