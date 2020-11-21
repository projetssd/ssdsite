<?php
require_once "../php/classes/service.php";

/* on charge l'objet service correspondant à ce qui est passé
   en url : https://mondomain.com/check_service.php?service=radarr
*/
$service = new service($_GET['service']);

$tab_retour = array(
    "running" => $service->check(),
    "installed" => $service->is_installed()
    );
echo json_encode($tab_retour);
