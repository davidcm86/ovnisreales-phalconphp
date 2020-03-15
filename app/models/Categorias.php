<?php

namespace Ovnisreales\Models;

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
    public $descripcion_secundaria;

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
