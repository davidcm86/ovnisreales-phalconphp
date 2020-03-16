<?php

namespace Ovnisreales\Models;

class Productos extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $nombre_producto;

    /**
     *
     * @var integer
     */
    public $es_rebajado;

    /**
     *
     * @var string
     */
    public $imagen;

    /**
     *
     * @var string
     */
    public $modified;

    /**
     *
     * @var string
     */
    public $created;

    /**
     *
     * @var integer
     */
    public $activo;

    /**
     *
     * @var double
     */
    public $precio;

    /**
     *
     * @var integer
     */
    public $tipo_moneda_id;

    /**
     *
     * @var string
     */
    public $enlace;

    /**
     *
     * @var integer
     */
    public $categoria_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("ovnisreales");
        $this->setSource("productos");
        $this->belongsTo('tipo_moneda_id', 'Ovnisreales\Models\TipoMonedas', 'id', ['alias' => 'TipoMonedas']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'productos';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Productos[]|Productos|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Productos|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
