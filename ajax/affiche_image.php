<?php

require_once __DIR__ . '/../includes/header.php';

$image = new images($_GET['appli']);
$image->affiche_image();