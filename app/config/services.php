<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as LogFileAdapter;
use Phalcon\Breadcrumbs;
use Phalcon\Cache\Frontend\Data as FrontendData;
use Phalcon\Cache\Backend\Memcache as BackendMemcache;

use OvnisReales\Utils\Truncate;
use OvnisReales\Utils\Slug;

// Set the models cache service
$di->setShared(
    'modelsCache',
    function () {
        // Cache data for one day (default setting)
        $frontCache = new FrontendData(
            [
                'lifetime' => 43200,
            ]
        );
        // Memcached connection settings
        $cache = new BackendMemcache(
            $frontCache,
            [
                'host' => 'localhost',
                'port' => '11211'
            ]
        );
        return $cache;
    }
);

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set(
    'url',
    function() {
        $url = new \Phalcon\Mvc\Url();
        $url->setBaseUri('/');
        return $url;
});
$di->set('Mpdf', function () {
    return new \Mpdf\Mpdf();
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();
            $volt = new VoltEngine($view, $this);
            $volt->setOptions([
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_',
                'compileAlways' => (ENVIRONMENT == 'development' ? true : false),
            ]);
            $compiler = $volt->getCompiler();
            $compiler->addFunction('strtotime', 'strtotime'); // añadimos la funcion strtotime a volt para formatear el data
            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Inicio librerias
 */
$di->set('Truncate', function () {
    return new Truncate();
});
$di->set('Slug', function () {
    return new Slug();
});
$di->setShared('Breadcrumbs', function () {
    return new Breadcrumbs;
});
 /**
  * Fin librerias
  */

/**
 * Inicio plugins
 */
$di->set(
    'AuthPlugin',
    function() {
        $AuthPlugin = new AuthPlugin;
        return $AuthPlugin;
});
$di->set(
    'ImagenesPlugin',
    function() {
        $ImagenesPlugin = new ImagenesPlugin;
        return $ImagenesPlugin;
});
/**
 * Fin plugins
 */

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});


$di->set('flashSession', function () {
    return new FlashSession([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});


$dispatcher    = new \Phalcon\Mvc\Dispatcher();
$eventsManager = new \Phalcon\Events\Manager();

// errores 404 página no encontrada
$eventsManager->attach("dispatch", function ($event, $dispatcher, $exception) use ($di) {

    if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward([
                        'controller' => 'errores',
                        'action' => 'notFound',
                    ]);
                    return false;
                default:
                    $dispatcher->forward([
                        'controller' => 'errores',
                        'action' => 'internalError',
                    ]);
                return false;
            }
        }

    });

$dispatcher->setEventsManager($eventsManager);
$di->setShared('dispatcher', $dispatcher);

/**
 * Recogemos los css para sacarlos minificados
 */
$di->setShared('assets', function() {
    $manager = new Phalcon\Assets\Manager();
    $manager->collection('localCss')
    ->setTargetPath('css/layout.min.css')
    ->setTargetUri('css/layout.min.css')
    ->addCss('css/layout.css')
    ->addFilter(new Phalcon\Assets\Filters\Cssmin());
    return $manager;
});

$di->set('modelsManager',function() {
        return new ModelsManager();
    }
);

$di->set("logger", function () {
    $config = $this->getConfig();
    return new LogFileAdapter($config->application->logPath);
});


$dispatcher    = new \Phalcon\Mvc\Dispatcher();
$eventsManager = new \Phalcon\Events\Manager();

// errores 404 página no encontrada
$eventsManager->attach("dispatch", function ($event, $dispatcher, $exception) use ($di) {

    if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward([
                        'controller' => 'errores',
                        'action' => 'notFound',
                    ]);
                    return false;
                default:
                    $dispatcher->forward([
                        'controller' => 'errores',
                        'action' => 'internalError',
                    ]);
                return false;
            }
        }

    });

$dispatcher->setEventsManager($eventsManager);
$dispatcher->setDefaultNamespace('OvnisReales\Controllers');
$di->setShared('dispatcher', $dispatcher);