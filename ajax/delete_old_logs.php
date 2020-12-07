<?php
require_once __DIR__ . '/../includes/header.php';

$command = 'find ' . __DIR__ . '/../logs -type f -mtime +7 -exec rm -f {} \;'; 
// on ne lance que dans 5% des appels, pour ne pas lancer la suppression tout le temps pour rien
if(rand(1,100) < 5)
{
    shell_exec($command);
    die('ok');
}
die('no');