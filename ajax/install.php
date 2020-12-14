<?php
shell_exec('sudo ' . __DIR__ . '/../scripts/manage_service.sh seedbox 2>&1 | tee -a ' . __DIR__ . '/../logtail/log 2>/dev/null >/dev/null &');
echo 'ok';
?>
