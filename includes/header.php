<?php
/**
 * Fichier header
 * Doit être appelé avant chaque page, y compris les ajax
 * Permet de définir tout ce qui est commun au site et de charger les classes
 * automatiquement
 */


/**
 * On charge la conf
 */
require_once __DIR__ . '/../conf.php';


// initialisation de twig (morteur de template)
require __DIR__ . '/../vendor/autoload.php';
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, ['debug' => true,]);
$twig->addExtension(new \Twig\Extension\DebugExtension());

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
