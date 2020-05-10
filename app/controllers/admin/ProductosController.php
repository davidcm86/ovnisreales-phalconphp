<?php
namespace OvnisReales\Controllers\Admin;

use OvnisReales\Models\Productos;
use OvnisReales\Models\Categorias;

use Phalcon\Http\Response;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use Phalcon\Mvc\Model\Criteria;

require_once BASE_PATH . '/vendor/simple-html-dom/simple-html-dom/simple_html_dom.php';

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
        if ($this->request->isPost()) {
            $producto = new Productos();
            $producto->nombre_producto = $this->request->getPost('nombre_producto');
            $producto->tipo_moneda_id = $this->__getIdTipoMonedaid($this->idiomaAdmin);
            $producto->categoria_id = $this->request->getPost('categoria_id');
            $producto->precio = $this->request->getPost('precio');
            $producto->es_rebajado = rand(0, 1);
            $producto->activo = 1;
            $producto->enlace = $this->request->getPost('enlace');
            if (!empty($producto->enlace)) $producto = $this->__insertarDataProductoFromUrl($producto);
            $files = $this->request->getUploadedFiles();
            if (isset($files[0]) && !empty($files[0]->getName())) $producto->imagen = "validation-true";
            if (!empty($producto->imagen) && !empty($producto->precio)) {
                if ($producto->save()) {
                    $rutaImagen = BASE_PATH . '/public/images/productos/' . $this->idiomaAdmin . '/' . Categorias::getSlugCategoria($producto->categoria_id) . '/';
                    $rutaImagenBd = $this->ImagenesPlugin->uploadGenericoMultiple($rutaImagen, $this->Slug->generate($producto->nombre_producto));
                    if (!empty($rutaImagenBd)) {
                        $producto->imagen = $rutaImagenBd;
                        $producto->update();
                    }
                    $this->flashSession->success("El producto ha sido creada correctamente.");
                    $this->response->redirect('/admin/productos/crear');
                } else {
                    $messagesError = [];
                    foreach ($producto->getMessages() as $message) {
                        $messagesError[] = $message . '</br>';
                    }
                    $this->view->messagesError = $messagesError;
                }
            } else {
                if (empty($producto->imagen)) $messagesError[] = 'No se ha guardado la imagen</br>';
                if (empty($producto->precio)) $messagesError[] = 'No se ha guardado el precio</br>';
                $this->view->messagesError = $messagesError;
            }
        }
        $this->view->categorias = Categorias::find(['conditions' => 'pais = "'.$this->idiomaAdmin.'"']);
    }

    public function editarAction($id)
    {
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
                $rutaImagen = BASE_PATH . '/public/images/productos/' . $this->idiomaAdmin . '/' . Categorias::getSlugCategoria($producto->categoria_id) . '/';
                $rutaImagenBuscar = BASE_PATH . '/public/' . $producto->imagen;
                $rutaImagenBd = $this->ImagenesPlugin->uploadGenericoMultiple($rutaImagen, $this->Slug->generate($producto->nombre_producto), $rutaImagenBuscar);
                if (!empty($rutaImagenBd)) {
                    $producto->imagen = $rutaImagenBd;
                    $producto->update();
                }
                $this->flashSession->success("El producto ha sido editado correctamente.");
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

        /**
     * Obtenemos los datos del producto de amazon mediante la url. Debido a un error al obtener la url, tengo que
     * copiar el html a un fichero para leer de ahí.
     */
    private function __insertarDataProductoFromUrl($producto)
    {
        $instance = new \simple_html_dom();
        $html = file_get_contents($producto->enlace);
        $html = preg_replace("[\n|\r|\n\r]", "", $html);
        // escribimos le html en un fichero
        $rutaArchivoTmp = BASE_PATH . '/tmp/archivo.html';
        $myfile = fopen($rutaArchivoTmp, "w") or die("Unable to open file!");
        fwrite($myfile,  preg_replace("[\n|\r|\n\r]", "", trim($html)));
        fclose($myfile);
        // obtenemos el html para coger el DOM
        $html = file_get_html($rutaArchivoTmp);
        if (method_exists($html,"find")) {
            // then check if the html element exists to avoid trying to parse non-html
            if ($html->find('html')) {
                foreach($html->find('span#price_inside_buybox') as $element) {
                    if (!empty($element->plaintext)) {
                        $precio = $element->plaintext;
                        $precio = strip_tags($precio);
                        $precio = str_replace('€', '', $precio);
                        $precio = str_replace('$', '', $precio);
                        $precio = str_replace(',', '.', $precio);
                        $precio = str_replace(' ', '', $precio);
                        $precio = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $precio); // limpia caracteres ascii que no se ven y en mi caso es un espacio
                        if (substr_count($precio, '.') == 2) {
                            $precioExplode = explode('.', $precio);
                            $precio = $precioExplode[0] . $precioExplode[1] . '.' . $precioExplode[2];
                        }
                        $producto->precio = $precio;
                    }
                 }
                foreach($html->find('span#priceblock_ourprice') as $element) {
                    if (!empty($element->plaintext)) {
                        $precio = $element->plaintext;
                        $precio = strip_tags($precio);
                        $precio = str_replace('€', '', $precio);
                        $precio = str_replace('$', '', $precio);
                        $precio = str_replace(',', '.', $precio);
                        $precio = str_replace(' ', '', $precio);
                        $precio = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $precio); // limpia caracteres ascii que no se ven y en mi caso es un espacio
                        if (substr_count($precio, '.') == 2) {
                            $precioExplode = explode('.', $precio);
                            $precio = $precioExplode[0] . $precioExplode[1] . '.' . $precioExplode[2];
                        }
                        $producto->precio = $precio;
                    }
                }
                    
                foreach($html->find('div[class=imgTagWrapper]') as $element) {
                    foreach($element->find('img') as $img)
                    {
                        if (!empty($img->src)) {
                            $nombreProducto = trim($producto->nombre_producto);
                            $nombreProducto = $this->Slug->generate($nombreProducto) . '.jpg';
                            $rutaBd = '/images/productos/' . $this->idiomaAdmin . '/' . Categorias::getSlugCategoria($producto->categoria_id) . '/' . $nombreProducto;
                            $rutaFileSystem = BASE_PATH . '/public/images/productos/' . $this->idiomaAdmin . '/' . Categorias::getSlugCategoria($producto->categoria_id);
                            $this->ImagenesPlugin->copiarImagenUrl($rutaFileSystem, file_get_contents($img->src), $nombreProducto);
                            $producto->imagen = $rutaBd;
                        }
                    }
                }
            }
       }
       if (!file_exists($$rutaArchivoTmp)) unlink($rutaArchivoTmp);
       return $producto;
    }
}