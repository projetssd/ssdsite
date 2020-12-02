<?php

/**
 * Class log
 * Permet de gérer les logs, en lecture et écriture
 */
class log
{
    public function writelog($message, $type='INFO', $display = false)
    {
        $date = date('d/m/Y H:i:s');
        $datefichier = date('Ymd');
        error_log($date . " - " . $type . " - " . $message . PHP_EOL,3,__DIR__ . '/../../logs/ssdsite-' . $datefichier .
                                                                    '.log');
    }
}