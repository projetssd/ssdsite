<?php

require_once __DIR__ . '/../includes/header.php';

$plexident = $_POST['plexident'];
$plexpass = $_POST['plexpass'];

$mserver = new plex($plexident);
$mserver->create_plex($plexpass);
echo 'ok';
