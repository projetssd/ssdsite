<?php
$locale = 'fr_FR.UTF-8';
setlocale(LC_ALL, $locale);
putenv('LC_ALL='.$locale);
$data = shell_exec('who -b');
$uptime = explode(' démarrage système ', $data);
$uptime = $uptime[0].' '.$uptime[1];
echo(''.$uptime.'');
