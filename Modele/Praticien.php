<?php

final class Praticien extends ObjetMetier
{
    private $_I_identifiant;
        
    private $_S_nom;

    private $_S_prenom;

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
    
    public function hydrater(array $A_infos)
    {
    	if(count($A_infos)==2){
    	$this->_S_nom = array_shift($A_infos);
    	$this->_S_prenom = array_shift($A_infos);
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
}