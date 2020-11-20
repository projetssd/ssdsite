<?php

require 'includes/header.php';

// on prépare les variables pour la suite
// server_name
$data = shell_exec('lsb_release -d');
$uptime = explode(" ", $data);
$server_name = substr($data, 12);

//uptime
$locale = 'fr_FR.UTF-8';
setlocale(LC_ALL, $locale);
putenv('LC_ALL='.$locale);
$data = shell_exec('who -b');
$uptime = explode(' démarrage système ', $data);
$uptime = $uptime[0].' '.$uptime[1];

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


// maintenant qu'on a toutes les variables, on appelle le bon template, en mettant les variables dedans
$template = $twig->load('index.twig');
echo $template->render(['IP' => $ip,
'TITRE' => 'Gestion du serveur SSD',
'FREE_DISK' => $free_disk,
'UPTIME'=> $uptime,
'SERVER_NAME' => $server_name,
'APPLIS' => $applis
]);
