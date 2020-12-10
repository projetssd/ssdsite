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
        global $debugbar;
        global $mode_debug;
        
        $commande = 'cd ' . __DIR__ . '/../../ && git fetch';
        $fetch = exec($commande,$rettab,$retvalue);
        if($mode_debug)
        {
           $debugbar['messages']->addMessage('fetch :');
           $debugbar['messages']->addMessage($commande);
            $debugbar['messages']->addMessage($rettab);
            $debugbar['messages']->addMessage($retvalue);
        }
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
            "MESSAGE" => $message,
            "BRANCH" => $this->branch,
            "CURRENT_HASH" => $this->current_hash,
            "DISTANT_HASH" => $this->distant_hash);
    }
}