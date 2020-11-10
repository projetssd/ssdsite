<?php
require_once "../php/classes/service.php";
$service = new service($_GET['service']);
// on lance la fonction install qui va stocker le log dans une variable $log de la classe service
$service->install();
echo $service->log;
