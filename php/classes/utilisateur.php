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
    function configure()
    {
        shell_exec('sudo ' . __DIR__ . '/../../scripts/manage_service.sh ' . $this->utilisateur . ' configure');
        return true;
    }
}