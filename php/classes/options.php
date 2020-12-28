 <?php

/**
 * Class options
 */
class options
{
    /**
     * @var string nom de l'option
     */
    public $outils;

    /**
     *  constructor.
     * @param $scanner
     */

    public function __construct($scanner)
    {
        $this->outils = $scanner;
    }

    function tools()
    {
        $log = new log;
        $log->writelog("-----------------",'DEBUG');
        $log->writelog("tools" . $this->outils,'DEBUG');

        $commande = 'sudo ' . __DIR__ . '/../../scripts/manage_service.sh tools "' . $this->outils . '" ';
        $log->writelog("Lancé","DEBUG");
        $log->writelog("Commande : " . $commande,"DEBUG");
        shell_exec($commande);
    }
}
