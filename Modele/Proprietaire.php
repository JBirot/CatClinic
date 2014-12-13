<?php

final class Proprietaire extends ObjetMetier
{
    private $_I_identifiant;
        
    private $_S_nom;

    private $_S_prenom;

    private $_A_chats = array();

    // Mes mutateurs (setters)
    public function changeIdentifiant ($I_identifiant)
    {
        $this->_I_identifiant = $I_identifiant;
    }

    public function changeNom ($S_nom)
    {
        $this->_S_nom = $S_nom;
    }

    public function changePrenom ($S_prenom)
    {
        $this->_S_prenom = $S_prenom;
    }

    public function ajouteChat (Chat $O_chat = null)
    {
        if (!in_array($O_chat, $this->_A_chats)) {
            $this->_A_chats[] = $O_chat;
        }
    }
    
    public function changeChats (Array $A_chats)
    {
    	$this->_A_chats = $A_chats;
    }
    
    public function enleveChat (Chat $O_chat = null)
    {
    	if (false !== ($I_index = array_search($O_chat, $this->_A_chats))) {
    		array_push($this->_A_operations[static::SUPPRESSION], $this->_A_chats[$I_index]);
    		unset ($this->_A_chats[$I_index]);
    	}
    }

    // Mes accesseurs (getters)

    public function donneIdentifiant ()
    {
        return $this->_I_identifiant;
    }

    public function donneNom ()
    {
        return $this->_S_nom;
    }

    public function donnePrenom ()
    {
        return $this->_S_prenom;
    }

    public function donneChats ()
    {
        return $this->_A_chats;
    }
}