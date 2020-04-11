<?php
namespace OvnisReales\Classes;
use OvnisReales\Classes\MiddleWareViewClass;
use Phalcon\Events\Event;
use \zz\Html\HTMLMinify;

class MiddleWareViewClass {
    public $metaData = array();
    public static $instance = null;

    /**
     * @return MyMetaData
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new MiddleWareViewClass();
        }
        return self::$instance;
    }

    public function __construct() {}

    public function minifyHtml() {

    }

    public function afterRender(Event $event, $view)
    {
        //$router = new \Phalcon\Mvc\Router;
        //$logger = new \Phalcon\Logger\Adapter\File(BASE_PATH . '/tmp/logs/error.log');
        $out = $view->getContent();
        $minify = HTMLMinify::minify($out);
        $view->setContent($minify);
    }
}