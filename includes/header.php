<?php
/**
 * Fichier header
 * Doit être appelé avant chaque page, y compris les ajax
 * Permet de définir tout ce qui est commun au site et de charger les classes
 * automatiquement
 */

$prod = true;
if (isset($_GET['dev'])) {
    $prod = $_GET['dev'];
}
if(file_exists(__DIR__ . '/../DEV'))
{
    $prod = false;
}

$mode_debug = false;
if (isset($_GET['debug'])) {
    $mode_debug = $_GET['debug'];
}
if(file_exists(__DIR__ . '/../DEBUG'))
{
    $mode_debug = true;
}


// initialisation de twig (morteur de template)
require __DIR__ . '/../vendor/autoload.php';
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
if ($prod) {
    $twig = new \Twig\Environment($loader, ['cache' => __DIR__ .'/../cache']);
} else {
    $twig = new \Twig\Environment($loader, ['debug' => true,]);
    $twig->addExtension(new \Twig\Extension\DebugExtension());
}

$debugbar_js = '';
$debugbarrender = '';
if($mode_debug)
{
    $debugbar = new DebugBar\StandardDebugBar();
    //$debugbar->addCollector(new DebugBar\DataCollector\ExceptionsCollector());
    
   
    
    $data = array('foo' => 'bar','toto' => 'tata',"titi" => array('steph' => "toito","oiuoi" => "oiuoiu"));
    $data2 = $data;
    $debugbar->addCollector(new DebugBar\DataCollector\ConfigCollector($data));
    //$debugbar->addCollector(new DebugBar\DataCollector\ConfigCollector($data2));
    
   
    //$debugbar = new StandardDebugBar();
    $debugbarRenderer = $debugbar->getJavascriptRenderer();
    $debugbar_js = $debugbarRenderer->renderHead();
    

}




/**
 * Autochargement des classes manquantes
 */
function my_autoloader($class)
{
    if (file_exists(__DIR__ . '/../php/classes/' . $class . '.php')) {
        require_once __DIR__ . '/../php/classes/' . $class . '.php';
    }
}
spl_autoload_register('my_autoloader');
