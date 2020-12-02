 <?php

/**
 * Class client
 */
class client
{
    /**
     * @var string Nom du client
     */
    public $client;
    public $secret;

    /**
     *  constructor.
     * @param $client id oauth
     */

    public function __construct($rclone)
    {
        $this->client = $rclone;

    }

    function credential($secret)
    {
        $log = new log;
        $log->writelog("-----------------",'DEBUG');
        $log->writelog("credential " . $this->client,'DEBUG');
        /**

        $commande = 'sudo ' . __DIR__ . '/../../scripts/manage_service.sh credential "' . $this->client . '" ';
        $log->writelog("Lancé","DEBUG");
        $log->writelog("Commande : " . $commande,"DEBUG");
        shell_exec($commande);
    }
}
