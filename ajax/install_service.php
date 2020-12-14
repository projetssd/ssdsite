<?php

require_once __DIR__ . '/../includes/header.php';


$service = new service($_GET['service']);
$subdomain = $_GET['subdomain'];
// on lance la fonction install qui va rendre la main presque tout de suite
// on ne peut faire aucun contrôle dessus
$service->install($subdomain);
echo 'ok';

