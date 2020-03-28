<?php

namespace Ovnisreales\Models;

class EstadisticaProductos extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $producto_id;

    /**
     *
     * @var integer
     */
    public $contador;

    /**
     *
     * @var integer
     */
    public $mes;

    /**
     *
     * @var integer
     */
    public $anio;

    /**
     *
     * @var string
     */
    public $created;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("ovnisreales");
        $this->setSource("estadistica_productos");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'estadistica_productos';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return EstadisticaProductos[]|EstadisticaProductos|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return EstadisticaProductos|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function saveEstadistica($productoId, $mes, $anio)
    {
        $parameters['conditions'] = "producto_id = " . $productoId . " AND mes = " . $mes . " AND anio = " . $anio;
        $estadistica = parent::findFirst($parameters);
        if (!isset($estadistica->id)) {
            $estadistica = new EstadisticaProductos();
            $estadistica->producto_id = $productoId;
            $estadistica->contador = 1;
            $estadistica->mes = $mes;
            $estadistica->anio = $anio;
            $estadistica->created = date('Y-m-d');
            $estadistica->save();
        } else {
            $estadistica->contador = $estadistica->contador +1 ;
            $estadistica->mes = $mes;
            $estadistica->anio = $anio;
            $estadistica->update();
        }
        return;
    }

}
