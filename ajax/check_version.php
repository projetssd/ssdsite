<?php
require_once "../php/classes/service.php";

/* on charge l'objet service correspondant à ce qui est passé
   en url : https://mondomain.com/check_service.php?service=radarr
*/
$service = new service($_GET['service']);
if ($service->installed) {
    if ($service->running) {
        echo $service->get_version();
    } else {
        echo "Service non démarré";
    }
} else {
    echo "Service non installé";
}
