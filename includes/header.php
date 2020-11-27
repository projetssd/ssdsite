<?php
/**
 * Fichier header
 * Doit être appelé avant chaque page, y compris les ajax
 * Permet de définir tout ce qui est commun au site et de charger les classes
 * automatiquement
 */




$prod = false;
if (isset($_GET['prod'])) {
    $prod = $_GET['prod'];
}
if(file_exists(__DIR__ . '/../PROD'))
{
    $prod = true;
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
