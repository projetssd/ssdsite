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
    /**
     * @var string le token
     */
    public $token;


    /**
     * client constructor.
     * @param string $rclone
     */
    public function __construct($rclone)
    {
        $this->client = $rclone;
        $this->token  = $rclone;
    }

    /**
     * @param string $secret
     */
    function credential($secret)
    {
        $log = new log;
        $log->writelog("-----------------", 'DEBUG');
        $log->writelog("credential " . $this->client, 'DEBUG');

        $commande =
            __DIR__ . '/../../scripts/manage_service.sh credential "' . $this->client . '" "' . $secret . '" ';
        $log->writelog("Lancé", "DEBUG");
        $log->writelog("Commande : " . $commande, "DEBUG");
        shell_exec($commande);
    }

    /**
     * @param string $drive
     * @param string $drivename
     */
    function createtoken($drive, $drivename)
    {
        $log = new log;
        $log->writelog("-----------------", 'DEBUG');
        $log->writelog("token " . $this->client, 'DEBUG');

        $commande =
            __DIR__ . '/../../scripts/manage_service.sh createtoken "' . $this->token . '" "' . $drive . '" "' . $drivename . '" ';
        $log->writelog("Lancé", "DEBUG");
        $log->writelog("Commande : " . $commande, "DEBUG");
        shell_exec($commande);
    }
}
