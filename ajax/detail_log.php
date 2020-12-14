<?php
require_once __DIR__ . '/../includes/header.php';
$logs = new log;
$detaillogs = $logs->detail_log($_GET['logfile']);
echo $detaillogs;
