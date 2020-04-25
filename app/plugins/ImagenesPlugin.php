<?php
use OvnisReales\Models\Categorias;

use Phalcon\Mvc\User\Plugin;
use Phalcon\Http\Response;

use OvnisReales\Classes\ResizeClass;

class ImagenesPlugin extends Plugin
{
    public function uploadGenericoMultiple($rutaImagen, $nombreArchivo = '', $rutaImagenBd = '')
    {
        if ($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();
            foreach ($files as $file) {
                if ($this->extensionesPermitidas($file->getExtension())) {
                    $nombreFichero = $nombreArchivo . '.' . $file->getExtension();
                    if (!file_exists($rutaImagen)) {
                        mkdir($rutaImagen, 0777, true);
                        chmod($rutaImagen, 0777);
                    }
                    $rutaSalvarBd = $rutaImagen . $nombreFichero;
                    if (!empty($rutaImagenBd) && file_exists($rutaImagenBd)) unlink($rutaImagenBd);
                    $salvarRutaImagen = $rutaImagen . '/' . $nombreFichero;
                    $salvarRutaImagen = str_replace('//', '/', $salvarRutaImagen);
                    $file->moveTo($salvarRutaImagen);
                    $this->generarImagenesTamanios($salvarRutaImagen);
                    chmod($salvarRutaImagen, 0777);
                    $returnRutaBD = explode('/public', $rutaSalvarBd);
                    return $returnRutaBD[1];
                }
            }
        }
        return;
    }

    /**
     * Obtenemos la imagen data de una url y la copiamos en la ruta que queramos
     */
    public function copiarImagenUrl($rutaImagen, $dataImagen, $nombreProducto)
    {
        if (!file_exists($rutaImagen)) {
            mkdir($rutaImagen, 0777, true);
            chmod($rutaImagen, 0777);
        }
        file_put_contents($rutaImagen . '/' . $nombreProducto, $dataImagen);
        $this->generarImagenesTamanios($rutaImagen);

    }

    public function extensionesPermitidas($extension)
    {
        $extensiones = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
        if (in_array($extension, $extensiones)) return true;
        return false;
    }

    /**
     * quitarExtension
     * Quitamos la extension del nombre de la imagen para luego hacer slug
     *
     * @param  mixed $nombreImagen
     *
     * @return void
     */
    public function quitarExtension($nombreImagen)
    {
        $extensiones = ['.jpg', '.jpeg', '.gif', '.png', '.svg'];
        $nombreImagen = str_replace($extensiones, '', $nombreImagen);
        return $nombreImagen;
    }

     /**
     * Creamos distintops tamaños de imagenes gracias a la class Resize
     */
    public function generarImagenesTamanios($rutaImagenAbsoluta)
    {
        $rutaImagenAbsoluta = str_replace('//', '/', $rutaImagenAbsoluta);
        if (file_exists($rutaImagenAbsoluta)) {
            $resizeObj = new ResizeClass($rutaImagenAbsoluta);
            $resizeObj -> resizeImage(450, 300, 'crop');
            $resizeObj -> saveImage($rutaImagenAbsoluta, 90);
        }
    }
}
?>