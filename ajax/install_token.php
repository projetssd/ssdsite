<?php

require_once __DIR__ . '/../includes/header.php';

$token = $_GET['token'];
$drive = $_GET['drive'];
$drivename = $_GET['drivename'];

$rclone = new client($token);
$rclone->createtoken($drive, $drivename);
echo 'ok';

