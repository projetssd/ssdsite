<?php
require_once "../php/classes/service.php";
$service = new service($_GET['service']);
// on lance la fonction stop qui va rendre la main presque tout de suite
// on ne peut faire aucun contrôle dessus
$service->stop();