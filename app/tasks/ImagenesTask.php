<?php

use Phalcon\Cli\Task;

use OvnisReales\Classes\ResizeClass;

class ImagenesTask extends Task
{

    public function redimensionarresizeAction()
    {
        $phql = 'SELECT * FROM OvnisReales\Models\Productos as Productos WHERE imagen_tratada = 0';
        $manager = $this->modelsManager;
        $productos = $manager->executeQuery($phql);
        foreach ($productos as $producto) {
            echo $producto->id;
            $rutaImagenAbsoluta = str_replace('//', '/', $producto->imagen);
            $rutaImagenAbsoluta = BASE_PATH . '/public' . $rutaImagenAbsoluta;
            if (file_exists($rutaImagenAbsoluta)) {
                $resizeObj = new ResizeClass($rutaImagenAbsoluta);
                // dependiendo de la anchura y altura de la imagen, se utiliza un resize u otro
                $widthImagen = $resizeObj->width;
                $heightImagen = $resizeObj->height;
                if ($widthImagen >= $heightImagen) {
                    // es más ancha que larga
                    $widthImagen = 270;
                    $heightImagen = 180;
                } else {
                    // es más larga que ancha
                    $widthImagen = 180;
                    $heightImagen = 270;
                }
                $resizeObj -> resizeImage($widthImagen, $heightImagen, 'auto');
                $resizeObj -> saveImage($rutaImagenAbsoluta, 90);
                $phql = 'UPDATE OvnisReales\Models\Productos SET imagen_tratada = 1 WHERE id = ' . $producto->id;
                $manager = $this->modelsManager;
                $manager->executeQuery($phql);
                sleep('3');
            }
        }
    }


    //var $tamanos = array(''=>500);
    //var $ruta = BASE_PATH_TASK . '/public/imagenes/contenidos/';
    /**
     * Redimensionamos las imagenes ya creadas
     */
    public function redimensionarAction()
    {
        $phql = 'SELECT Contenidos.id as id_contenido, Imagenes.ruta as ruta_imagen, Imagenes.id as id_imagen, Contenidos.slug as slug_contenido
        FROM Menopausia\Models\Contenidos as Contenidos 
        INNER JOIN Menopausia\Models\Imagenes as Imagenes ON Imagenes.id = Contenidos.imagen_id 
        WHERE Imagenes.tratada = 0';
        $manager = $this->modelsManager;
        $imagenes = $manager->executeQuery($phql);
        foreach ($imagenes as $imagen) {
            if ($imagen->ruta_imagen != '') {
                $rutaImagenAnterior = $imagen->ruta_imagen;
                $this->comprueboruta($this->generoRutaId($imagen->id_contenido));
                $this->crearFicheros($imagen->id_contenido,'/' . $imagen->ruta_imagen, $imagen->id_imagen);
            }
            if ($this->modelsCache->get('contenido-slug-' . $imagen->slug_contenido)) $this->modelsCache->delete('contenido-slug-' . $imagen->slug_contenido);
            $phqlUpdate = 'UPDATE \Menopausia\Models\Imagenes SET tratada = 1 WHERE id = ' . $imagen->id_imagen;
            $this->modelsManager->executeQuery($phqlUpdate);
            // borro anterior imagen
            $rutaImagenBorrarAnterior = BASE_PATH_TASK . '/public' . $rutaImagenAnterior;
            if (file_exists($rutaImagenBorrarAnterior)) unlink($rutaImagenBorrarAnterior);
            sleep('2');
        }
		$this->modelsCache->delete('home-contenidos');
    }

    /* 
     * Genera los ficheros nuevos a partir de la imagen de la bd y el id
     * Crea los diferentes ficheros optimizados. 
     */
    private function crearFicheros($id,$nombre, $idImagen) {
        if ($nombre!='') {
            $array = explode('.', $nombre);
            $rutaGeneradaUpdate = $this->generoRutaId($id);
            $ruta_ima=$this->ruta.$rutaGeneradaUpdate;
            $imagen_final=str_replace('//','/',$ruta_ima);
            $ext = mb_strtolower(end($array));
            if (file_exists($imagen_final.$id.'.'.$ext)) {
                chmod($imagen_final.$id.'.'.$ext, 0777);
                if ($this->optimizoImagen($imagen_final.$id.'.'.$ext,$ext)) {
                    foreach($this->tamanos as $nomb=>$tam) {
                        $this->redimensionoImagen($imagen_final.$id.'.'.$ext,$nomb,$tam,$ext);
                    }
                }
            } else {
                $rutaArchivo = str_replace("//", "/", BASE_PATH_TASK . '/public' . $nombre);
                $rutaArchivo = str_replace("//", "/", $rutaArchivo);
                if (copy($rutaArchivo,$imagen_final.$id.'.'.$ext)) {
                    chmod($imagen_final.$id.'.'.$ext, 0777);
                    if ($this->optimizoImagen($imagen_final.$id.'.'.$ext,$ext)) {
                        foreach($this->tamanos as $nomb=>$tam) {
                            $this->redimensionoImagen($imagen_final.$id.'.'.$ext,$nomb,$tam,$ext);
                        }
                    }
                }
            }
            // pongo el nombre correcto de la ruta para el nombre
            $rutaNombreNuevo = '/imagenes/contenidos/' . $rutaGeneradaUpdate . $id. '.' . $ext;
            $phqlUpdateImagen = 'UPDATE \Menopausia\Models\Imagenes SET ruta = "'.$rutaNombreNuevo.'" WHERE id = ' . $idImagen;
            $this->modelsManager->executeQuery($phqlUpdateImagen);
        }
    }

    /* Genera la imagen con el tamaÃ±o que se le pase y optimiza la imagen
     * 
     */
    private function redimensionoImagen($fichero,$nombre,$x,$ext) {
        $thumb = new Imagick();
        $thumb->readImage($fichero);
        $tmpX=$thumb->getImageWidth();
        if ($tmpX!=$x) {
            if ($ext=='gif' or $ext=='png') {
                $thumb->setImageOpacity(1.0);
            }
            $thumb->resizeImage($x,0,Imagick::FILTER_LANCZOS,1);
            if ($nombre!='') {
                $nombreFinal=str_replace('.'.$ext,'_'.$nombre.'.'.$ext,$fichero);
            } else
                $nombreFinal=$fichero;
                $thumb->writeImage($nombreFinal);
                $this->optimizoImagen($nombreFinal, $ext);
        }
        $thumb->clear();
        $thumb->destroy();
    }

    /* 
     *Optimiza la imagen pasandole ImageMin
     */
    private function optimizoImagen($fichero,$ext) {        
        $fOptimizado=str_replace('.'.$ext,'opt.'.$ext,$fichero);
        exec('imagemin ' . $fichero.' > '. $fOptimizado);
        if (file_exists($fOptimizado)) {
            unlink($fichero);
            rename($fOptimizado,$fichero);
            chmod($fichero, 0766);
            return true;
        } else {
            return false;
        }
    }

    /* Comprueba si exite la ruta donde se va a escribir las imagenes nuevas
     * si no exite la crea
     * si existe no hace nada
     */
    private function comprueboruta($id) {
        $base=$this->ruta;
        if (is_dir($base.'/'.$id)) {
            return true;
        } else {            
            mkdir($base.$id,0777,true);
        }
    }

    /* Genero la ruta desde el id  */
    private function generoRutaId($id) {
        $ruta_imagen='';
        for ($f=0;$f<mb_strlen($id);$f++) {
            $ruta_imagen .=$id[$f] ."/";
        }
        return $ruta_imagen;
    }
}