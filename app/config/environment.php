<?php
    define('ENVIRONMENT', isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'development');
    $variableEntorno = (ENVIRONMENT == 'development') ? 1 : -1;
    if (ENVIRONMENT == 'development') {
        defined('RUTA_ARRAYS') || define('RUTA_ARRAYS', APP_PATH . '/config/arrays/');
    } else {
        defined('RUTA_ARRAYS') || define('RUTA_ARRAYS', '/var/www/html/ovnisreales/config/arrays');
    }
    switch (ENVIRONMENT) {
        case 'development':
            switch ($_SERVER['SERVER_NAME']) {
                case 'mx.ovnisreales.loc':
                    defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.loc');
                    defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                    defined('IDIOMA') || define('IDIOMA', 'mx-MX');
                    break;
                case 'es.ovnisreales.loc':
                    defined('DOMINIO') || define('DOMINIO', 'https://www.es.ovnisreales.loc');
                    defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'es');
                    defined('IDIOMA') || define('IDIOMA', 'es-ES');
                    break;
                case 'ovnisreales.loc':
                    //  TODO: mirar la ip para ver de donde es, si no hay mandarle a mx. Ver como puedo hacer esto beautify. Parece q no me dija functions aqui
                    if (ENVIRONMENT == 'development') {
                        $ip = '2.141.83.233';
                    } else {
                        // Get real visitor IP behind CloudFlare network
                        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                                $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                                $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                        }
                        $client  = @$_SERVER['HTTP_CLIENT_IP'];
                        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
                        $remote  = $_SERVER['REMOTE_ADDR'];

                        if(filter_var($client, FILTER_VALIDATE_IP)) {
                            $ip = $client;
                        } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
                            $ip = $forward;
                        } else {
                            $ip = $remote;
                        }
                    }
                    if (ENVIRONMENT == 'development') {
                        $ip = '2.141.83.233';
                    }
                    $urlIp = "https://www.iplocate.io/api/lookup/" . $ip;
                    $result = file_get_contents($urlIp);
                    if (!empty($result)) {
                        $resultDecode = json_decode($result);
                        switch ($resultDecode->country_code) {
                            case 'ES':
                                defined('DOMINIO') || define('DOMINIO', 'https://www.es.ovnisreales.loc');
                                defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'es');
                                return $di->get('response')->redirect('https://www.es.ovnisreales.loc');
                                break;
                            case 'MX':
                                defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.loc');
                                defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                                return $di->get('response')->redirect('https://www.mx.ovnisreales.loc');
                                break;
                            default:
                                defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.loc');
                                defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                                return $di->get('response')->redirect('https://www.mx.ovnisreales.loc');
                        }
                    } else  {
                        defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.loc');
                        defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                        return $di->get('response')->redirect('https://www.mx.ovnisreales.loc');
                    }
                    break;
            }
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            ini_set('log_errors', '2'); 
            $debug = new \Phalcon\Debug(); // modo debug 
            $debug->listen();
            break;
        case 'production':
            /*define('DOMINIO', 'https://www.calcubaby.com');
            ini_set('display_errors', 0);
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
            break;*/
        default:
            header('HTTP/1.1 503 Service Unavailable.', true, 503);
            echo 'The application environment is not set correctly.';
            exit(1); // EXIT_ERROR
    }
    // cogemos los errores y si estamos en local los pintamos en pantalla, en pro solo van al log
    set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) {
        $logger = new \Phalcon\Logger\Adapter\File(BASE_PATH.'/tmp/logs/error.log');
        $msg = "[$errno] $errstr ; \n on line $errline in file $errfile \n";
        switch ($errno) {
            case E_USER_ERROR:
                $logger->critical("fatal error: ". $msg);
                break;
            case E_USER_WARNING:
                $logger->warning("warning error: ". $msg);
                break;
            case E_USER_NOTICE:
                $logger->notice("notice error: ". $msg);
                break;
            default:
                $logger->notice("notice error: ". $msg);
                break;
        }
    }, $variableEntorno); // en desarrollo mostramos error en pantalla, en pro no


    function getUserIP() {
        if (ENVIRONMENT == 'development') {
            $ip = '2.141.83.233';
        } else {
            // Get real visitor IP behind CloudFlare network
            if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                    $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                    $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            }
            $client  = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $remote  = $_SERVER['REMOTE_ADDR'];

            if(filter_var($client, FILTER_VALIDATE_IP)) {
                $ip = $client;
            } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
                $ip = $forward;
            } else {
                $ip = $remote;
            }
        }
        return $ip;
    }

    function getLocationFromIp($ip) {
        if (ENVIRONMENT == 'development') {
            $ip = '2.141.83.233';
        }
        $urlIp = "https://www.iplocate.io/api/lookup/" . $ip;
        $result = file_get_contents($urlIp);
        return $result;
    }