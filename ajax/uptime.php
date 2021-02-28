<?php
require_once __DIR__ . '/../includes/header.php';
$locale = 'fr_FR.UTF-8';
setlocale(LC_ALL, $locale);
putenv('LC_ALL='.$locale);
$data = trim(shell_exec('who -b'));
$uptime = explode(' ', $data);
$uptime = $uptime[3].' '.$uptime[4];
$newdate = str_replace("-","",$uptime);

$date = new DateTime($newdate);
$dateformat =  $date->format('d/m/Y');
$heureformat = $date->format("H:i:s");

echo $dateformat. ' ' . $heureformat;


