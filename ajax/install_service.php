<?php

require_once '../php/classes/service.php';
$service = new service($_GET['service']);
$subdomain = $_GET['subdomain'];
echo "Service = " . $_GET['service'] . " et subdomain = " . $subdomain;
// on lance la fonction install qui va rendre la main presque tout de suite
// on ne peut faire aucun contrôle dessus
$service->install($subdomain);
echo 'ok';

