<?php

/**
 * 实例化service
 * @param string $class
 * @param string $baseUrl
 * @param type $ext
 * @return Service 
 */
function service($class, $baseUrl = '', $ext = '.class.php') {
    if (empty($baseUrl))
        $baseUrl = SERVICE_PATH;
    $class = $class . 'Service';
    $class_namespace = 'Api\\Service\\'.$class;
    import($class, $baseUrl, $ext);
    $service   =   class_exists($class) ? new $class : new $class_namespace;
    return $service;
}

?>
