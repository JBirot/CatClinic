<?php

class Categorie extends ObjetMetier
{
	private $_I_identifiant;
	
	private $_S_titre;
	
	
	//Setters
	public function changeIdentifiant($I_identifiant)
	{
		$this->_I_identifiant = $I_identifiant;
	}
	
	public function changeTitre($S_titre)
	{
		$this->_S_titre = $S_titre;
	}
	
	//Getters
	public function donneIdentifiant()
	{
		return $this->_I_identifiant;
	}
	public function donneTitre()
	{
		return $this->_S_titre;
	}

	//Validation
	public function est_valide()
	{
		//Pour qu'une catégorie soit valide il lui faut:
		//- Un titre
		return (isset($this->_S_titre));
	}
}