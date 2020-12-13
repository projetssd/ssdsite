<?php
require_once __DIR__ . '/../includes/header.php';
$service = new service($_GET['service']);
// on lance la fonction restart qui va rendre la main presque tout de suite
// on ne peut faire aucun contrôle dessus
$service->restart();
