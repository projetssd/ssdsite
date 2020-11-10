<?php


class service
{
    var $url             = '';
    var $display_name;
    var $username        = '';
    var $password        = '';
    var $command_line    = '';
    var $command_lineone = '';

    function __construct($my_service)
    {
        /*******************************************************
         * Ici on va charger les infos spécifiques à un service
         * Si le service n'est pas présent, on ferme la page
         * C'est ce service qui sera utilisé dans toutes les fonctions qui suivent
         *
         */
        $this->display_name = $my_service;
        switch ($my_service)
        {
            case "radarr":
                $this->url          = "http://127.0.0.1:7878";
                $this->command_line =
                    'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root ansible-playbook /opt/seedbox-compose/includes/dockerapps/radarr.yml 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';
                // on peut éventuellement surcharger $username et $password ici
                $this->command_lineone =
                    'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root docker rm -f radarr 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';
                // on peut éventuellement surcharger $username et $password ici
                break;
            case "sonarr":
                $this->url          = "http://127.0.0.1:8989";
                $this->command_line =
                    'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root ansible-playbook /opt/seedbox-compose/includes/dockerapps/sonarr.yml 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';
                // on peut éventuellement surcharger $username et $password ici
                $this->command_lineone =
                    'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root docker rm -f sonarr 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &';
                // on peut éventuellement surcharger $username et $password ici
                break;
            default:
                die("Service non disponible");
        }
    }

    /**
     * Fonctoin qui permet de check si un service tourne, via un appel curl
     * @return boolean
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
        $result      = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        curl_close($ch);

        $return_code = false;
        if ($status_code == 200)
        {
            $return_code = true;
        }
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

    public function display()
    {
        echo '<div class="col-md-4" >
                                         <div class="post" >
                                            <div class="card card-info card-outline" >
                                                <div class="card-body user-block" >
                                                    <img class="img-circle img-bordered-sm" src =
        "https://www.scriptseedboxdocker.com/wp-content/uploads/2020/05/' . $this->display_name . '.png" alt         = "user image" >
                                                    <span class="username" >
                                                        <a href = "#" >' . $this->display_name . '</a >
                                                    </span >
                                                    <span class="description" > Version 3.0.4.991 </span >
                                                </div >

                                                <div class="card-footer" id = "toto" >
                                                    
                                                    <!--Notes Merrick
                                                                      Les boutons start / stop doivent avoir comme classe
                                                                      start - stop - button -<nom_service >
                                                                                              On va les cacher par défaut
                                                                                                                   -- >
                                                    <a href = "php/index.php?reset=true" class="link-black start-stop-button-' . $this->display_name . '
                                                                      text-sm mr-2" id = "reset" name = "reset" style =
        "display: none;" ><i class="fas fa-share mr-1" ></i > Restart</a >
                                                    <a href = "php/index.php?stop=true" class="link-black start-stop-button-' . $this->display_name . '
                                                                      text-sm mr-2" id = "stop" name = "stop" style =
        "display: none;" ><i class="fas fa-stop mr-1" ></i > Stop</a >

                                                    <span class="float-right" >
                                                        <!--Notes Merrick
                                                                        Le bouton d\'install doit avoir pour id
                                                                        status-<nomservice>
                                                                        Comme classe bouton-isntall et
                                                                        data-appli=<nomservice>
                                                                        -->
                                                        <button type="submit" name="' . $this->display_name . '" id="status-' . $this->display_name . '" class="btn btn-block
                                                                               btn-success btn-sm text-with
                                                                               bouton-install" data-appli="' . $this->display_name . '"></button>

                                                    </span>
                                                    <!-- </form> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.app -->';
    }
}