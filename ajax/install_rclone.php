<?php

require_once __DIR__ . '/../includes/header.php';

$client = $_GET['client'];
$secret = $_GET['secret'];

$rclone = new client($client);
$rclone->credential($secret);
echo 'ok';

