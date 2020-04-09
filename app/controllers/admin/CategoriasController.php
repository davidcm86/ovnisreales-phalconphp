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
    }

    public function editarAction($id)
    {
        $categoria = Categorias::findFirst($id);
        if (!$categoria) {
            $this->flashSession->error("Categoria no encontrada.");
            $this->response->redirect('/admin/categorias');
            return;
        }
        $this->assets->addJs('/js/admin/ckeditor/ckeditor.js');
        $this->assets->addJs('/js/admin/categorias.js');
        if ($this->request->isPost()) {
            $nombre = $this->request->getPost('nombre');
            if ($nombre != $categoria->nombre) {
                $categoria->nombre = $this->request->getPost('nombre');
                $categoria->slug = $this->Slug->generate($this->request->getPost('nombre'));
            }
            $categoria->pais = $this->session->get('IdiomaAdmin');
            $categoria->descripcion_principal = $this->request->getPost('descripcion_principal');
            $categoria->descripcion_secundaria = $this->request->getPost('descripcion_secundaria');
            $categoria->title_seo = $this->request->getPost('title_seo');
            $categoria->description_seo = $this->request->getPost('description_seo');
            $categoria->keywords = $this->request->getPost('keywords');
            $files = $this->request->getUploadedFiles();
            if ($categoria->update()) {
                if (isset($files[0]) && !empty($files[0]->getName())) {
                    $rutaImagen = BASE_PATH . '/public/img/categorias_principales/' . $this->idiomaAdmin . '/';
                    $rutaImagenBuscar = BASE_PATH . '/public/' . $categoria->imagen;
                    $rutaImagenBd = $this->ImagenesPlugin->uploadGenericoMultiple($rutaImagen, $categoria->slug, $rutaImagenBuscar);
                    if (!empty($rutaImagenBd)) {
                        $categoria->imagen = $rutaImagenBd;
                        $categoria->update();
                    }
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
        } else {
            $this->view->imagen = $categoria->imagen;
            $this->tag->setDefault("id", $categoria->id);
            $this->tag->setDefault("nombre", $categoria->nombre);
            $this->tag->setDefault("descripcion_principal", $categoria->descripcion_principal);
            $this->tag->setDefault("descripcion_secundaria", $categoria->descripcion_secundaria);
            $this->tag->setDefault("title_seo", $categoria->title_seo);
            $this->tag->setDefault("description_seo", $categoria->description_seo);
            $this->tag->setDefault("keywords", $categoria->keywords);   
        }
        $this->view->id = $id;
    }

    public function deleteAction($id)
    {
        $this->view->disable();
        $categoria = Categorias::findFirstByid($id);
        if (!$categoria) {
            $this->flashSession->error("La categoría no ha sido encontrada.");
            $this->response->redirect('/admin/categorias');
            return;
        }
        $rutaImagenBorrar = BASE_PATH . '/public' . $categoria->imagen;
        if (file_exists($rutaImagenBorrar)) unlink($rutaImagenBorrar);
        if (!$categoria->delete()) {
            $messagesError = '';
            foreach ($categoria->getMessages() as $message) {
                $messagesError .= $message;
            }
            $this->flashSession->success($messagesError);
            $this->response->redirect('/admin/categorias');
            return;
        }
        $this->flashSession->success("La categoría ha sido borrada correctamente.");
        $this->response->redirect('/admin/categorias');
    }

    public function cambiarIdiomaAction($idioma = null)
    {
        $this->view->disable();
        if (!empty($idioma)) $this->session->set('IdiomaAdmin', $idioma);
        $this->response->redirect('/admin/categorias');
    }
}
