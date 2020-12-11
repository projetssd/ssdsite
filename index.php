<?php

require 'includes/header.php';
if($mode_debug)
{
    $debugbar['time']->startMeasure('monindex', 'Chargement index');
}


$tableau_appli = [
    'rutorrent',
    'radarr',
    'sonarr',
    'medusa',
    'jackett',
    'lidarr',
    'sensorr',
    'emby'];
    
$service = new service('all');
$tab_installed = $service->get_installed_appli();
$applis = $service->get_all($tab_installed);
if($mode_debug)
{
    $debugbar['messages']->addMessage("On a chargé les éléments " . print_r($applis,true));
    $debugbar['messages']->addMessage($applis);
}


// javascripts  utilisés
$js = array(
    'https://code.jquery.com/jquery-3.5.1.min.js',
    'bootstrap.bundle.min.js',
    'adminlte.min.js',
    'toastr.min.js',
    'sweetalert2.min.js',
    'ssd_specific.js',
    'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.js',
    'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/localization/messages_fr.min.js',
    'changetheme.js',
    'navigation_modal.js',
    'get_logs.js',
    'test_git.js'
);
    
// css utilisés
$css = array(
    "all.min.css",
    "https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css",
    "adminlte.min.css",
    "toastr.min.css",
    "ssd.css",);


$asset_css = '';
$asset_js = '';
if ($prod) {
    $config = [
        'pipeline' => 'auto',
        'public_dir' => __DIR__ ,
        'css_dir' => 'dist/css',
        'js_dir' => 'dist/js'
    ];
    $assets = new \Stolz\Assets\Manager($config);
    $assets->add($css);
    $assets->add($js);
    $asset_css = $assets->css();
    $asset_js = $assets->js();
}

if($mode_debug)
{
    $debugbar['time']->stopMeasure('monindex');
}

/**
 * PLUS DE DEBUG BAR APRES CA !!
 */
if($mode_debug)
{
    $debugbarrender = $debugbarRenderer->render();
}



// maintenant qu'on a toutes les variables, on appelle le bon template, en mettant les variables dedans
$template = $twig->load('index.twig');
echo $template->render(['IP' => $ip,
'TITRE' => 'Gestion du serveur SSD',
'FREE_DISK' => $free_disk,
'UPTIME'=> $uptime,
'SERVER_NAME' => $server_name,
'APPLIS' => $applis,
'JS' => $js,
'CSS' => $css,
'PROD' => $prod,
'ASSET_CSS' => $asset_css,
'ASSET_JS' => $asset_js,
'DEBUGBARJS' => $debugbar_js,
'DEBUGBARRENDER' => $debugbarrender
]);
