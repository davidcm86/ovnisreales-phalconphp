<?php
    define('ENVIRONMENT', isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'development');
    defined('SALT') || define('SALT', 'dj587hfm0n2f56n072dm892');
    $variableEntorno = (ENVIRONMENT == 'development') ? 1 : -1;
    if (ENVIRONMENT == 'development') {
        defined('RUTA_ARRAYS') || define('RUTA_ARRAYS', APP_PATH . '/config/arrays/');
    } else {
        defined('RUTA_ARRAYS') || define('RUTA_ARRAYS', '/var/www/html/ovnisreales/app/config/arrays/');
    }
    switch (ENVIRONMENT) {
        case 'development':
            switch ($_SERVER['SERVER_NAME']) {
                case 'mx.ovnisreales.loc':
                    defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.loc');
                    defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                    defined('IDIOMA') || define('IDIOMA', 'mx-MX');
                    defined('IDIOMA_OG') || define('IDIOMA_OG', 'mx_MX');
                    break;
                case 'es.ovnisreales.loc':
                    defined('DOMINIO') || define('DOMINIO', 'https://www.es.ovnisreales.loc');
                    defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'es');
                    defined('IDIOMA') || define('IDIOMA', 'es-ES');
                    defined('IDIOMA_OG') || define('IDIOMA_OG', 'es_ES');
                    break;
                case 'ovnisreales.loc':
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
                                defined('IDIOMA') || define('IDIOMA', 'es-ES');
                                defined('IDIOMA_OG') || define('IDIOMA_OG', 'es_ES');
                                defined('ANALYTICS_CODE') || define('ANALYTICS_CODE', 'UA-166797175-1');
                                return $di->get('response')->redirect('https://www.es.ovnisreales.loc');
                                break;
                            case 'MX':
                                defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.loc');
                                defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                                defined('IDIOMA') || define('IDIOMA', 'mx-MX');
                                defined('IDIOMA_OG') || define('IDIOMA_OG', 'mx_MX');
                                defined('ANALYTICS_CODE') || define('ANALYTICS_CODE', 'UA-166797175-2');
                                return $di->get('response')->redirect('https://www.mx.ovnisreales.loc');
                                break;
                            default:
                                defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.loc');
                                defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                                defined('IDIOMA') || define('IDIOMA', 'mx-MX');
                                defined('IDIOMA_OG') || define('IDIOMA_OG', 'mx_MX');
                                defined('ANALYTICS_CODE') || define('ANALYTICS_CODE', 'UA-166797175-2');
                                return $di->get('response')->redirect('https://www.mx.ovnisreales.loc');
                        }
                    } else  {
                        defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.loc');
                        defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                        defined('IDIOMA') || define('IDIOMA', 'mx-MX');
                        defined('IDIOMA_OG') || define('IDIOMA_OG', 'mx_MX');
                        defined('ANALYTICS_CODE') || define('ANALYTICS_CODE', 'UA-166797175-2');
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
            switch ($_SERVER['SERVER_NAME']) {
                case 'mx.ovnisreales.com':
                    defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.com');
                    defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                    defined('IDIOMA') || define('IDIOMA', 'mx-MX');
                    defined('IDIOMA_OG') || define('IDIOMA_OG', 'mx_MX');
                    defined('ANALYTICS_CODE') || define('ANALYTICS_CODE', 'UA-166797175-2');
                    break;
                case 'es.ovnisreales.com':
                    defined('DOMINIO') || define('DOMINIO', 'https://www.es.ovnisreales.com');
                    defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'es');
                    defined('IDIOMA') || define('IDIOMA', 'es-ES');
                    defined('IDIOMA_OG') || define('IDIOMA_OG', 'es_ES');
                    defined('ANALYTICS_CODE') || define('ANALYTICS_CODE', 'UA-166797175-1');
                    break;
                case 'ovnisreales.com':
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
                    $urlIp = "https://www.iplocate.io/api/lookup/" . $ip;
                    $result = file_get_contents($urlIp);
                    if (!empty($result)) {
                        $resultDecode = json_decode($result);
                        switch ($resultDecode->country_code) {
                            case 'ES':
                                defined('DOMINIO') || define('DOMINIO', 'https://www.es.ovnisreales.com');
                                defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'es');
                                defined('IDIOMA') || define('IDIOMA', 'es-ES');
                                defined('IDIOMA_OG') || define('IDIOMA_OG', 'es_ES');
                                defined('ANALYTICS_CODE') || define('ANALYTICS_CODE', 'UA-166797175-1');
                                return $di->get('response')->redirect('https://www.es.ovnisreales.com');
                                break;
                            case 'MX':
                                defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.com');
                                defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                                defined('IDIOMA') || define('IDIOMA', 'mx-MX');
                                defined('IDIOMA_OG') || define('IDIOMA_OG', 'mx_MX');
                                defined('ANALYTICS_CODE') || define('ANALYTICS_CODE', 'UA-166797175-2');
                                return $di->get('response')->redirect('https://www.mx.ovnisreales.com');
                                break;
                            default:
                                defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.com');
                                defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                                defined('IDIOMA') || define('IDIOMA', 'mx-MX');
                                defined('IDIOMA_OG') || define('IDIOMA_OG', 'mx_MX');
                                defined('ANALYTICS_CODE') || define('ANALYTICS_CODE', 'UA-166797175-2');
                                return $di->get('response')->redirect('https://www.mx.ovnisreales.com');
                        }
                    } else  {
                        defined('DOMINIO') || define('DOMINIO', 'https://www.mx.ovnisreales.com');
                        defined('DOMINIO_SELECT') || define('DOMINIO_SELECT', 'mx');
                        defined('IDIOMA') || define('IDIOMA', 'mx-MX');
                        defined('IDIOMA_OG') || define('IDIOMA_OG', 'mx_MX');
                        defined('ANALYTICS_CODE') || define('ANALYTICS_CODE', 'UA-166797175-2');
                        return $di->get('response')->redirect('https://www.mx.ovnisreales.com');
                    }
                    break;
            }
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            ini_set('log_errors', '2'); 
            $debug = new \Phalcon\Debug(); // modo debug 
            $debug->listen();
            break;
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