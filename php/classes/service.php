<?php

class service
{
    private $url = '';
    private $display_name;
    private $username = '';
    private $password = '';
    private $command_install = '';
    private $command_uninstall = '';
    private $command_restart = '';
    private $command_stop = '';
    private $display_text;
    private $installed;
    private $running;

    public function __construct($my_service)
    {
        /*******************************************************
         * Ici on va charger les infos spécifiques à un service
         * Si le service n'est pas présent, on ferme la page
         * C'est ce service qui sera utilisé dans toutes les fonctions qui suivent
         *
         */
        //
        // on commence par mettre tout ce qui es générique
        //
        $this->display_name = $my_service;
        $this->command_line =
                    'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root ansible-playbook /opt/seedbox-compose/includes/dockerapps/'.$this->display_name.'.yml 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';
        $this->command_lineone =
                    'rm /var/www/seedboxdocker.website/logtail/log; echo 0 | sudo tee /opt/seedbox/variables/'.$this->display_name.'; sudo -u root sudo -u root docker rm -f '.$this->display_name.' 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';
        $this->command_linetwo =
                    'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root docker restart '.$this->display_name.' 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';
        $this->command_linethree =
                    'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root docker stop '.$this->display_name.' 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';

        // ici on va surcharger ce qui n'est pas générique
        switch ($my_service) {
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
     * @return bool
     */
    public function is_installed()
    {
        /**
         * Chemin du fichier qui contient les infos.
         */
        $filename = '/opt/seedbox/variables/'.$this->display_name;

        $installed = false; // par défaut, on considère que le service n'est pas là
        if (file_exists($filename)) {
            $file = fopen($filename, 'r');
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
     * Fonctoin qui permet de check si un service tourne, via un appel curl.
     *
     * @return bool
     */
    public function check()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        // on commence l'auth, a priori
        //curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        curl_close($ch);

        $return_code = false;
        if (200 == $status_code) {
            $return_code = true;
        }
        $this->running = $return_code;

        return $return_code;
    }

    /**
     * @return bool
     */
    public function install()
    {
        /* la commande d'install se termine par un & et donc rend la main tout de suite
        impossible de catcher la sortie
        on ne stocke donc aucune info
        les infos seront lues dans le défilement des logs */

        shell_exec($this->command_line);

        return true;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        /* la commande d'uninstall se termine par un & et donc rend la main tout de suite
        impossible de catcher la sortie
        on ne stocke donc aucune info
        les infos seront lues dans le défilement des logs */

        shell_exec($this->command_lineone);

        return true;
    }

    /**
     * @return bool
     */
    public function restart()
    {
        /* la commande de restart se termine par un & et donc rend la main tout de suite
        impossible de catcher la sortie
        on ne stocke donc aucune info
        les infos seront lues dans le défilement des logs */

        shell_exec($this->command_linetwo);

        return true;
    }

    public function stop()
    {
        /* la commande de restart se termine par un & et donc rend la main tout de suite
        impossible de catcher la sortie
        on ne stocke donc aucune info
        les infos seront lues dans le défilement des logs */

        shell_exec($this->command_linethree);

        return true;
    }

    public function display($display = true)
    {
        $this->installed = $this->is_installed();
        $this->display_text = '<div class="col-md-4 divappli ';
        if (!$this->installed) {
            $this->display_text .= 'div-uninstalled ';
        }
        $this->display_text .= 'id="div-'.$this->display_name.'" data-appli="'.$this->display_name.'" data-installed="';
        if ($this->installed) {
            $this->display_text .= '1';
        } else {
            $this->display_text .= '0';
        }
        $this->display_text .= '">
                                        <div class="post">
                                            <div class="card card-info card-outline">
                                                <div class="card-body user-block">
                                                    <img class="img-circle img-bordered-sm" src="https://www.scriptseedboxdocker.com/wp-content/uploads/icones/'.$this->display_name.'.png" alt="user image">
                                                    <span class="username">
                                                        <a href="#">'.ucfirst($this->display_name).'</a>
                                                    </span>
                                                    <span class="description">Version 3.0.4.991</span>
                                                </div>

                                                <div class="card-footer" id="toto">
                                                    <a class="link-black start-stop-button-'.$this->display_name.' text-sm mr-2 bouton-start" data-appli="'.$this->display_name.'" id="reset-'.$this->display_name.'" name="reset-'.$this->display_name.'" style="';
        if (!$this->installed) {
            $this->display_text .= 'display: none;';
        }
        $this->display_text .= 'cursor: pointer;"><i class="fas fa-share mr-1"></i>Restart</a>
                                                    <a class="link-black start-stop-button-'.$this->display_name.' text-sm mr-2 bouton-stop" data-appli="'.$this->display_name.'" id="stop-'.$this->display_name.'" name="stop-'.$this->display_name.'" style="';
        if (!$this->installed) {
            $this->display_text .= 'display: none;';
        }
        $this->display_text .= ';cursor: pointer;"><i class="fas fa-share mr-1"></i>stop</a>

                                                    <span class="float-right">
                                                        <button type="submit" name="'.$this->display_name.'" id="status-'.$this->display_name.'" class="btn btn-block ';
        if ($this->installed) {
            $this->display_text .= 'btn-warning ';
        } else {
            $this->display_text .= 'btn-sucess ';
        }
        $this->display_text .= 'btn-success btn-sm text-with bouton-install" data-appli="'.$this->display_name.'">';
        if ($this->installed) {
            $this->display_text .= 'Désinstaller';
        } else {
            $this->display_text .= 'Installer';
        }
        $this->display_text .= '</button>

                                                    </span>
                                                    <!-- </form> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
        if ($display) {
            echo $this->display_text;
        }
    }
}
