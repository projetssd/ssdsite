<?php

/**
 * Class git
 * 
 */
class git
{
    private $branch;
    private $current_hash;
    private $distant_hash;
    
    public function __construct()
    {
        $this->get_branch();
        $this->get_current_hash();
        $this->get_distant_hash();
    }
    
    private function get_branch()
    {
        $this->branch = trim(shell_exec('git rev-parse --abbrev-ref HEAD'));
    }
    
    private function get_current_hash()
    {
        $this->current_hash = trim(shell_exec('git rev-parse HEAD'));
    }
    
    private function get_distant_hash()
    {
        $this->distant_hash = trim(shell_exec('git rev-parse main@{upstream}'));
    }
    
    public function test_update()
    {
        $majafaire = false;
        $message = '';
        if($this->branch != 'main')
        {
            $majafaire = true;
            $message = "Vous n'êtes pas sur la branche master !";
        }
        else
        {
            if($this->current_hash != $this->distant_hash)
            {
                $majafaire = true;
                $message = "Votre application n'est pas à jour";
            }
        }
        return array("MAJAFAIRE" => $majafaire,
            "MESSAGE" => $message);
    }
}