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
    
    
    public function get_logs($max_files = 5)
    {
        $current_file = 0;
        // on prend les fichiers par ordre alpha inverse
        // ce qui permet d'avoir les plus anciens en premier
        if(!$tabfichiers = scandir(__DIR__ . '/../../logs',SCANDIR_SORT_DESCENDING))
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
                    $date = new DateTime($tabexplode[0] . " " . $tabexplode[1]);
                    $dateformat =  $date->format('d/m/Y');
                    $heureformat = $date->format("H:i:s");
                    $retour[] = array(
                        "nomfichier" => $val,
                        "date" => $dateformat,
                        "heure" => $heureformat,
                        "action" => $tabexplode[2],
                        "appli" => $tabexplode[3]
                    );
                    $current_file ++;
                    if($current_file >= $max_files)
                    {
                        // on s'arrête là
                        return $retour;
                    }
                }

            }
            
           
        }
        return $retour;
        
    }
    
    public function detail_log($logfile)
    {
        $retour = '';
        $tab = file(__DIR__ . '/../../logs/' . $logfile);
        foreach($tab as $val)
        {
            $retour .= $val . "<br />";
        }
        return $retour;
    }
}