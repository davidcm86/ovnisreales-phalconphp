<?php

use Phalcon\Cache\Frontend\Data as FrontendData;
use Phalcon\Cache\Backend\Memcache as BackendMemcache;

$router = $di->getRouter();
$router->add('/admin/:controller/:action/:params', [
    'namespace'  => 'OvnisReales\Controllers\Admin',
    'controller' => 1,
    'action'     => 2,
    'params'     => 3,
]);
$router->add('/admin/:controller', [
    'namespace'  => 'OvnisReales\Controllers\Admin',
    'controller' => 1
]);
$frontCache = new FrontendData(
    [
        'lifetime' => 86400
    ]
);
// Memcached connection settings
$cache = new BackendMemcache(
    $frontCache,
    [
        'host' => 'localhost',
        'port' => '11211',
    ]
);

// categorias disponibles
if (!$cache->exists('Categorias' . DOMINIO_SELECT)) {
    $cache->save('Categorias' . DOMINIO_SELECT,json_decode(file_get_contents(RUTA_ARRAYS."/Categorias-".DOMINIO_SELECT.".ini.php"),true));
}
$categorias = $cache->get('Categorias' . DOMINIO_SELECT);
if (isset($categorias[DOMINIO_SELECT])) {
    $categorias = implode('|',array_keys($categorias[DOMINIO_SELECT]));
} else {
    $categorias = '';
}
// categorias
$router->add(
    '/('.$categorias.')',
    [
        'controller'        => 'categorias',
        'action'            => 'listar',
        'categoriaSlug'     => 1
    ]
);
$router->handle();
