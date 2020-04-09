<?php
namespace OvnisReales\Controllers;

use OvnisReales\Models\VideosFacebook;

use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class VideosController extends ControllerBase
{
    /**
     * Listamos los videos de facebook que tenemos
     * TODO: para mx/es son los mismos videos por lo que tendremos que hacer algo para google, que no de contenido duplicado
     */
    public function listadoAction()
    {
        $numberPage = 1;
        if (!$this->request->isPost()) {
            $numberPage = $this->request->getQuery("page", "int");
            //$this->persistent->parameters = $parameters;
        }
        //$this->persistent->parameters = $parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }/* else {
            // si pagina mantenemos los valores de los input en la vista
            $_POST = $this->session->get('condiciones-blog-buscar');
        }*/
        // Ya tenemos la relación en los modelos, por ello en columns no hace falta poner los  models relacion
        $builder = $this->modelsManager->createBuilder()
            ->columns(['VideosFacebook.*'])
            ->from(['VideosFacebook' => 'OvnisReales\Models\VideosFacebook'])
            ->orderBy('VideosFacebook.created DESC');
        $paginator = new PaginatorQueryBuilder(
            [
                'builder' => $builder,
                'limit'   => 1,
                'page'    => $numberPage,
            ]
        );
        $h1Pagina = "";
        if ($numberPage > 1) $h1Pagina = " para la página " . $numberPage;
        $this->view->h1Pagina = $h1Pagina;
        $this->view->page = $paginator->getPaginate();

        // TODO: si vamos a tirar los videos iguales, como hacer para que no repitan en ES/MX...porque repiten pero habrá que decir algo a google
        $this->view->titleSeo = 'TODO';
        $this->view->descriptionSeo = 'TODO';
	}
}
