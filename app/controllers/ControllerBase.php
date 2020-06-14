<?php

namespace OvnisReales\Controllers;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

use OvnisReales\Models\Categorias;

use OvnisReales\Classes\MiddleWareViewClass;

class ControllerBase extends Controller
{
    public function initialize()
    {
        date_default_timezone_set('Europe/Madrid');
        setlocale(LC_ALL, 'es_ES.UTF-8');
		if (!empty($this->dispatcher->getParam("extension")) && ($this->dispatcher->getParam("extension")=='amp') || ($this->dispatcher->getParam("extension")=='.amp')) {
			$this->view->setTemplateAfter('amp');
		} else {
			$this->view->setTemplateAfter('default');
		}
        $this->assets->addJs('js/common.js');
        // idiomas para subdominios
        $this->view->languages = ['es' => 'Español', 'mx' => 'México'];
        // enviamos idioma seleccionado al select
        $this->tag->setDefault('selectLanguage', DOMINIO_SELECT);

        $this->__getCategoriasPrincipalesFooter();

        // MinifyHTML
        $middleWareViewClass = new MiddleWareViewClass();
        $middleWareViewClass::getInstance()->minifyHtml();
    }

    private function __getCategoriasPrincipalesFooter()
    {
        $categoriasPrincipalesFooter = $this->modelsCache->get('categorias-principales-footer-' . DOMINIO_SELECT);
        if (empty($categoriasPrincipalesFooter)) {
            $categoriasPrincipalesFooter = Categorias::find(["conditions" => "pais = '" . DOMINIO_SELECT . "'"]);
            $this->modelsCache->save('categorias-principales-footer-' . DOMINIO_SELECT, $categoriasPrincipalesFooter);
        }
        $this->view->categoriasPrincipalesFooter = $categoriasPrincipalesFooter;
    }
}
