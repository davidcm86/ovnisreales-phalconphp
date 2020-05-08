<?php

use Phalcon\Cli\Task;

class ProductosTask extends Task
{
    /**
     * Cada 24h generamos al azar las rebajas y así tb movemos el orden de los productos
     */
    public function rebajasAction()
    {
        $idiomas = ['es', 'mx'];
        foreach ($idiomas as $idioma) {
            $phql = 'SELECT * FROM OvnisReales\Models\Productos as Productos 
            WHERE Productos.activo = 1';
            $manager = $this->modelsManager;
            $productos = $manager->executeQuery($phql);
            foreach ($productos as $producto) {
                $producto->es_rebajado = rand(0,1);
                $producto->update();
            }
            $this->modelsCache->delete('categorias-productos-' . $idioma);
        }
    }
}