<?php

/**
 * Class service
 * Classe permettant la manipulation des services (applis).
 */
class service
{
    /**
     * @var string Url à checker pour que le service tourne
     */
    public $url = '';
    /**
     * @var string Nom de lappli
     */
    public $display_name;
    /**
     * @var string Ligne de commande pour installer
     */
    public $command_install = '';
    /**
     * @var string Ligne de commande pour désinstaller
     */
    public $command_uninstall = '';
    /**
     * @var string Ligne de commande pour redémarrer
     */
    public $command_restart = '';
    /**
     * @var string Ligne de commande pour arrêter une appli
     */
    public $command_stop = '';
    /**
     * @var string Code html formatté pour afficher une "vignette" d'appli
     */
    public $display_text;
    /**
     * @var bool Est-ce que l'appli est installée ?
     */
    public $installed;
    /**
     * @var bool Est-ce que l'appli tourne ?
     */
    public $running;
    /**
     * @var text url cliquable
     */
    public $public_url;
    /**
     * @var text Numéro de version
     */
    public $version;
    /**
     * @var integer Port utilisé par l'appli
     */
    public $port;
    /**
     * @var text Adresse ip du container
     */
    public $host;


    /**
     * service constructor.
     *
     * @param $my_service String Nom de l'appli (radarr, sonarr, ...)
     * Cas particulier sur all qui permet d'init la classe sans appli associée
     */
    public function __construct($my_service)
    {
        /*******************************************************
         * Ici on va charger les infos spécifiques à un service
         * Si le service n'est pas présent, on ferme la page
         * C'est ce service qui sera utilisé dans toutes les fonctions qui suivent
         */
        //
        // on commence par mettre tout ce qui es générique
        //
        $logfile                 = '/var/www/seedboxdocker.website/logtail/log';
        $this->display_name      = trim($my_service); // on supprimer les espaces avant/après
        $this->command_install   =
            'rm ' . $logfile . '; sudo -u www-data /var/www/seedboxdocker.website/scripts/install.sh ' . $this->display_name . ' 2>&1 | tee -a ' . $logfile . ' 2>/dev/null >/dev/null &';
        $this->command_uninstall =
            'rm ' . $logfile . '; echo 0 | sudo tee /opt/seedbox/status/' . $this->display_name . '; sudo -u root sudo -u root docker rm -f ' . $this->display_name . ' 2>&1 >/dev/null &';
        $this->command_restart   =
            'rm ' . $logfile . '; sudo -u root docker restart ' . $this->display_name . ' 2>&1 | tee -a ' . $logfile . ' 2>/dev/null >/dev/null &';
        $this->command_stop      =
            'rm ' . $logfile . '; sudo -u root docker stop ' . $this->display_name . ' 2>&1 | tee -a' . $logfile . ' 2>/dev/null >/dev/null &';
        $this->command_start     =
            'rm ' . $logfile . '; sudo -u root docker start ' . $this->display_name . ' 2>&1 | tee -a ' . $logfile . 'g 2>/dev/null >/dev/null &';
        //
        // on va chercher l'ip du docker
        //
        $retour =
            exec("docker inspect -f '{{.NetworkSettings.Networks.bridge.IPAddress}}' " . $this->display_name, $tab_retour, $code_retour);
        if ($code_retour == 0) {
            $this->url  = 'http://' . $retour;
            $this->host = $retour;
        } else {
            // docker non trouvé, on met l'ip locale histoire de ne pas laisser vide
            $this->url = 'http://127.0.0.1';
        }

        // ici on va surcharger ce qui n'est pas générique
        switch ($my_service) {
            case 'radarr':
                $this->port = 7878;
                break;
            case 'sonarr':
                $this->port = 8989;
                break;
            case 'rutorrent':
                $this->port = 8080;
                break;
            case 'lidarr':
                $this->port = 8686;
                break;
            case 'medusa':
                $this->port = 8081;
                break;
            case 'jackett':
                $this->port = 9117;
                break;
            case 'sensorr':
                $this->port = 443;
                break;
            case 'emby':
                $this->port = 8096;
                break;
            case 'all':
                // cas particulier pour charger toutes les cases possibles
                // ne sert qu'à construire un tableau qu'on utilisera plus tard
                // je mets juste ce case pour ne pas bloquer
                break;
            default:
                die('Service non disponible');
        }
        $this->url = "http://" . $this->host . ":" . $this->port;

        if ($my_service != 'all') {
            // on va remplir les valeurs par défaut
            //$this->running    = $this->check();
            //$this->installed  = $this->is_installed();
            $this->public_url = false;
            // on récupère les url publiques
            // on lit le fichier
            $file    = fopen('/opt/seedbox/resume', 'r');
            $matches = array();
            if ($file) {
                while (!feof($file)) {
                    $buffer = fgets($file);
                    if (strpos($buffer, $this->display_name) !== false) {
                        $matches[] = $buffer;
                    }
                }
                fclose($file);
            }
            if (count($matches) != 0) {
                $tab_temp         = explode('=', $matches[0]);
                $this->public_url = trim($tab_temp[1]);
            }
        }
    }

    /**
     * Teste si le service est installé.
     *
     * @return bool true si installé
     */
    public function is_installed()
    {
        /**
         * Chemin du fichier qui contient les infos.
         */
        $filename = '/opt/seedbox/status/' . $this->display_name;

        $installed = false; // par défaut, on considère que le service n'est pas là

        $contents = ''; // init de variable pour l'IDE
        if (file_exists($filename)) {
            $file     = fopen($filename, 'r');
            $contents = fread($file, filesize($filename));
        }
        $contents = substr($contents, 0); // on ne garde que le premier caractère

        switch ($contents) {
            case 0:
                // pas installé
                break;
            case 1:
                // en cours d'install
                break;
            case 2:
                // déjà installé
                $installed = true;
                break;
            case 3:
                // en cours de désinstall
                break;
            default:
                break;
        }

        $this->installed = $installed;

        return $installed;
    }

    /**
     * Fonction qui permet de check si un service tourne, via un appel curl.
     *
     * @return bool true si running
     */
    public function check()
    {
        if ($this->is_installed()) {
            $connection = fsockopen($this->host, $this->port, $errno, $errstr);
            if (!$connection) {
                echo "<!-- " . $this->host . " - " . $this->port . " - " . "$errstr ($errno)" . "-->";
            } else {
                if (is_resource($connection)) {
                    fclose($connection);
                    return true;
                }
            }
        }
       


        return false;
    }

    /**
     * Récupère le code html ou la sortie de page de l'url en entrée
     * @param $url text URl à récupérer
     * @return bool|string Code html de sortie
     */
    public function get_html($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/toto');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/toto');

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12');
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }

    /**
     * Sort le numéro de version de l'appli en cours
     * @return mixed|string|string[]
     */
    public function get_version()
    {
        if (!$this->check()) {
            return ' container arrêté';
        }
        switch ($this->display_name) {
            case 'radarr':
            case 'sonarr':
            case 'lidarr':
                $html = $this->get_html($this->url . '/initialize.js');

                $html = explode('=', $html);
                $html = $html[1];
                $html = str_replace('{', '', $html);
                $html = str_replace('}', '', $html);
                $html = explode(',', $html);
                foreach ($html as $val) {
                    $detail = explode(':', $val);
                    if (trim($detail[0]) == 'version') {
                        $version = str_replace("'", "", $detail[1]);
                    }
                }

                break;

            case 'rutorrent':
                $version = 'image communauté Mondedié';
                break;
            case 'medusa':
                $result  = shell_exec("docker inspect -f '{{.Config.Labels.build_version}}' medusa");
                $temp    = explode('-', $result);
                $version = $temp[1];
                break;

            case 'jackett':

                $html    = $this->get_html($this->url . '/api/v2.0/server/config');
                $json    = json_decode($html, true);
                $version = $json['app_version'];
                break;
        }

        if (empty($version)) {
            return 'Version inconnue';
        }
        return $version;
    }

    /**
     * Installe l'appli de la classe en cours.
     *
     * @return bool always true
     */
    public function install()
    {
        /* la commande d'install se termine par un & et donc rend la main tout de suite
        impossible de catcher la sortie
        on ne stocke donc aucune info
        les infos seront lues dans le défilement des logs */

        shell_exec($this->command_install);

        return true;
    }

    /**
     * Désinstalle l'appli de la classe en cours.
     *
     * @return bool always true
     */
    public function uninstall()
    {
        shell_exec($this->command_uninstall);

        return true;
    }

    /**
     * Restarte l'appli de la classe en cours.
     *
     * @return bool always true
     */
    public function restart()
    {
        if ($this->check()) {
            // le service tourne, on va redémarrer
            shell_exec($this->command_restart);
        } else {
            // le service ne tourne pas, on le démarre
            shell_exec($this->command_start);
        }

        return true;
    }

    /**
     * Arrête le container de l'appli de la classe en cours.
     *
     * @return bool always true
     */
    public function stop()
    {
        shell_exec($this->command_stop);

        return true;
    }

    /**
     * Récupère l'url publique de l'appli en cours
     * @return bool|mixed|text L'url publique (cliquable) de l'appli en cours, ou false si pas troué
     */
    public function get_public_url()
    {
        $handle = @fopen("/opt/seedbox/resume", "r");
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle);
                if (strpos($buffer, $this->display_name) !== false) {
                    $matches[] = $buffer;
                }
            }
            fclose($handle);
        }
        $matches = array_unique($matches);
        if (count($matches) != 0) {
            $this->public_url = $matches[0];
        } else {
            $this->public_url = false;
        }
        return $this->public_url;
    }

    /**
     * Génère le code html de l'appli en cours.
     *
     * @param bool $display Si True, on affiche le résultat, si false, on ne fait que setter la variable
     *
     * @return string Le code html formatté
     */
    public function display($display = true)
    {
        $this->display_text = '<div class="col-md-4 divappli ';
        if (!$this->installed) {
            $this->display_text .= 'div-uninstalled ';
        }
        $this->display_text .= '" id="div-' . $this->display_name . '" data-appli="' . $this->display_name . '" data-installed="';
        if ($this->installed) {
            $this->display_text .= '1';
        } else {
            $this->display_text .= '0';
        }
        $this->display_text .= '">
                                        <div class="post">
                                            <div class="card card-info card-outline">
                                                <div class="card-body user-block">
                                                    <img class="img-circle img-bordered-sm" src="https://www.scriptseedboxdocker.com/wp-content/uploads/icones/' . $this->display_name . '.png" alt="user image">
                                                    <span class="username">';
        if ($this->public_url !== false) {
            $this->display_text .= '<a href="https://' . $this->public_url . '" target="_blank">' . ucfirst($this->display_name) . '</a>';
        } else {
            $this->display_text .= ucfirst($this->display_name);
        }

        $this->display_text .= '</span><span class="description" id="version-' . $this->display_name . '"></span></div><div class="card-footer" id="toto"><a class="link-black start-stop-button-' . $this->display_name . ' text-sm mr-2 bouton-start" data-appli="' . $this->display_name . '" id="reset-' . $this->display_name . '" style="';
        if (!$this->installed) {
            $this->display_text .= 'display: none;';
        }
        $this->display_text .= 'cursor: pointer;"><i class="fas fa-share mr-1"></i>';
        if ($this->running) {
            $this->display_text .= '<span id="texte-bouton-restart-' . $this->display_name . '">Redémarrer</span>';
        } else {
            $this->display_text .= '<span id="texte-bouton-restart-' . $this->display_name . '">Démarrer</span>';
        }

        $this->display_text .= '</a><a class="link-black start-stop-button-' . $this->display_name . ' text-sm mr-2 bouton-stop" data-appli="' . $this->display_name . '" id="stop-' . $this->display_name . '" style="';
        if (!$this->installed) {
            $this->display_text .= 'display: none;';
        }
        $this->display_text .= ';cursor: pointer;"><i class="fas fa-share mr-1"></i>stop</a><span class="float-right"><button type="submit" name="' . $this->display_name . '" id="status-' . $this->display_name . '" class="btn btn-block ';
        if ($this->installed) {
            $this->display_text .= 'btn-warning ';
        } else {
            $this->display_text .= 'btn-sucess ';
        }
        $this->display_text .= 'btn-success btn-sm text-with bouton-install" data-appli="' . $this->display_name . '">';
        if ($this->installed) {
            $this->display_text .= 'Désinstaller';
        } else {
            $this->display_text .= 'Installer';
        }
        $this->display_text .= '</button></span><!-- </form> --></div></div></div></div>';
        if ($display) {
            echo $this->display_text;
        }

        return $this->display_text;
    }

    /**
     * Cette fonction prend en entrée un tableau d'applis
     * et va chercher toutes les infos nécessaires
     * En retour on a un tableau avec en premier les applis installées et après les non installées
     * par défaut, le display est à true, ce qui veut dire qu'on afficher les cartes des applis.
     *
     * @param $input : tableau des applis à afficher
     * @param bool $display : est-ce qu'on affiche ou pas le résultat ?
     *
     * @return array[] : tableau des applis
     */
    public static function get_all($input, $display = true)
    {
        // init des tableaux
        $appli_installed   = [];
        $appli_uninstalled = [];

        // on boucle sur chaque appli passée en param
        foreach ($input as $appli) {
            // on charge le service correspondant
            $temp               = new service($appli);
            $temp->display_text = $temp->display(false);

            if ($temp->is_installed()) {
                $appli_installed[] = $temp;
            } else {
                $appli_uninstalled[] = $temp;
            }
            unset($temp);
        }
        $return_array = ['installed'   => $appli_installed,
                         'uninstalled' => $appli_uninstalled,];

       
        $display_text = '';
        foreach ($return_array['installed'] as $appli) {
            $display_text .= $appli->display_text;
        }
        foreach ($return_array['uninstalled'] as $appli) {
            $display_text .= $appli->display_text;
        }
        if ($display) {
            echo $display_text;
        }

        return $return_array;
    }
}
