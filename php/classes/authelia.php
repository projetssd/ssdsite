 <?php

/**
 * Class options
 */
class authelia
{
    /**
     * @var string nom de l'option
     */
    public $mailauthelia;

    /**
     *  constructor.
     * @param $google
     */
    public function __construct($authentification)
    {
        $this->mailauthelia= $authentification;
    }

    /**
     * @param string $smtpauthelia
     * @param string $portauthelia
     * @param string $passeauthelia
     */
    function add_authelia($smtpauthelia, $portauthelia, $passeauthelia)
    {
        $log = new log;
        $log->writelog("-----------------", 'DEBUG');
        $log->writelog("add_authelia" . $this->mailauthelia, 'DEBUG');

        $commande =
            __DIR__ . '/../../scripts/manage_service.sh add_authelia "' . $this->mailauthelia . '" "' . $smtpauthelia . '" "' . $portauthelia . '"  "' . $passeauthelia . '" ';
        $log->writelog("Lancé", "DEBUG");
        $log->writelog("Commande : " . $commande, "DEBUG");
        shell_exec($commande);
    }
}
