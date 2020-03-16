<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->pluginsDir,
        $config->application->formsDir,
    ]
);

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader
    ->registerNamespaces(
        [
            'Ovnisreales\Controllers' => $config->application->controllersDir,
            'Ovnisreales\Models' => $config->application->modelsDir,
            'Ovnisreales' => $config->application->libraryDir,
        ]
    );

if (file_exists(BASE_PATH . '/app/vendor/autoload.php')) {    
    require_once BASE_PATH . '/app/vendor/autoload.php';
}
/**
 * Register the custom loader (if any)
 */
if (file_exists(BASE_PATH . '/vendor/phalcon/autoload.php')) {
    require_once BASE_PATH . '/vendor/phalcon/autoload.php';
}

$loader->register();