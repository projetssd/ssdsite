<?php
require_once "../php/classes/service.php";

/* on charge l'objet service correspondant à ce qui est passé
   en url : https://mondomain.com/check_service.php?service=radarr
*/
$service = new service($_GET['service']);


if ($service->running) {
    echo "ok";
} else {
    echo "bad";
}
