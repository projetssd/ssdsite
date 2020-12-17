<?php

require_once __DIR__ . '/../includes/header.php';

$client = $_POST['client'];
$secret = $_POST['secret'];

$rclone = new client($client);
$rclone->credential($secret);
echo 'ok';

