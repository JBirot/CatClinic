<?php
class ArticleMapper extends SqlDataMapper
{
	public function __construct(Connexion $O_connexion)
	{
		parent::__construct(Constantes::TABLE_ARTICLE);
		$this->_S_classeMappee = 'Article';
		$this->_O_connexion = $O_connexion;
	}
	
	public function trouverParIntervalle($I_debut = NULL, $I_fin = NULL)
	{
		//TODO: INNER JOIN OU RECUPERATION DES OBJETS ???
		$S_requete = 'SELECT * FROM ' . $this->_S_nomTable ;
		
		if(!is_null($I_debut)&& !is_null($I_fin))
		{
			$S_requete.= ' LIMIT ?,?';
		}
		
		$A_paramsRequete = array(array($I_debut,Connexion::PARAM_ENTIER), array($I_fin,Connexion::PARAM_ENTIER));
		
		$A_articles = array();
		
		foreach($this->_O_connexion->projeter($S_requete,$A_paramsRequete) as $O_articleEnBase)
		{
			// TODO : si la classe mappée est incorrecte aucun message n'est envoyé pour l'instant
			if($O_article = $this->hydrater($O_articleEnBase))
			{
				$A_articles[] = $O_article;
			}
		}
		
		return $A_articles;
	}
	
	public function trouverParIdentifiant($I_identifiant)
	{
		if(isset($I_identifiant))
		{
			$S_requete = 'SELECT * FROM ' . $this->_S_nomTable . ' WHERE id = ?';
			$A_paramsReq= array($I_identifiant);
			
			if($A_article = $this->_O_connexion->projeter($S_requete,$A_paramsReq))
			{
				$O_articleEnBase = $A_article[0];
				if(is_object($O_articleEnBase))
				{
					$O_article = $this->hydrater($O_articleEnBase);
					return $O_article;
				}
				else
				{
					throw new Exception("Une erreur s'est produite pour l'article d'identifiant " . $I_identifiant);
				}
			}
			else
			{
				throw new Exception("Il n'existe aucun article d'identifiant " . $I_identifiant);
			}
		}
		else
		{
			throw new Exception("L'identifiant d'un article ne peut être vide.");
		}
	}
	
	public function creer(Article $O_article)
	{
		if(!$O_article->estValide())
		{
			throw new Exception("Impossible d'enregistre l'article, il manque des informations.");
		}
		
		$S_titre = $O_article->donneTitre();
		$S_contenu = $O_article->donneContenu();
		$I_idAuteur = $O_article->donneAuteur()->donneIdentifiant();
		$I_idCategorie = $O_article->donneCategorie()->donneIdentifiant();
		
		$S_requete = 'INSERT INTO ' . $this->_S_nomTable . " (titre,contenu,id_auteur,id_categorie) VALUE (?,?,?,?)";
		$A_paramsReq = array($S_titre,$S_contenu,$I_idAuteur,$I_idCategorie);
		
		try
		{
			$O_article->changeIdentifiant($this->_O_connexion->inserer($S_requete,$A_paramsReq));
		}
		catch(PDOException $O_exception)
		{
			throw FabriqueDexceptions::fabriquer($O_exception->getCode(), $this->recupererCible());
		}
	}
	
	public function actualiser(Article $O_article)
	{
		if(null != $O_article->donneIdentifiant())
		{
			if(!$O_article->estValide())
			{
				throw new Exception("Impossible de mettre à jour l'article d'identifiant " . $O_article->donneIdentifiant());
			}
			
			$I_identifiant = $O_article->donneIdentifiant();
			$S_titre = $O_article->donneTitre();
			$S_contenu = $O_article->donneContenu();
			
			// TODO : Verification des id ???
			$I_idAuteur = $O_article->donneAuteur()->donneIdentifiant();
			$I_idCategorie = $O_article->donneCategorie()->donneIdentifiant();
			
			$S_requete = "UPDATE " . $this->_S_nomTable . " SET titre = ?, contenu = ?, id_auteur = ?, id_categorie = ? WHERE id = ?";
			$A_paramsReq = array($S_titre,$S_contenu,$I_idAuteur,$I_idCategorie,$I_identifiant);
			
			$this->_O_connexion->modifier($S_requete, $A_paramsReq);
			
			return true;
		}
		
		return false;
	}
	
	public function supprimer(Article $O_article)
	{
		if(null != $O_article->donneIdentifiant())
		{
			$S_requete = "DELETE FROM " . $this->_S_nomTable . " WHERE id = ?";
			$A_paramsReq = array($O_article->donneIdentifiant());
			
			if(false===$this->_O_connexion->modifier($S_requete, $A_paramsReq))
			{
				throw new Exception("Impossible de supprimer l'article d'identifiant ". $O_article->donneIdentifiant());
			}
			return true;
		}
		
		return false;
	}
	
	private function hydrater($O_articleEnBase)
	{
		if(!class_exists($this->_S_classeMappee)) return false;
		
		$O_article = new $this->_S_classeMappee;
		
		$O_article->changeIdentifiant($O_articleEnBase->id);
		$O_article->changeTitre($O_articleEnBase->titre);
		$O_article->changeContenu($O_articleEnBase->contenu);
		$O_article->changeEnLigne($O_articleEnBase->en_ligne);
		$O_article->changeDate($O_articleEnBase->date);
			
		//RECUPERATION DE LA CATEGORIE
		$O_categorieMapper = FabriqueDeMappers::fabriquer('Categorie', $this->_O_connexion);
		$O_article->changeCategorie($O_categorieMapper->trouverParIdentifiant($O_articleEnBase->id_categorie));
			
		//RECUPERATION DE L'AUTEUR
		$O_auteurMapper = FabriqueDeMappers::fabriquer('Auteur', $this->_O_connexion);
		$O_article->changeAuteur($O_auteurMapper->trouverParIdentifiant($O_articleEnBase->id_auteur));
		
		return $O_article;
	}
}