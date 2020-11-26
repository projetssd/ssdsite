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
     * @var string Nom de lappli
     */
    public $subdomain;
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
     * @var bool Est-ce que l'appli est installée ?
     */
    public $installed;
    /**
     * @var bool Est-ce que l'appli tourne ?
     */
    public $running;
    /**
     * @var string url cliquable
     */
    public $public_url;
    /**
     * @var string Numéro de version
     */
    public $version;
    /**
     * @var integer Port utilisé par l'appli
     */
    public $port;
    /**
     * @var string Adresse ip du container
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
        // on commence par mettre tout ce qui est générique
        //
        $this->display_name      = trim($my_service); // on supprimer les espaces avant/après
        $this->command_install   =
            'sudo ' . __DIR__ . '/../../scripts/manage_service.sh ' . $this->display_name . $this->subdomain . ' install';
        $this->command_uninstall =
            'sudo ' . __DIR__ . '/../../scripts/manage_service.sh ' . $this->display_name . ' uninstall';
        $this->command_restart   =
            'sudo ' . __DIR__ . '/../../scripts/manage_service.sh ' . $this->display_name . ' restart';
        $this->command_stop      =
            'sudo ' . __DIR__ . '/../../scripts/manage_service.sh ' . $this->display_name . ' stop';
        $this->command_start     =
            'sudo ' . __DIR__ . '/../../scripts/manage_service.sh ' . $this->display_name . ' start';
        //
        // on va chercher l'ip du docker
        //
        $retour =
            exec("docker inspect -f '{{.NetworkSettings.Networks.bridge.IPAddress}}' " . $this->display_name, $tab_retour, $code_retour);
        if ($code_retour == 0)
        {
            $this->url  = 'http://' . $retour;
            $this->host = $retour;
        } else
        {
            // docker non trouvé, on met l'ip locale histoire de ne pas laisser vide
            $this->url = 'http://127.0.0.1';
        }

        // ici on va surcharger ce qui n'est pas générique
        switch ($my_service)
        {
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

        if ($my_service != 'all')
        {
            // on va remplir les valeurs par défaut
            //$this->running    = $this->check();
            //$this->installed  = $this->is_installed();
            $this->public_url = false;
            // on récupère les url publiques
            // on lit le fichier
            $file    = fopen('/opt/seedbox/resume', 'r');
            $matches = array();
            if ($file)
            {
                while (!feof($file))
                {
                    $buffer = fgets($file);
                    if (strpos($buffer, $this->display_name) !== false)
                    {
                        $matches[] = $buffer;
                    }
                }
                fclose($file);
            }
            if (count($matches) != 0)
            {
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
        if (file_exists($filename))
        {
            $file     = fopen($filename, 'r');
            $contents = fread($file, filesize($filename));
        }
        $contents = substr($contents, 0); // on ne garde que le premier caractère

        switch ($contents)
        {
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
        if ($this->is_installed())
        {
            $connection = fsockopen($this->host, $this->port, $errno, $errstr);
            if (!$connection)
            {
                return false;
            } else
            {
                if (is_resource($connection))
                {
                    fclose($connection);
                    return true;
                }
            }
        }


        return false;
    }

    /**
     * Récupère le code html ou la sortie de page de l'url en entrée
     * @param $url string URl à récupérer
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
        // on met un fichier à la con pour que le cookie fonctionne
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
        if (!$this->check())
        {
            return ' container arrêté';
        }
        switch ($this->display_name)
        {
            case 'radarr':
            case 'sonarr':
            case 'lidarr':
                $html = $this->get_html($this->url . '/initialize.js');

                $html = explode('=', $html);
                $html = $html[1];
                $html = str_replace('{', '', $html);
                $html = str_replace('}', '', $html);
                $html = explode(',', $html);
                foreach ($html as $val)
                {
                    $detail = explode(':', $val);
                    if (trim($detail[0]) == 'version')
                    {
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

        if (empty($version))
        {
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
        echo "commande " . $this->command_install;
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
        if ($this->check())
        {
            // le service tourne, on va redémarrer
            shell_exec($this->command_restart);
        } else
        {
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
     * @return bool|mixed|string L'url publique (cliquable) de l'appli en cours, ou false si pas troué
     */
    public function get_public_url()
    {
        $handle  = @fopen("/opt/seedbox/resume", "r");
        $matches = array();
        if ($handle)
        {
            while (!feof($handle))
            {
                $buffer = fgets($handle);
                if (strpos($buffer, $this->display_name) !== false)
                {
                    $matches[] = $buffer;
                }
            }
            fclose($handle);
        }
        $matches = array_unique($matches);
        if (count($matches) != 0)
        {
            $this->public_url = $matches[0];
        } else
        {
            $this->public_url = false;
        }
        return $this->public_url;
    }


    /**
     * Cette fonction prend en entrée un tableau d'applis
     * et va chercher toutes les infos nécessaires
     * En retour on a un tableau avec en premier les applis installées et après les non installées
     * par défaut, le display est à true, ce qui veut dire qu'on afficher les cartes des applis.
     *
     * @param $input : tableau des applis à afficher
     *
     * @return array[] : tableau des applis
     */
    public static function get_all($input)
    {
        // init des tableaux
        $appli_installed   = [];
        $appli_uninstalled = [];

        // on boucle sur chaque appli passée en param
        foreach ($input as $appli)
        {
            // on charge le service correspondant
            $temp = new service($appli);


            if ($temp->is_installed())
            {
                $appli_installed[] = $temp;
            } else
            {
                $appli_uninstalled[] = $temp;
            }
            unset($temp);
        }
        return ['installed'   => $appli_installed,
                'uninstalled' => $appli_uninstalled,];
    }
}
