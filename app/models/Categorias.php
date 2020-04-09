<?php

namespace OvnisReales\Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;

class Categorias extends \Phalcon\Mvc\Model
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
    public $nombre;

    /**
     *
     * @var string
     */
    public $slug;

    /**
     *
     * @var string
     */
    public $pais;

    /**
     *
     * @var string
     */
    public $imagen;

    /**
     *
     * @var string
     */
    public $title_seo;

    /**
     *
     * @var string
     */
    public $description_seo;

    /**
     *
     * @var string
     */
    public $descripcion_principal;

    /**
     *
     * @var string
     */
    public $descripcion_secundaria;

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
     * @var string
     */
    public $keywords;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'nombre',
            new PresenceOf([
                'model'   => $this,
                'message' => 'El Nombre es obligatorio.',
            ])
        );

        $validator->add(
            "nombre",
            new Uniqueness(
                [
                    'model'   => $this,
                    "message" => "Ya existe un nombre igual, utiliza otro.",
                ]
            )
        );

        $validator->add(
            'title_seo',
            new PresenceOf([
                'model'   => $this,
                'message' => 'El Título SEO es obligatorio.',
            ])
        );

        $validator->add(
            'description_seo',
            new PresenceOf([
                'model'   => $this,
                'message' => 'El Descripción SEO es obligatoria.',
            ])
        );

        $validator->add(
            'keywords',
            new PresenceOf([
                'model'   => $this,
                'message' => 'Las Keywords son obligatorias.',
            ])
        );

        $validator->add(
            'imagen',
            new PresenceOf([
                'model'   => $this,
                'message' => 'Debes introducir una imagen.'
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
        $this->setSource("categorias");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'categorias';
    }

    public function beforeCreate()
    {
        $this->created = date('Y-m-d H:i:s');
    }


    public function beforeUpdate()
    {
        $this->modified = date('Y-m-d H:i:s');
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Categorias[]|Categorias|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Categorias|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
