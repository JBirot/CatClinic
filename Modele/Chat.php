<?php

final class Chat extends ObjetMetier
{
    private $_I_identifiant;

    private $_D_age;

    private $_S_tatouage;

    private $_S_nom;

    // Mes mutateurs (setters)
    public function changeIdentifiant ($S_identifiant)
    {
        $this->_I_identifiant = $S_identifiant;
    }

    public function changeAge ($I_age)
    {
        $this->_D_age = $I_age;
    }

    public function changeNom ($S_nom)
    {
        $this->_S_nom = $S_nom;
    }

    public function changeTatouage ($S_tatouage)
    {
        $this->_S_tatouage = $S_tatouage;
    }
    
    public function hydrater(array $A_infos)
    {
    	if (count($A_infos)==3)
    	{
    		$this->_S_nom = array_shift($A_infos);
    		$this->_D_age = array_shift($A_infos);
    		$this->_S_tatouage = array_shift($A_infos);
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
	
    public function donneDate()
    {
    	return $this->_D_age;
    }
    
    public function donneAge ()
    {
    	$O_birth = new DateTime($this->_D_age);
    	$O_today = new DateTime();
    	$O_age = $O_birth->diff($O_today,true);
    	$S_format = $O_age->format("%y")>1 ? '%y ans ' : ($O_age->format("%y")==1? '%y an':'');
    	$S_format .= $O_age->format('%m')>0 ? '%m mois' : '';
    	$S_format = $S_format ? $S_format : '%d jours';
    	return $O_age->format($S_format);
    }

    public function donneTatouage ()
    {
        return $this->_S_tatouage;
    }
}