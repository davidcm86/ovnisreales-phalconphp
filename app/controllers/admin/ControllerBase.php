<?php

namespace OvnisReales\Controllers\Admin;
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function initialize()
    {
        date_default_timezone_set('Europe/Madrid');
        setlocale(LC_ALL, 'es_ES.UTF-8');
        $this->assets->addCss('css/admin-estilos.css');
        $this->assets->addJs('js/admin/jquery-3.4.1.min.js');
        $this->assets->addJs('js/admin/bootstrap-4.1.3.min.js');
        $this->assets->addJs('js/admin/general.js');
        $this->view->setTemplateAfter('administrador');
        $this->view->languages = ['es' => 'Español', 'mx' => 'México'];
        if (!$this->session->has('IdiomaAdmin')) {
            $this->session->set('IdiomaAdmin', 'es');
            $this->tag->setDefault('selectLanguage', 'es');
        } else {
            $idiomaAdmin = $this->session->get('IdiomaAdmin');
            $this->tag->setDefault('selectLanguage', $idiomaAdmin);
        }
        if ($this->session->has('UsuarioAdmin')) $this->view->usuarioAdmin = $this->session->get('UsuarioAdmin');
    }

    public function beforeExecuteRoute()
    {
        if (!$this->session->has('UsuarioAdmin') && $this->dispatcher->getActionName() != 'login') {
            $this->flash->error('No tienes permisos para realizar esa acción.');
            $this->response->redirect('/admin/usuarios/login');
        }
        $this->view->setViewsDir($this->view->getViewsDir() . 'admin/'); // todo dentro de admin tiene el prefijo admin
    }

    public function thrown404() {
        // 404 si no existe el idioma
        $response = new \Phalcon\Http\Response();
        $response->setStatusCode(404, "Not Found");
        $this->response->send();
        return $this->dispatcher->forward([
            'controller' => 'errores',
            'action' => 'notFound',
        ]);
    }
}
