<?php

require 'includes/header.php';


// espace disque
$df = disk_free_space('/');
$dt = disk_total_space('/');
$du = $dt - $df;
$dt = ceil($dt/1024/1024/1024);
$free_disk = ceil($du/1024/1024/1024);

// ip
$ip = $_SERVER['REMOTE_ADDR'];

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
$applis = $service->get_all($tableau_appli, false);

// javascripts  utilisés
$js = array(
    'jquery.min.js',
    'bootstrap.bundle.min.js',
    'adminlte.min.js',
    'toastr.min.js',
    'sweetalert2.min.js',
    'ssd_specific.js'
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
'ASSET_JS' => $asset_js
]);
