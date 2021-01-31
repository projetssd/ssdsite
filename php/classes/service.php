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
     * @var string Sous domain de l'application
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
     * @var string Ligne de commande pour démarrer
     */
    public $command_start = '';
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
     * @var integer Port utilisé par l'appli
     */
    public $port = 0;
    /**
     * @var string Adresse ip du container
     */
    public $host;

    /**
     * @var string Utilisation interne : chemin des fichiers status
     */
    private $status_file = '/opt/seedbox/status/';

    /**
     * @var string Chemin du fichier resume
     */
    private $resume_file = '/opt/seedbox/resume';

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
        $start_command           = __DIR__ . '/../../scripts/manage_service.sh ';
        $this->command_install   = $start_command . 'install ' . $this->display_name . ' ';
        $this->command_uninstall = $start_command . 'uninstall ' . $this->display_name . ' ';
        $this->command_restart   = $start_command . 'restart ' . $this->display_name . ' ';
        $this->command_stop      = $start_command . 'stop ' . $this->display_name . ' ';
        $this->command_start     = $start_command . 'start ' . $this->display_name . ' ';

        //
        // on va chercher l'ip du docker
        //
        $this->url = 'http://127.0.0.1';
        $retour    =
            exec("docker inspect -f '{{.NetworkSettings.Networks.bridge.IPAddress}}' " . $this->display_name, $tab_retour, $code_retour);

        if ($code_retour == 0)
        {
            $this->url  = 'http://' . $retour;
            $this->host = $retour;
        }

        // ici on va surcharger ce qui n'est pas générique
        require __DIR__ . '/detail_appli.php';
        $this->port = $tableau_appli[$my_service]['port'];
        if ($this->port == 0)
        {
            $log = new log;
            $log->writelog("Appli " . $my_service . " non connue ?");
            // port non connu, on met un port par défaut
            $this->port = 80;
        }

        $this->url = "http://" . $this->host . ":" . $this->port;

        if (($my_service != 'all') && $this->is_installed())
        {
            // on va remplir les valeurs par défaut

            $this->public_url = false;
            if ($this->is_installed())
            {
                // on récupère les url publiques
                // on lit le fichier
                $file    = fopen($this->resume_file, 'r');
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
                if (!empty($matches))
                {
                    $tab_temp         = explode('=', $matches[0]);
                    $this->public_url = trim($tab_temp[1]);
                }
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
        $filename = $this->status_file . $this->display_name;

        $tmp_installed = false; // par défaut, on considère que le service n'est pas là

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
                $tmp_installed = true;
                break;
            case 3:
                // en cours de désinstall
                break;
            default:
                break;
        }

        $this->installed = $tmp_installed;

        return $tmp_installed;
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
            case 'bazarr':
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
            case 'emby':
                $version = shell_exec("docker exec -t emby sh -c 'cat /config/data/lastversion.txt'");
                break;
            case 'bitwarden':
                $result  = shell_exec("docker exec -t bitwarden sh -c 'cat /web-vault/version.json'");
                $detail  = explode(':', $result);
                $html    = str_replace('"', "", $detail[1]);
                $version = str_replace("}", "", $html);
                break;
            case 'cloudcmd':
                $result  =
                    shell_exec("docker exec -t cloudcmd sh -c 'grep version /usr/src/app/package.json | head -1'");
                $detail  = explode(':', $result);
                $html    = str_replace('"', "", $detail[1]);
                $version = str_replace(",", "", $html);
                break;
            case 'mellow':
                $result  = shell_exec("docker exec -t mellow sh -c 'grep version /usr/src/app/package.json | head -1'");
                $detail  = explode(':', $result);
                $html    = str_replace('"', "", $detail[1]);
                $version = str_replace(",", "", $html);
                break;
            case 'jackett':
                $html    = $this->get_html($this->url . '/api/v2.0/server/config');
                $json    = json_decode($html, true);
                $version = $json['app_version'];
                break;
            case 'autoscan':
                $version = shell_exec("docker exec autoscan autoscan --version");
                break;
            case 'filerun':
                $version = shell_exec("docker exec -t filerun sh -c 'cat /var/www/html/initial_version.txt'");
                break;
            case 'medusa':
            case "calibre":
            case "ombi":
            case "calibreweb":
            case "nextcloud":
            case "tautulli":
            case "heimdall":
            case "nzbhydra":
            case "sabnzbd":
            case "deluge":
            case "sickchill":
            case "bookstack":
            case "codif":
            case "duplicati":
                $result  = shell_exec("docker inspect -f '{{.Config.Labels.build_version}}' " . $this->display_name);
                $temp    = explode('-', $result);
                $version = $temp[1];
                break;
            case "readarr":
                $version =
                    shell_exec('docker inspect -f \'{{ index .Config.Labels "org.opencontainers.image.version" }}\' readarr');
                break;
            case "traefik":
                $version =
                    shell_exec('docker inspect -f \'{{ index .Config.Labels "org.opencontainers.image.version" }}\' traefik');
                break;
            case "firefox":
                $version =
                    shell_exec('docker inspect -f \'{{ index .Config.Labels "org.label-schema.version" }}\' firefox');
                break;
            case "jdownloader":
                $version =
                    shell_exec('docker inspect -f \'{{ index .Config.Labels "org.label-schema.schema-version" }}\' jdownloader');
                break;
            case "ubooquity":
                $temp    = shell_exec("docker inspect -f '{{.Config.Env}}' ubooquity");
                $tab_env = explode(" ", $temp);
                foreach ($tab_env as $detail_env)
                {

                    if (strpos($detail_env, "APP_VERSION") !== false)
                    {
                        $temp_version = explode("=", $detail_env);
                        $version      = $temp_version[1];
                    }
                }
                break;
            case "plex":
                $version = shell_exec('docker exec plex /usr/lib/plexmediaserver/Plex\ Media\ Server --version');
                break;
            default:
                $version = 'Version inconnue';
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
     *
     * @param $subdomain string Sous domaine de l'application
     * @return bool always true
     */
    public function install($subdomain, $authentification)
    {
        /* la commande d'install se termine par un & et donc rend la main tout de suite
        impossible de catcher la sortie
        on ne stocke donc aucune info
        les infos seront lues dans le défilement des logs */
        $this->command_install .= " " . $subdomain . " " . $authentification;
        $log                   = new log;
        $log->writelog("-----------------", 'DEBUG');
        $log->writelog("Installation " . $this->display_name, 'DEBUG');
        $log->writelog("Commande " . $this->command_install, 'DEBUG');
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
        $log = new log;
        $log->writelog("-----------------", 'DEBUG');
        $log->writelog("Désinstall " . $this->display_name, 'DEBUG');
        $log->writelog("Commande " . $this->command_uninstall, 'DEBUG');
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
        $log = new log;
        $log->writelog("-----------------", 'DEBUG');
        $log->writelog("Restart " . $this->display_name, 'DEBUG');

        if ($this->check())
        {
            // le service tourne, on va redémarrer
            shell_exec($this->command_restart);
            $log->writelog("Commande " . $this->command_restart, 'DEBUG');
        } else
        {
            // le service ne tourne pas, on le démarre
            shell_exec($this->command_start);
            $log->writelog("Commande " . $this->command_start, 'DEBUG');
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
        $log = new log;
        $log->writelog("-----------------", 'DEBUG');
        $log->writelog("Stop " . $this->display_name, 'DEBUG');
        $log->writelog("Commande " . $this->command_stop, 'DEBUG');
        shell_exec($this->command_stop);

        return true;
    }

    /**
     * Récupère l'url publique de l'appli en cours
     * @return bool|mixed|string L'url publique (cliquable) de l'appli en cours, ou false si pas troué
     */
    public function get_public_url()
    {
        $handle  = @fopen($this->resume_file, "r");
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
        if (!empty($matches))
        {

            $tab_temp         = explode('=', $matches[0]);
            $this->public_url = trim($tab_temp[1]);

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

    /**
     * Retourne un tableau des applis installées
     * @return array Liste des applis installées
     */
    function get_installed_appli()
    {
        global $debugbar;
        $array_cache = array(
            'collabora', 'cloudproxy', 'flaresolverr', 'office',);
        $retour      = array();
        $listfiles   = scandir($this->status_file);
        foreach ($listfiles as $file)
        {
            if ((substr($file, 0, 1) !== '.') && (substr($file, 0, 3) != 'db-') && (!in_array($file, $array_cache)))
            {
                $filename = $this->status_file . $file;
                $handle   = fopen($filename, 'r');
                $contents = fread($handle, filesize($filename));
                if ($contents == 2)
                {
                    $retour[] = $file;
                }
            }
        }
        return $retour;
    }

    /**
     * @param $applis_installed array Tableau des applis installées
     * @return array La liste des applis pas encore installées
     * Fait un diff entre toutes applis et applis installées
     */
    public function get_uninstalled_applis($applis_installed)
    {
        global $mode_debug;
        global $debugbar;

        $retour = array();
        require __DIR__ . '/detail_appli.php';

        $all_applis = array();
        foreach ($tableau_appli as $key => $val)
        {
            if ($key != 'all')
            {
                $all_applis[] = $key;
            }
        }


        $retour = array();
        foreach (array_diff($all_applis, $applis_installed) as $val)
        {
            $retour[] = $val;
        }
        sort($retour);
        return $retour;
    }
}


