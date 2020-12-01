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
    public $idcloud;
    public $passcloud;
    public $idoauth;
    public $clientoauth;
    public $mailoauth;


    /**
     * utilisateur constructor.
     * @param $user Nom de l'utilisateur
     */
    public function __construct($user)
    {
        $this->utilisateur = $user;

     }

    /**
     * @param $passe
     * @param $email
     * @param $domaine
     * @param $idplex
     * @param $passplex
     * @param $idcloud
     * @param $passcloud
     * @param $idoauth
     * @param $clientoauth
     * @param $mailoauth
     * @return bool Configuration d'un utilisateur
     */
    function configure($passe, $email, $domaine, $idplex, $passplex, $idcloud, $passcloud, $idoauth, $clientoauth, $mailoauth)
    {
        /**
         * Alors là, on attaque une BASE de tout ce qui est langage serveur
         * ON NE PEUT PAS FAIRE CONFIANCE A CE QUE NOUS DONNE L'UTILUSATEUR :)
         * Il faut donc tout checker
         */
        // on part du principe que tout est ok
        $verif = true;
        // on construit un tableau vide pour catcher les erreurs
        $tab_params = array(
            "passe"       => true,
            'email'       => true,
            'domaine'     => true,
            'idplex'      => true,
            'passplex'    => true,
            'idcloud'     => true,
            'passcloud'   => true,
            'idoauth'     => true,
            'clientoauth' => true,
            'mailoauth'    => true
        );
        // on boucle sur tous les arguments
        // si tu veux en rendre certains optionnels, il suffit de les supprimer du tableau
        // ci dessus
        foreach ($tab_params as $key => $item)
        {
            if (empty(trim($$key)))
            {
                $verif            = false;
                $tab_params[$key] = false;
            }
        }
        // bon, maintenant, on sait que si $verif = false, on ne doit pas aller plus loin
        if ($verif)
        {
            /**
             * Alors là, il est possible que tu aie des variables vides parce que pas obligatoire
             * Il suffit de mettre des " autour :)
             * On va aussi simplifier l'appel en bouclant que le tableau qu'on a plus haut
             */
            $commande = 'sudo ' . __DIR__ . '/../../scripts/manage_service.sh "' . $this->utilisateur . '" ';
            foreach ($tab_params as $key => $item)
            {
                $commande .= '"' . $$key . '" ';
            }
            shell_exec($commande . ' configure');
            $tab_retour = array("verif" => true, "commande" => $commande);
        } else
        {
            // on est dans le cas où on ne doit rien faire
            $tab_retour = array("verif" => false, "detail" => $tab_params);
        }
        // on va maintenant générer un affichage
        // ce sera un tableau json qui sera interprété par le ajax
        // voir le fichier js pour vérifier
        echo json_encode($tab_retour);
        return $verif;
    }
}
