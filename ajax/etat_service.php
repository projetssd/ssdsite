<?php
require_once "../php/classes/service.php";

/* on charge l'objet service correspondant à ce qui est passé
   en url : https://mondomain.com/check_service.php?service=radarr
*/
$service = new service($_GET['service']);


$running = false;
if ($service->check()) {
    $running = true;
}

$installed = false;
if ($service->is_installed()) {
    $installed = true;
}
$tab_retour = array(
    "running" => $running,
    "installed" => $installed);
echo json_encode($tab_retour);
