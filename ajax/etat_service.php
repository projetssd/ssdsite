<?php
require_once __DIR__ . '/../includes/header.php';


/* on charge l'objet service correspondant à ce qui est passé
   en url : https://mondomain.com/check_service.php?service=radarr
*/
$service = new service($_GET['service']);
$public_url = false;
$installed = false;
$running = false;
$version = '';

if($service->is_installed())
{
    $installed = true;
    $public_url = $service->get_public_url();
    if($service->check())
    {
        $running = true;
        $version = $service->get_version();
    }
}

$tab_retour = array(
    "running" => $running,
    "installed" => $installed,
    "public_url" => $public_url,
    "version" => $version
    );
echo json_encode($tab_retour);
