<?php
namespace OvnisReales\Controllers\Admin;

use OvnisReales\Models\Categorias;

use Phalcon\Http\Response;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;

class CategoriasController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->idiomaAdmin = $this->session->get('IdiomaAdmin');
        $this->usuarioAdmin = $this->session->get('UsuarioAdmin');
    }

    public function indexAction()
    {
        $numberPage = 1;
        if (!$this->request->isPost()) {
            $numberPage = $this->request->getQuery("page", "int");
        }
        $builder = $this->modelsManager->createBuilder()
            ->columns(['Categorias.*'])
            ->from(['Categorias' => 'OvnisReales\Models\Categorias'])
            ->where('Categorias.pais = "'.$this->idiomaAdmin.'"')
            ->orderBy('Categorias.nombre');
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
        try {
            $this->assets->addJs('/js/admin/ckeditor/ckeditor.js');
            $this->assets->addJs('/js/admin/categorias.js');
            if ($this->request->isPost()) {
                $categoria = new Categorias();
                $categoria->nombre = $this->request->getPost('nombre');
                $categoria->slug = $this->Slug->generate($this->request->getPost('nombre'));
                $categoria->pais = $this->session->get('IdiomaAdmin');
                $categoria->descripcion_principal = $this->request->getPost('descripcion_principal');
                $categoria->descripcion_secundaria = $this->request->getPost('descripcion_secundaria');
                $categoria->title_seo = $this->request->getPost('title_seo');
                $categoria->description_seo = $this->request->getPost('description_seo');
                $categoria->keywords = $this->request->getPost('keywords');
                $files = $this->request->getUploadedFiles();
                if (isset($files[0]) && !empty($files[0]->getName())) $categoria->imagen = "validation-true";
                if ($categoria->save()) {
                    $rutaImagen = BASE_PATH . '/public/img/categorias_principales/' . $this->idiomaAdmin . '/';
                    $rutaImagenBd = $this->ImagenesPlugin->uploadGenericoMultiple($rutaImagen, $categoria->slug);
                    if (!empty($rutaImagenBd)) {
                        $categoria->imagen = $rutaImagenBd;
                        $categoria->update();
                    }
                    $this->flashSession->success("La categoría ha sido creada correctamente.");
                    $this->response->redirect('/admin/categorias');
                } else {
                    $messagesError = [];
                    foreach ($categoria->getMessages() as $message) {
                        $messagesError[] = $message . '</br>';
                    }
                    $this->tag->setDefault("descripcion_principal", $this->request->getPost('descripcion_principal'));
                    $this->tag->setDefault("descripcion_secundaria", $this->request->getPost('descripcion_secundaria'));
                    $this->view->messagesError = $messagesError;
                }
            }
        } catch (\Exception $e) {
            $message = get_class($e) . ": " . $e->getMessage() . "\n" . " File=" . $e->getFile() . "\n" . " Line=" . $e->getLine() . "\n" . $e->getTraceAsString() . "\n";
            print_r($message);die;
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

    public function cambiarIdiomaAction($idioma = null)
    {
        $this->view->disable();
        if (!empty($idioma)) $this->session->set('IdiomaAdmin', $idioma);
        $this->response->redirect('/admin/categorias');
    }
}
