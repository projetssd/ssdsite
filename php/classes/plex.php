 <?php

/**
 * Class plex
 */
class plex
{
    /**
     * @var string nom de l'option
     */
    public $plexident;

    /**
     *  constructor.
     * @param $mserver
     */
    public function __construct($mserver)
    {
        $this->plexident = $mserver;
    }

    /**
     * @param string $plexident
     * @param string $plexpass
     */
    function create_plex($plexpass)
    {
        $log = new log;
        $log->writelog("-----------------", 'DEBUG');
        $log->writelog("create_plex" . $this->plexident, 'DEBUG');

        $commande =
            __DIR__ . '/../../scripts/manage_service.sh create_plex "' . $this->plexident . '" "' . $plexpass . '"';
        $log->writelog("Lancé", "DEBUG");
        $log->writelog("Commande : " . $commande, "DEBUG");
        shell_exec($commande);
    }
}
