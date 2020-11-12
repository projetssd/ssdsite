<?php

/**
 * Class service
 * Classe permettant la manipulation des services (applis)
 */
class service
{
    /**
     * @var string Url à checker pour que le service tourne
     */
    private $url = '';
    /**
     * @var string Nom de lappli
     */
    private $display_name;
    /**
     * @var string Ligne de commande pour installer
     */
    private $command_install = '';
    /**
     * @var string Ligne de commande pour désinstaller
     */
    private $command_uninstall = '';
    /**
     * @var string Ligne de commande pour redémarrer
     */
    private $command_restart = '';
    /**
     * @var string Ligne de commande pour arrêter une appli
     */
    private $command_stop = '';
    /**
     * @var string Code html formatté pour afficher une "vignette" d'appli
     */
    private $display_text;
    /**
     * @var bool Est-ce que l'appli est installée ?
     */
    private $installed;
    /**
     * @var bool Est-ce que l'appli tourne ?
     */
    private $running;

    /**
     * service constructor.
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
        $this->display_name      = trim($my_service); // on supprimer les espaces avant/après
        $this->command_install   =
            'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root ansible-playbook /opt/seedbox-compose/includes/dockerapps/' . $this->display_name . '.yml 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';
        $this->command_uninstall =
            'rm /var/www/seedboxdocker.website/logtail/log; echo 0 | sudo tee /opt/seedbox/variables/' . $this->display_name . '; sudo -u root sudo -u root docker rm -f ' . $this->display_name . ' 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';
        $this->command_restart   =
            'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root docker restart ' . $this->display_name . ' 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';
        $this->command_stop      =
            'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root docker stop ' . $this->display_name . ' 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';

        // ici on va surcharger ce qui n'est pas générique
        switch ($my_service)
        {
            case 'radarr':
                $this->url = 'http://127.0.0.1:7878';
                break;
            case 'sonarr':
                $this->url = 'http://127.0.0.1:8989';
                break;
            case 'rutorrent':
                $this->url = 'http://127.0.0.1:8080';
                break;
            case 'lidarr':
                $this->url = 'http://127.0.0.1:8686';
                break;
            case 'all':
                // cas particulier pour charger toutes les cases possibles
                // ne sert qu'à construire un tableau qu'on utilisera plus tard
                // je mets juste ce case pour ne pas bloquer
                break;
            default:
                die('Service non disponible');
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
        $filename = '/opt/seedbox/variables/' . $this->display_name;

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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        curl_close($ch);

        $return_code = false;
        if (200 == $status_code)
        {
            $return_code = true;
        }
        $this->running = $return_code;

        return $return_code;
    }

    /**
     * Installe l'appli de la classe en cours
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
     * Désinstalle l'appli de la classe en cours
     * @return bool always true
     */
    public function uninstall()
    {
        shell_exec($this->command_uninstall);
        return true;
    }

    /**
     * Restarte l'appli de la classe en cours
     * @return bool always true
     */
    public function restart()
    {
        shell_exec($this->command_restart);
        return true;
    }

    /**
     * Arrête le container de l'appli de la classe en cours
     * @return bool always true
     */
    public function stop()
    {
        shell_exec($this->command_stop);
        return true;
    }

    /**
     * Génère le code html de l'appli en cours
     * @param bool $display Si True, on affiche le résultat, si false, on ne fait que setter la variable
     * @return string Le code html formatté
     */
    public function display($display = true)
    {
        $this->installed    = $this->is_installed();
        $this->runnig       = $this->check();
        $this->display_text = '<div class="col-md-4 divappli ';
        if (!$this->installed)
        {
            $this->display_text .= 'div-uninstalled ';
        }
        $this->display_text .= '" id="div-' . $this->display_name . '" data-appli="' . $this->display_name . '" data-installed="';
        if ($this->installed)
        {
            $this->display_text .= '1';
        } else
        {
            $this->display_text .= '0';
        }
        $this->display_text .= '">
                                        <div class="post">
                                            <div class="card card-info card-outline">
                                                <div class="card-body user-block">
                                                    <img class="img-circle img-bordered-sm" src="https://www.scriptseedboxdocker.com/wp-content/uploads/icones/' . $this->display_name . '.png" alt="user image">
                                                    <span class="username">
                                                        <a href="#">' . ucfirst($this->display_name) . '</a>
                                                    </span>
                                                    <span class="description">Version 3.0.4.991</span>
                                                </div>

                                                <div class="card-footer" id="toto">
                                                    <a class="link-black start-stop-button-' . $this->display_name . ' text-sm mr-2 bouton-start" data-appli="' . $this->display_name . '" id="reset-' . $this->display_name . '" style="';
        if (!$this->installed)
        {
            $this->display_text .= 'display: none;';
        }
        $this->display_text .= 'cursor: pointer;"><i class="fas fa-share mr-1"></i>';
        if ($this->running)
        {
            $this->display_text .= 'Restart';
        } else
        {
            $this->display_text .= 'Start';
        }

        $this->display_text .= '</a>
                                                    <a class="link-black start-stop-button-' . $this->display_name . ' text-sm mr-2 bouton-stop" data-appli="' . $this->display_name . '" id="stop-' . $this->display_name . '" style="';
        if (!$this->installed)
        {
            $this->display_text .= 'display: none;';
        }
        $this->display_text .= ';cursor: pointer;"><i class="fas fa-share mr-1"></i>stop</a>

                                                    <span class="float-right">
                                                        <button type="submit" name="' . $this->display_name . '" id="status-' . $this->display_name . '" class="btn btn-block ';
        if ($this->installed)
        {
            $this->display_text .= 'btn-warning ';
        } else
        {
            $this->display_text .= 'btn-sucess ';
        }
        $this->display_text .= 'btn-success btn-sm text-with bouton-install" data-appli="' . $this->display_name . '">';
        if ($this->installed)
        {
            $this->display_text .= 'Désinstaller';
        } else
        {
            $this->display_text .= 'Installer';
        }
        $this->display_text .= '</button>

                                                    </span>
                                                    <!-- </form> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
        if ($display)
        {
            echo $this->display_text;
        }

        return $this->display_text;
    }

    /**
     * Cette fonction prend en entrée un tableau d'applis
     * et va chercher toutes les infos nécessaires
     * En retour on a un tableau avec en premier les applis installées et après les non installées
     * par défaut, le display est à true, ce qui veut dire qu'on afficher les cartes des applis.
     * @param $input : tableau des applis à afficher
     * @param bool $display : est-ce qu'on affiche ou pas le résultat ?
     * @return array[] : tableau des applis
     */
    public static function get_all($input, $display = true)
    {
        // init des tableaux
        $appli_installed   = [];
        $appli_uninstalled = [];

        // on boucle sur chaque appli passée en param
        foreach ($input as $appli)
        {
            // on charge le service correspondant
            $temp               = new service($appli);
            $temp->display_text = $temp->display(false);

            if ($temp->is_installed())
            {
                $appli_installed[] = $temp;
            } else
            {
                $appli_uninstalled[] = $temp;
            }
            unset($temp);
        }
        $return_array = ['installed'   => $appli_installed,
                         'uninstalled' => $appli_uninstalled,];

        if ($display)
        {
            $display_text = '';
            foreach ($return_array['installed'] as $appli)
            {
                $display_text .= $appli->display_text;
            }
            foreach ($return_array['uninstalled'] as $appli)
            {
                $display_text .= $appli->display_text;
            }
            echo $display_text;
        }

        return $return_array;
    }
}
