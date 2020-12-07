<?php
require_once __DIR__ . '/../includes/header.php';

/* on charge l'objet service correspondant à ce qui est passé
   en url : https://mondomain.com/check_service.php?service=radarr
*/
$service = new service($_GET['service']);
if ($service->is_installed()) {
    if ($service->check()) {
        echo $service->get_version();
    } else {
        echo "Service non démarré";
    }
} else {
    echo "Service non installé";
}
