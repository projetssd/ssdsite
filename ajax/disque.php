<?php
require_once __DIR__ . '/../includes/header.php';


$df = disk_free_space('/');
$dt = disk_total_space('/');
$du = $dt - $df;
 
$dt = ceil($dt/1024/1024/1024);
$du = ceil($du/1024/1024/1024);
echo $du.' Go ';
