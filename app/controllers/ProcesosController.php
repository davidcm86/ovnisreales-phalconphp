<?php
namespace Ovnisreales\Controllers;

class ProcesosController extends ControllerBase
{
    /**
     * Se generar tipos de contenido, categorias y subcategorias
     */
    public function generoArraysAction($pass = '')
    {
        if (empty($pass) || $pass != '3d6g45g5jh34f') die;
		echo "Inicio";
		if (!file_exists(RUTA_ARRAYS)) mkdir(RUTA_ARRAYS, 0777, true);
        $this->view->disable();
		$datos=array(
		    'Categorias'
        );
        $idiomas = ['es', 'mx'];
        foreach ($idiomas as $idioma) {
            $normal = [];
            $query = $this->modelsManager->createQuery('SELECT * FROM Ovnisreales\Models\Categorias WHERE pais = "' . $idioma .'"');
            $arrTabla  = $query->execute()->toArray();
            foreach($arrTabla as $item) {
                //$normal[$idioma][$item['id']]['nombre'] = $item['nombre'];
                //$normal[$idioma][$item['id']]['slug'] = $item['slug'];
                $normal[$idioma][$item['slug']] = $item['id'];
            }
            $fich=fopen(RUTA_ARRAYS . 'Categorias-'.$idioma.'.ini.php','w');
            fwrite($fich,json_encode($normal));
            fclose($fich);
        }
		echo "Fin ok";
	}
}
