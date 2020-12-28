<?php

require_once __DIR__ . '/../includes/header.php';

$emailcloud = $_POST['emailcloud'];
$apicloud = $_POST['apicloud'];

$cloud = new cloudflare($emailcloud);
$cloud->function_cloudflare($apicloud);
echo 'ok';
