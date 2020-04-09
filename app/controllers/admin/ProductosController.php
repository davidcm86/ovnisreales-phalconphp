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
        try {
            $producto = Productos::findFirst($id);
            if (!$producto) {
                $this->flashSession->error("Producto no encontrado.");
                $this->response->redirect('/admin/productos');
                return;
            }
            if ($this->request->isPost()) {
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
                    $rutaImagenBuscar = BASE_PATH . '/public/' . $producto->imagen;
                    $rutaImagenBd = $this->ImagenesPlugin->uploadGenericoMultiple($rutaImagen, $this->Slug->generate($producto->nombre_producto), $rutaImagenBuscar);
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
            } else {
                $this->view->imagen = $producto->imagen;
                $this->tag->setDefault("id", $producto->id);
                $this->tag->setDefault("nombre_producto", $producto->nombre_producto);
                $this->tag->setDefault("categoria_id", $producto->categoria_id);
                $this->tag->setDefault("precio", $producto->precio);
                $this->tag->setDefault("es_rebajado", $producto->es_rebajado);
                $this->tag->setDefault("activo", $producto->activo);
                $this->tag->setDefault("enlace", $producto->enlace);   
            }
            $this->view->id = $id;
            $this->view->categorias = Categorias::find(['conditions' => 'pais = "'.$this->idiomaAdmin.'"']);
        } catch (\Exception $e) {
            $message = get_class($e) . ": " . $e->getMessage() . "\n" . " File=" . $e->getFile() . "\n" . " Line=" . $e->getLine() . "\n" . $e->getTraceAsString() . "\n";
        }
    }

    public function deleteAction($id)
    {
        $this->view->disable();
        $producto = Productos::findFirstByid($id);
        if (!$producto) {
            $this->flashSession->error("El producto no ha sido encontrada.");
            $this->response->redirect('/admin/productos');
            return;
        }
        $rutaImagenBorrar = BASE_PATH . '/public' . $producto->imagen;
        if (file_exists($rutaImagenBorrar)) unlink($rutaImagenBorrar);
        if (!$producto->delete()) {
            $messagesError = '';
            foreach ($producto->getMessages() as $message) {
                $messagesError .= $message;
            }
            $this->flashSession->success($messagesError);
            $this->response->redirect('/admin/productos');
            return;
        }
        $this->flashSession->success("El producto ha sido borrada correctamente.");
        $this->response->redirect('/admin/productos');
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