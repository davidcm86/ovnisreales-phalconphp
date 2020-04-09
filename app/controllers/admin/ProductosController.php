<?php
namespace OvnisReales\Controllers\Admin;

use OvnisReales\Models\Productos;
use OvnisReales\Models\Categorias;

use Phalcon\Http\Response;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use Phalcon\Mvc\Model\Criteria;

class ProductosController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->idiomaAdmin = $this->session->get('IdiomaAdmin');
        $this->usuarioAdmin = $this->session->get('UsuarioAdmin');
    }

    public function indexAction()
    {
        $this->view->categorias = Categorias::find(['conditions' => 'pais = "'.$this->idiomaAdmin.'"']);
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'OvnisReales\Models\Productos', $_POST);
            $params = $query->getParams();
            $this->session->set('condiciones-productos-buscar', $_POST);
            $this->persistent->parameters = $params;
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }
        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        } else {
            // si pagina mantenemos los valores de los input en la vista
            $_POST = $this->session->get('condiciones-productos-buscar');
        }
        $builder = $this->modelsManager->createBuilder($parameters)
            ->columns(['Productos.*'])
            ->from(['Productos' => 'OvnisReales\Models\Productos'])
            ->orderBy('Productos.created DESC');
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
            if ($this->request->isPost()) {
                $producto = new Productos();
                $producto->nombre_producto = $this->request->getPost('nombre_producto');
                $producto->tipo_moneda_id = $this->__getIdTipoMonedaid($this->idiomaAdmin);
                $producto->categoria_id = $this->request->getPost('categoria_id');
                $producto->precio = $this->request->getPost('precio');
                $producto->es_rebajado = $this->request->getPost('es_rebajado');
                $producto->activo = $this->request->getPost('activo');
                $producto->enlace = $this->request->getPost('enlace');
                $files = $this->request->getUploadedFiles();
                if (isset($files[0]) && !empty($files[0]->getName())) $producto->imagen = "validation-true";
                if ($producto->save()) {
                    $rutaImagen = BASE_PATH . '/public/img/productos/' . Categorias::getSlugCategoria($producto->categoria_id) . '/' . $this->idiomaAdmin . '/';
                    $rutaImagenBd = $this->ImagenesPlugin->uploadGenericoMultiple($rutaImagen, $this->Slug->generate($producto->nombre_producto));
                    if (!empty($rutaImagenBd)) {
                        $producto->imagen = $rutaImagenBd;
                        $producto->update();
                    }
                    $this->flashSession->success("El producto ha sido creada correctamente.");
                    $this->response->redirect('/admin/productos');
                } else {
                    $messagesError = [];
                    foreach ($producto->getMessages() as $message) {
                        $messagesError[] = $message . '</br>';
                    }
                    $this->view->messagesError = $messagesError;
                }
            }
            $this->view->categorias = Categorias::find(['conditions' => 'pais = "'.$this->idiomaAdmin.'"']);
        } catch (\Exception $e) {
            $message = get_class($e) . ": " . $e->getMessage() . "\n" . " File=" . $e->getFile() . "\n" . " Line=" . $e->getLine() . "\n" . $e->getTraceAsString() . "\n";
            print_r($message);die;
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

    private function __getIdTipoMonedaid($pais)
    {
        switch ($pais) {
            case 'es':
                return 1;
                break;
            case 'mx':
                return 2;
                break;
            default:
                return 1;
        }
    }
}
