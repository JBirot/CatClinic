<?php

final class Article extends ObjetMetier
{
	private $_I_identifiant;
	
	private $_O_categorie;
	
	private $_O_auteur;
	
	private $_S_titre;
	
	private $_S_contenu;
	
	private $_I_enLigne;
	
	private $_O_date;
	
	//Setters
	public function changeIdentifiant($I_identifiant)
	{
		$this->_I_identifiant = $I_identifiant;
	}
	
	public function changeCategorie(Categorie $O_categorie)
	{
		$this->_O_categorie = $O_categorie;
	}
	
	public function changeAuteur(Auteur $O_auteur)
	{
		$this->_O_auteur = $O_auteur;
	}
	
	public function changeTitre($S_titre)
	{
		$this->_S_titre = $S_titre;
	}
	
	public function changeContenu($S_contenu)
	{
		$this->_S_contenu = $S_contenu;
	}
	
	public function changeDate($O_date)
	{
		$this->_O_date = $O_date;
	}
	
	public function changeEnLigne($I_enLigne)
	{
		$this->_I_enLigne = $I_enLigne;
	}
	
	public function hydrater($S_titre, $S_contenu, $O_categorie, $O_auteur,$I_enLigne)
	{
		$this->_S_titre = $S_titre;
		$this->_S_contenu = $S_contenu;
		$this->_O_categorie = $O_categorie;
		$this->_O_auteur = $O_auteur;
		$this->_I_enLigne = $I_enLigne;
	}
	
	//Getters
	public function donneIdentifiant()
	{
		return $this->_I_identifiant;
	}
	
	public function donneCategorie()
	{
		return $this->_O_categorie;
	}
	
	public function donneCategorieId()
	{
		return $this->_O_categorie->donneIdentifiant();
	}
	
	public function donneCategorieTitre()
	{
		return $this->_O_categorie->donneTitre();
	}
	
	public function donneAuteur()
	{
		return $this->_O_auteur;
	}
	
	public function donneAuteurId()
	{
		return $this->_O_auteur->donneIdentifiant();
	}
	
	public function donneAuteurNom()
	{
		return $this->_O_auteur->donneNom();
	}
	
	public function donneAuteurPrenom()
	{
		return $this->_O_auteur->donnePrenom();
	}
	
	public function donneTitre()
	{
		return $this->_S_titre;
	}
	
	public function donneContenu()
	{
		return $this->_S_contenu;
	}
	
	public function donneDate()
	{
		return $this->_O_date;
	}
	public function estEnLigne()
	{
		return $this->_I_enLigne;
	}
	
	//Validation
	public function estValide()
	{
		return($this->_O_auteur->est_valide() && $this->_O_categorie->est_valide() && isset($this->_S_contenu) && isset($this->_S_titre)&&(isset($this->_I_enLigne)));
	}
}