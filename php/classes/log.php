<?php

/**
 * Class log
 * Permet de gérer les logs, en lecture et écriture
 */
class log
{
    /**
     * @var false|string Datedu log
     */
    private $datelog;

    /**
     * log constructor.
     * Construit une date qui sera utilisée plusieurs fois pour les logs
     */
    public function __construct()
    {
        $this->datelog = date('Ymd-His');
    }

    /**
     * @param string $message Le message à loguer
     * @param string $type letype de log (INFO, DEBUG, ...)
     * Ecrit dans le fichier log
     */
    public function writelog($message, $type = 'INFO')
    {
        $date        = date('d/m/Y H:i:s');
        $datefichier = date('Ymd');
        if (!error_log("PHP : " . $date . "-" . $type . "-" . $message . PHP_EOL, 3, __DIR__ . '/../../logs/ssdsite-' .
                                                                                     $datefichier .
                                                                                     '.log'))
        {
            die("erreur d'écriture de fichier");
        }
    }

    /**
     * @param string $message Le message à loguer
     * @param string $appli L'appli concernée
     * @param string $type Le type de log (DEBUG, INFO, ...)
     * Ecrit dans le log sépcifique d'une appli
     */
    public function writelogappli($message, $appli, $type)
    {
        $fichier = __DIR__ . '/../../logs/' . $this->datelog . ' - ' . $type . '-' . $appli . '.log';
        error_log($message . PHP_EOL, 3, $fichier);
    }


    /**
     * @param int $max_files Le nombre max de fichier à retourner
     * @return array La liste des fichier logs
     * @throws Exception
     * Va chercher les max_files derniers fichiers de logs
     */
    public function get_logs($max_files = 5)
    {
        $current_file = 0;
        // on prend les fichiers par ordre alpha inverse
        // ce qui permet d'avoir les plus anciens en premier
        if (!$tabfichiers = scandir(__DIR__ . '/../../logs', SCANDIR_SORT_DESCENDING))
        {
            return array();
        }
        $retour = array();
        foreach ($tabfichiers as $val)
        {
            // on ne prend pas les fichiers qui commencent par un point
            // on ne prend pas les logs qui commencent par  ssdsite
            if ((substr($val, 0, 1) !== '.') && (substr($val, 0, 7) !== 'ssdsite'))
            {
                try
                {
                    $temp        = explode('.', $val);
                    $tabexplode  = explode('-', $temp[0]);
                    $date        = new DateTime($tabexplode[0] . " " . $tabexplode[1]);
                    $dateformat  = $date->format('d/m/Y');
                    $heureformat = $date->format("H:i:s");
                    $retour[]    = array(
                        "nomfichier" => $val,
                        "nomcourt"   => $temp[1],
                        "date"       => $dateformat,
                        "heure"      => $heureformat,
                        "action"     => $tabexplode[2],
                        "appli"      => $tabexplode[3]
                    );
                    $current_file++;
                }
                catch (Exception $e)
                {
                    // on n'arrive pas à calculer la date
                    ;
                }
                if ($current_file >= $max_files)
                {
                    // on s'arrête là
                    return $retour;
                }
            }
        }
        return $retour;
    }

    /**
     * @param string $logfile Le fichier de log à lire
     * @return string Le contenu du log
     * Lit un fichier de log
     */
    public function detail_log($logfile)
    {
        $retour = '';
        $tab    = file(__DIR__ . '/../../logs/' . $logfile);
        foreach ($tab as $val)
        {
            $retour .= $val;
        }
        return $retour;
    }
}