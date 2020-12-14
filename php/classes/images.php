<?php


class images
{
    public $appli;
    
    public function __construct($appli)
    {
        $this->appli = $appli;   
    }
    
    private function images_exists()
    {
        return file_exists(__DIR__ . '/../../dist/appli_img/' . $this->appli . '.png');
    }
    
    private function distant_images_exists()
    {
        return curl_init("https://www.scriptseedboxdocker.com/wp-content/uploads/icones/" . $this->appli . ".png") !== false;
    }
    
    private function copy_img_to_local()
    {
        $log = new log;
        // d'abord on copie l'image vers un fichier temporaire
        $tmpfname = tempnam("/tmp", "img");
        if(!copy("https://www.scriptseedboxdocker.com/wp-content/uploads/icones/" . $this->appli . ".png",$tmpfname))
        {
            
            $log->writelog("Erreur de copie de fichier distant :  ");
            $log->writelog("https://www.scriptseedboxdocker.com/wp-content/uploads/icones/" . $this->appli . ".png");
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
        if(!imagecopyresampled($newimg,$im,0,0,0,0,40,40,$width,$height))
        {
            $log->writelog("Erreur de redimensionnement de l'image");
        }
        //$newimg = $im;
        // on stocke l'image
        imagepng($newimg,__DIR__ . '/../../dist/appli_img/' . $this->appli . '.png');
        // on retourne l'image pour qu'elle soit affichée
        return $newimg;
    }
    
    
    public function affiche_image()
    {
        $log = new log;
        if ($this->images_exists())
        {
            //$log->writelog("Image " . $this->appli . " locale existante");
            $im = imagecreatefrompng(__DIR__ . '/../../dist/appli_img/' . $this->appli . '.png');
            header('Content-Type: image/png');
            imagepng($im);
            imagedestroy($im);
            return true;
        }
        // si on est ici c'est qu'on n'a pas l'image en local
        if($this->distant_images_exists())
        {
            //$log->writelog("Image " . $this->appli . " distante existante");
            $im = $this->copy_img_to_local();
            header('Content-Type: image/png');
            imagepng($im);
            imagedestroy($im);
            return true;
        }
        // si on est là, c'est qu'on n'a rien...
        $im = imagecreatefrompng(__DIR__ . '/../../dist/img/no_logo.png');
        header('Content-Type: image/png');
        imagepng($im);
        imagedestroy($im);
        return true;
        
    }
    
}