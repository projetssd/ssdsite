<?php
$data = shell_exec('lsb_release -d');
$uptime = explode(" ", $data);
echo substr($data, 12);
