<?php
use Phalcon\Di\FactoryDefault;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
// se comenta el try para dejar el debug de phalcon
try {

    $di = new FactoryDefault();

    include APP_PATH . '/config/environment.php';
    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */

    /**
     * Handle routes
     */
    include APP_PATH . '/config/router.php';

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo str_replace(["\n","\r","\t"], '', $application->handle()->getContent());

} catch (\Exception $e) {
    $logger = new \Phalcon\Logger\Adapter\File(BASE_PATH . '/tmp/logs/error.log');
    $message = get_class($e) . ": " . $e->getMessage() . "\n" . " File=" . $e->getFile() . "\n" . " Line=" . $e->getLine() . "\n" . $e->getTraceAsString() . "\n";
    $logger->critical($message);
}
