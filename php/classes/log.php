<?php

/**
 * Class log
 * Permet de gérer les logs, en lecture et écriture
 */
class log
{
    public function writelog($message, $type='INFO', $display = false)
    {
        $date        = date('d/m/Y H:i:s');
        $datefichier = date('Ymd');
        if(!error_log("PHP : " . $date . " - " . $type . " - " . $message . PHP_EOL, 3, __DIR__ . '/../../logs/ssdsite-' .
                                                                                    $datefichier .
                                                                                    '.log'))
        {
            die("erreur d'écriture de fichier");
        }
    }
    
    
    public function get_logs()
    {
        if(!$tabfichiers = scandir(__DIR__ . '/../../logs'))
        {
            return array();
        }
        $retour = array();
        foreach($tabfichiers as $val)
        {
            // on ne prend pas les fichiers qui commencent par un point
            if(substr($val,0,1) !== '.')
            {
                // on ne prend pas les logs qui commencent par  ssdsite
                if(substr($val,0,7) !== 'ssdsite')
                {
                    $temp = explode('.',$val);
                    $tabexplode = explode('-',$temp[0]);
                    $retour[] = array(
                        "nomfichier" => $val,
                        "date" =>$tabexplode[0],
                        "heure" => $tabexplode[1],
                        "action" => $tabexplode[2],
                        "appli" => $tabexplode[3]
                        );
                }

            }
            
           
        }
        return $retour;
        
    }
}