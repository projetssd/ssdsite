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
    public $token;
    public $drive;
    public $drivename;



    /**
     *  constructor.
     * @param $client id oauth
     */

    public function __construct($rclone)
    {
        $this->client = $rclone;
        $this->token = $rclone;
    }

    function credential($secret)
    {
        $log = new log;
        $log->writelog("-----------------",'DEBUG');
        $log->writelog("credential " . $this->client,'DEBUG');

        $commande = 'sudo ' . __DIR__ . '/../../scripts/manage_service.sh credential "' . $this->client . '" "' . $secret . '" ';
        $log->writelog("Lancé","DEBUG");
        $log->writelog("Commande : " . $commande,"DEBUG");
        shell_exec($commande);
    }

    function createtoken($drive, $drivename)
    {
        $log = new log;
        $log->writelog("-----------------",'DEBUG');
        $log->writelog("token " . $this->client,'DEBUG');

        $commande = 'sudo ' . __DIR__ . '/../../scripts/manage_service.sh createtoken "' . $this->token .  '" "' . $drive . '" "' . $drivename . '" ';
        $log->writelog("Lancé","DEBUG");
        $log->writelog("Commande : " . $commande,"DEBUG");
        shell_exec($commande);
    }


}
