<?php

namespace OvnisReales\Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;

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
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'nombre_producto',
            new PresenceOf([
                'model'   => $this,
                'message' => 'El Nombre es obligatorio.',
            ])
        );

        $validator->add(
            'nombre_producto',
            new Uniqueness([
                'model'   => $this,
                'message' => 'El Nombre no puede estar repetido.',
            ])
        );

        $validator->add(
            'precio',
            new PresenceOf([
                'model'   => $this,
                'message' => 'El Precio es obligatorio.',
            ])
        );

        $validator->add(
            'enlace',
            new PresenceOf([
                'model'   => $this,
                'message' => 'El Enlace es obligatorio.',
            ])
        );

        $validator->add(
            "enlace",
            new Uniqueness(
                [
                    'model'   => $this,
                    "message" => "Ya existe un Enlace igual, utiliza otro.",
                ]
            )
        );

        $validator->add(
            'categoria_id',
            new PresenceOf([
                'model'   => $this,
                'message' => 'La categoría es obligatoria.',
            ])
        );

        return $this->validate($validator);
    }
    
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("ovnisreales");
        $this->setSource("productos");
        $this->belongsTo('tipo_moneda_id', 'OvnisReales\Models\TipoMonedas', 'id', ['alias' => 'TipoMonedas']);
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

    public function beforeCreate()
    {
        $this->created = date('Y-m-d H:i:s');
    }

    public function beforeUpdate()
    {
        $this->modified = date('Y-m-d H:i:s');
    }

}
