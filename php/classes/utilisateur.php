 <?php

/**
 * Class utilisateur
 */
class utilisateur
{
    /**
     * @var string Nom de l'utilisateur
     */
    public $utilisateur;
    public $passe;
    public $email;
    public $domaine;
    public $idplex;
    public $passplex;

    /**
     * utilisateur constructor.
     * @param $user Nom de l'utilisateur
     */
    public function __construct($user)
    {
        $this->utilisateur = $user;

     }
    /**
     * @return bool Configuration d'un utilisateur
     */
    function configure($passe, $email, $domaine, $idplex, $passplex)
    {
        shell_exec('sudo ' . __DIR__ . '/../../scripts/manage_service.sh ' . $this->utilisateur . '" "' . $passe . '" "' . $email . '" "' . $domaine . '" "' . $idplex . '" "' . $passplex . ' configure');
        return true;
    }
}
