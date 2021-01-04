<?php
require_once __DIR__ . '/../includes/header.php';


$df = disk_free_space('/');
$dt = disk_total_space('/');
$du = $dt - $df;

$percent = round(($df/$dt)*100,2); 

$df = ceil($df/1024/1024/1024);
echo $df.' Go (' . $percent . '%)';
