<?php
require_once __DIR__ . '/../includes/header.php';
$logs = new log;
$meslogs = $logs->get_logs();

echo json_encode($meslogs);