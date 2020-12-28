 <?php

/**
 * Class options
 */
class oauth
{
    /**
     * @var string nom de l'option
     */
    public $clientoauth;
    public $secretoauth;
    public $mailoauth;

    /**
     *  constructor.
     * @param $google
     */

    public function __construct($google)
    {
        $this->clientoauth = $google;
    }

    function oauth($secretoauth, $mailoauth)
    {
        $log = new log;
        $log->writelog("-----------------",'DEBUG');
        $log->writelog("oauth" . $this->clientoauth,'DEBUG');

        $commande = 'sudo ' . __DIR__ . '/../../scripts/manage_service.sh oauth "' . $this->clientoauth . '" "' . $secretoauth . '" "' . $mailoauth . '" ';
        $log->writelog("Lancé","DEBUG");
        $log->writelog("Commande : " . $commande,"DEBUG");
        shell_exec($commande);
    }
}
