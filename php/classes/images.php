<?php


class images
{
    /**
     * @var string Application
     */
    public $appli;
    /**
     * @var string Chemin par défaut des images
     */
    private $chemin = __DIR__ . '/../../dist/appli_img/';
    /**
     * @var string Chemin par défaut des images distantes
     */
    private $chemin_distant = "https://iconesssd.sdewitte.net/";
    /**
     * @var string Header par défaut (png)
     */
    private $header = 'Content-Type: image/png';

    /**
     * images constructor.
     * @param string $appli Le nom de l'application
     */
    public function __construct($appli)
    {
        $this->appli = $appli;
    }

    /**
     * Regarde si une image existe en local
     * @return bool Vrai si image existe en local
     */
    private function images_exists()
    {
        return file_exists($this->chemin . $this->appli . '.png');
    }

    /**
     * Regarde si une image existe en distant
     * @return bool Vrai si image existe en distant
     */
    private function distant_images_exists()
    {
        return curl_init($this->chemin_distant . $this->appli . ".png") !== false;
    }

    /**
     * Copie une image distante en local
     * @return false|GdImage|resource false si erreur, sinon ressource image
     */
    private function copy_img_to_local()
    {
        $log = new log;
        // d'abord on copie l'image vers un fichier temporaire
        $tmpfname = tempnam("/tmp", "img");
        if (!copy($this->chemin_distant . $this->appli . ".png", $tmpfname))
        {

            $log->writelog("Erreur de copie de fichier distant :  ");
            $log->writelog($this->chemin_distant . $this->appli . ".png");
            $log->writelog(" vers " . $tmpfname);
            unlink($tmpfname);
            return false;
        }
        // maintenant qu'on a une copie locale, on va modifier le fichier
        if(!$im = imagecreatefrompng($tmpfname))
        {
           
            $log->writelog("Erreur de création de fichier png (imagecreatefrom)");
            $log->writelog(" depuis " . $tmpfname); 
            unlink($tmpfname);
            return false;
        }
        // on retaille l'image en 40 x 40
        list($width, $height) = getimagesize($tmpfname);
        $newimg = imagecreatetruecolor(40, 40);
        $log->writelog("Ancienne taille " . $width . " - " . $height);
        if (!imagecopyresampled($newimg, $im, 0, 0, 0, 0, 40, 40, $width, $height))
        {
            $log->writelog("Erreur de redimensionnement de l'image");
        }
        //$newimg = $im;
        // on stocke l'image
        imagepng($newimg, $this->chemin . $this->appli . '.png');
        // on retourne l'image pour qu'elle soit affichée
        return $newimg;
    }

    /**
     * Affiche une image
     * @return bool
     */
    public function affiche_image()
    {
        if ($this->images_exists())
        {
            $im = imagecreatefrompng($this->chemin . $this->appli . '.png');
            header($this->header);
            imagepng($im);
            imagedestroy($im);
            return true;
        }
        // si on est ici c'est qu'on n'a pas l'image en local
        if($this->distant_images_exists())
        {
            $im = $this->copy_img_to_local();
            header($this->header);
            imagepng($im);
            imagedestroy($im);
            return true;
        }
        // si on est là, c'est qu'on n'a rien...
        $im = imagecreatefrompng(__DIR__ . '/../../dist/img/no_logo.png');
        header($this->header);
        imagepng($im);
        imagedestroy($im);
        return true;
        
    }
    
}