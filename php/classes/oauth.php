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

    /**
     *  constructor.
     * @param $google
     */
    public function __construct($google)
    {
        $this->clientoauth = $google;
    }

    /**
     * @param string $secretoauth
     * @param string $mailoauth
     */
    function function_oauth($secretoauth, $mailoauth)
    {
        $log = new log;
        $log->writelog("-----------------", 'DEBUG');
        $log->writelog("oauth" . $this->clientoauth, 'DEBUG');

        $commande =
            __DIR__ . '/../../scripts/manage_service.sh oauth "' . $this->clientoauth . '" "' . $secretoauth . '" "' . $mailoauth . '" ';
        $log->writelog("Lancé", "DEBUG");
        $log->writelog("Commande : " . $commande, "DEBUG");
        shell_exec($commande);
    }
}
