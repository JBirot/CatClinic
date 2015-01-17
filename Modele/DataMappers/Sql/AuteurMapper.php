<?php

class AuteurMapper extends SqlDataMapper
{
	public function __construct(Connexion $O_connexion)
	{
		parent::__construct(Constantes::TABLE_AUTEUR);
		$this->_S_classeMappee = "Auteur";
		$this->_O_connexion = $O_connexion;
	}
	
	public function trouverParIntervalle($I_debut = NULL, $I_fin = NULL)
	{
		$S_requete = 'SELECT id, nom, prenom FROM ' . $this->_S_nomTable ;
		$A_paramsRequete = null;
		
		if(!is_null($I_debut) && !is_null($I_fin))
		{
			$S_requete.= ' LIMIT ?, ?';
		}
		
		$A_paramsRequete = array(array($I_debut,Connexion::PARAM_ENTIER), array($I_fin,Connexion::PARAM_ENTIER));		
		
		$A_auteurs = array();
		
		foreach ($this->_O_connexion->projeter($S_requete,$A_paramsRequete) as $O_auteurEnBase)
		{
			if($O_auteur = $this->hydrater($O_auteurEnBase))
			{
				$A_auteurs[] = $O_auteur;
			}
		}
		
		return $A_auteurs;
	}
	
	public function trouverParIdentifiant($I_identifiant)
	{
	    if(!isset($I_identifiant))
    	{
    		throw new Exception("L'identifiant d'un utilisateur ne peut être vide.");
    	}
		$S_requete 	= "SELECT id,nom,prenom FROM " . $this->_S_nomTable . " WHERE id = ?";
		$A_paramsReq= array($I_identifiant);
		if($A_auteur = $this->_O_connexion->projeter($S_requete,$A_paramsReq))
		{
			$O_auteurEnBase = $A_auteur[0];
			if(is_object($O_auteurEnBase))
			{
				if(!$O_auteur = $this->hydrater($O_auteurEnBase)) throw new Exception("La classe mappée " . $this->_S_classeMappee . " est introuvable.");
				return $O_auteur;
			}
			else 
			{
				throw new Exception("Une erreur s'est produite pour l'auteur d'identifiant ".$I_identifiant);
			}
		}
		else
		{
			throw new Exception("Il n'existe pas d'auteur pour l'identifiant " . $I_identifiant);
		}
	}
	
	public function creer(Auteur $O_auteur)
	{
		if(!$O_auteur->est_valide())
		{
			throw new Exception("Impossible d'enregistrer l'auteur, il manque des informations.");
		}
		
		$S_nom = $O_auteur->donneNom();
		$S_prenom = $O_auteur->donnePrenom();
		
		$S_requete	=	"INSERT INTO " . $this->_S_nomTable . " (nom,prenom) VALUES (?,?)";
		$A_paramsReq=	array($S_nom,$S_prenom);
		
		try
		{
			$O_auteur->changeIdentifiant($this->_O_connexion->inserer($S_requete, $A_paramsReq));
		}
		catch (PDOException $O_exception)
		{
			throw FabriqueDexceptions::fabriquer($O_exception->getCode(), $this->recupererCible());
		}
	}
	
	public function actualiser(Auteur $O_auteur)
	{
		if(null == $O_auteur->donneIdentifiant())
		{
			throw new Exception("Impossible de trouver l'identifiant de l'auteur à modifier.");
		}
		if (!$O_auteur->est_valide())
		{
			throw new Exception("Impossible de mettre à jour l'auteur d'identifiant " . $O_auteur->donneIdentifiant());
		}
		
		$I_identitiant = $O_auteur->donneIdentifiant();
		$S_nom = $O_auteur->donneNom();
		$S_prenom = $O_auteur->donnePrenom();
		
		$S_requete 	= 	"UPDATE " . $this->_S_nomTable . " SET nom = ?, prenom = ? WHERE id = ?";
		$A_paramsReq=	array($S_nom,$S_prenom,$I_identifiant);
		
		if(false===$this->_O_connexion->modifier($S_requete, $A_paramsReq))
		{
			throw new Exception("Impossible de modifier l'auteur d'identifiant ". $I_identitiant);
		}
		return true;
	}
	
	public function supprimer(Auteur $O_auteur)
	{
		if(null == $O_auteur->donneIdentifiant())
		{
			throw new Exception("Impossible de trouver l'identifiant de l'auteur à supprimer.");	
		}
		$S_requete 	= 	"DELETE FROM " . $this->_S_nomTable . " WHERE id = ?";
		$A_paramsReq=	array($O_auteur->donneIdentifiant());
			
		if(false===$this->_O_connexion->modifier($S_requete, $A_paramsReq))
		{
			throw new Exception("Impossible de supprimer l'auteur d'identifiant " . $O_auteur->donneIdentifiant());
		}
			
		return true;
	}
	
	private function hydrater($O_auteurEnBase)
	{
		if(!class_exists($this->_S_classeMappee)) return false;
		
		$O_auteur = new $this->_S_classeMappee;
		
		$O_auteur->changeIdentifiant($O_auteurEnBase->id);
		$O_auteur->changeNom($O_auteurEnBase->nom);
		$O_auteur->changePrenom($O_auteurEnBase->prenom);
		
		return $O_auteur;
	}
}