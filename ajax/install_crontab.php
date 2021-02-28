<?php

require_once __DIR__ . '/../includes/header.php';


$service = new service($_POST['service']);
$cron = $_POST['cron'];

// on lance la fonction crontab qui va rendre la main presque tout de suite
// on ne peut faire aucun contrôle dessus
$service->crontab($cron);
echo 'ok';

