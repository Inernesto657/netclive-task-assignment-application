<?php

/**
 * ob_start to allow headers to be sent 
 * without affecting other outputs.
 */
ob_start();

/**
 * class autoloader
 */
spl_autoload_register(function($class){

    set_include_path(dirname(__DIR__));

    // imports the called class if it exists; 
    if(file_exists(get_include_path() . "/{$class}.php")){

        require_once(get_include_path() . "/{$class}.php");
    }else{

        // else redirect to the 404 Not Found page;
        header("Location: ../Views/page_not_found.php");
    }
});

/**
 * instanciate the Boot Class
 */
(new Core\Boot());

/**
 * Url query strigs
 * @var $url
 */
$url = htmlspecialchars($_SERVER['QUERY_STRING']);

(new Core\Router($url))->processUrlCall();

?>