<?php

require_once __DIR__."/constant.php";
require_once BASEPATH."vendor/autoload.php";

// ---------------------------------------------------
// @TODO:50 move to somewhere better
function array_merge_recursive_ex(array & $array1, array & $array2)
{
    $merged = $array1;

    foreach ($array2 as $key => & $value)
    {
        if (is_array($value) && isset($merged[$key]) && is_array($merged[$key]))
        {
            $merged[$key] = array_merge_recursive_ex($merged[$key], $value);
        } else if (is_numeric($key))
        {
             if (!in_array($value, $merged))
                $merged[] = $value;
        } else
            $merged[$key] = $value;
    }

    return $merged;
}

// --------------------------------------------------------------------

$config = require(APPPATH.'config.php');

if (is_file(APPPATH.'config.local.php')) {
    $localConfig = include(APPPATH.'config.local.php');
    $config = array_merge_recursive_ex($config, $localConfig);
}

// ---------------------------------------------------

$container = new Slim\Container($config);

$app = new Slim\App($container);

// attach dependencies
$di = require(APPPATH.'di.php');

foreach ($di as $name => $callable) {
    $container[$name] = $callable;
}

return $app;
