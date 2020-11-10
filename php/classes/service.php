<?php


class service
{
    var $url = '';
    var $username = '';
    var $password = '';
    var $command_line = '';
    var $log = '';

    function __construct($my_service)
    {
        /*******************************************************
         * Ici on va charger les infos spécifiques à un service
         * Si le service n'est pas présent, on ferme la page
         * C'est ce service qui sera utilisé dans toutes les fonctions qui suivent
         *
         */
        switch ($my_service)
        {
            case "radarr":
                $this->url = "http://127.0.0.1:7878";
                $this->command_line = 'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root ansible-playbook /opt/seedbox-compose/includes/dockerapps/radarr.yml 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null';
                // on peut éventuellement surcharger $username et $password ici
                break;
            case "monautreservice":
                $this->url = "http://127.0.0.1:8080";
                $this->command_line = 'rm /var/www/seedboxdocker.website/logtail/log; sudo -u root ansible-playbook /opt/seedbox-compose/includes/dockerapps/autreservice.yml 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null';
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
        // pas la peine de faire un retour true or false, on ne sait pas si ça s'est bien passé
        // on va juste retourner le texte de la commande
        echo "Execution de " . $this->command_line;
        $this->log = shell_exec($this->command_line);
        return true;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        sleep(10);
        return true;
    }
}