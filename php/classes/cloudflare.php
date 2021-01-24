 <?php

/**
 * Class options
 */
class cloudflare
{
    /**
     * @var string nom de l'option
     */
    public $emailcloud;

    /**
     *  constructor.
     * @param $cloud
     */
    public function __construct($cloud)
    {
        $this->emailcloud = $cloud;
    }

    /**
     * @param string $apicloud
     */
    function function_cloudflare($apicloud)
    {
        $log = new log;
        $log->writelog("-----------------", 'DEBUG');
        $log->writelog("cloudflare" . $this->emailcloud, 'DEBUG');

        $commande =
            __DIR__ . '/../../scripts/manage_service.sh cloudflare "' . $this->emailcloud . '" "' . $apicloud . '" ';
        $log->writelog("Lancé", "DEBUG");
        $log->writelog("Commande : " . $commande, "DEBUG");
        shell_exec($commande);
    }
}
