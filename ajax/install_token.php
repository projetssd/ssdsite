<?php

require_once __DIR__ . '/../includes/header.php';

$token = $_POST['token'];
$drive = $_POST['drive'];
$drivename = $_POST['drivename'];

$rclone = new client($token);
$rclone->createtoken($drive, $drivename);
echo 'ok';

