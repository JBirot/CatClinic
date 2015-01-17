<?php

final class Auteur extends ObjetMetier
{
	private $_I_identifiant;
	
	private $_S_nom;
	
	private $_S_prenom;
	
	//Setters
	public function changeIdentifiant($I_identifiant)
	{
		$this->_I_identifiant = $I_identifiant;
	}
	
	public function changeNom($S_nom)
	{
		$this->_S_nom = $S_nom;
	}
	
	public function changePrenom($S_prenom)
	{
		$this->_S_prenom = $S_prenom;
	}
	
	public function hydrater($I_identifiant,$S_nom,$S_prenom)
	{
		$this->_I_identifiant = $I_identifiant;
		$this->_S_nom = $S_nom;
		$this->_S_prenom = $S_prenom;
	}
	
	//Getters
	public function donneIdentifiant()
	{
		return $this->_I_identifiant;
	}
	
	public function donneNom()
	{
		return $this->_S_nom;
	}
	
	public function donnePrenom()
	{
		return $this->_S_prenom;
	}
	
	//Validation
	public function est_valide()
	{
		//Un autre valide doit avoir:
		//- Un nom
		//- Un prenom
		return (isset($this->_S_nom) && isset($this->_S_prenom));
	}
}