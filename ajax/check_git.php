<?php
require_once __DIR__ . '/../includes/header.php';

$git = new git;
$test_maj = $git->test_update();

echo json_encode($test_maj);