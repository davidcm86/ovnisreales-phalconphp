<?php
namespace OvnisReales\Controllers\Admin;

use OvnisReales\Models\VideosFacebook;

use Phalcon\Http\Response;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use Phalcon\Paginator\Adapter\Model as Paginator;

class VideosfacebookController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->usuarioAdmin = $this->session->get('UsuarioAdmin');
    }

    public function indexAction()
    {
        $numberPage = 1;
        if (!$this->request->isPost()) {
            $numberPage = $this->request->getQuery("page", "int");
        }
        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters = [];
        $builder = $this->modelsManager->createBuilder($parameters)
            ->columns(['VideosFacebook.*'])
            ->from(['VideosFacebook' => 'OvnisReales\Models\VideosFacebook'])
            ->orderBy('VideosFacebook.created');
        $paginator = new PaginatorQueryBuilder(
            [
                'builder' => $builder,
                'limit'   => 15,
                'page'    => $numberPage,
            ]
        );
        $this->view->page = $paginator->getPaginate();
    }

    public function crearAction()
    {
        if ($this->request->isPost()) {
            $videofacebook = new VideosFacebook();
            $videofacebook->titulo = $this->request->getPost('titulo');
            $videofacebook->descripcion = $this->request->getPost('descripcion');
            $videofacebook->facebook_id = $this->request->getPost('facebook_id');
            if ($videofacebook->save()) {
                $this->flashSession->success("Video facebook creado correctamente.");
                $this->response->redirect('/admin/videosfacebook');
            }
        }
    }

    public function editarAction($id)
    {
        $video = VideosFacebook::findFirstByid($id);
        if (!$video) {
            $this->flashSession->success("Video no encontrado.");
            $this->response->redirect('admin/videosfacebook');
            return;
        }    
        if ($this->request->isPost()) {
            $video->titulo = $this->request->getPost('titulo');
            $video->facebook_id = $this->request->getPost('facebook_id');
            $video->descripcion = $this->request->getPost('descripcion');
            if ($video->save()) {
                $this->flashSession->success("Video editado correctamente.");
                $this->response->redirect('/admin/videosfacebook');
            }
        } else {
            $this->tag->setDefault("titulo", $video->titulo);
            $this->tag->setDefault("facebook_id", $video->facebook_id);
            $this->tag->setDefault("descripcion", $video->descripcion);
        }
        $this->view->id = $id;
    }

    public function deleteAction($id)
    {
        $this->view->disable();
        $video = VideosFacebook::findFirstByid($id);
        if (!$video) {
            $this->flashSession->success("El video no ha sido encontrado.");
            $this->response->redirect('admin/videosfacebook');
            return;
        }
        if (!$video->delete()) {
            $messagesError = '';
            foreach ($video->getMessages() as $message) {
                $messagesError .= $message;
            }
            $this->flashSession->success($messagesError);
            $this->response->redirect('admin/videosfacebook');
            return;
        }
        $this->flashSession->success("El video ha sido borrado correctamente.");
        $this->response->redirect('admin/videosfacebook');
    }
}
