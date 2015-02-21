<?php
class ArticleMapper extends SqlDataMapper
{
	public function __construct(Connexion $O_connexion)
	{
		parent::__construct(Constantes::TABLE_ARTICLE);
		$this->_A_champsTriables = array('id','titre','id_categorie','id_auteur','en_ligne','date');
		$this->_S_classeMappee = 'Article';
		$this->_O_connexion = $O_connexion;
	}
	
	public function trouverParIntervalle($I_debut = NULL, $I_fin = NULL,array $A_ordre = null)
	{
		$S_requete = 'SELECT * FROM ' . $this->_S_nomTable ;
		$A_paramsReq = null;
		
	    if($A_ordre)
        {
        	if(isset($this->_A_champsTriables[$A_ordre[0]]))
        	{
        		$S_sens = $A_ordre[1] ? 'DESC' : 'ASC';
        		$S_requete .= ' ORDER BY '.$this->_A_champsTriables[$A_ordre[0]].' '.$S_sens.' ';
	       	}
        }
		
		if (null !== $I_debut && null !== $I_fin)
		{
			$S_requete .= ' LIMIT :debut, :fin';
			$A_paramsReq['debut'] = array(intval($I_debut), Connexion::PARAM_ENTIER);
			$A_paramsReq['fin'] = array(intval($I_fin), Connexion::PARAM_ENTIER);
		}
		$A_articles = array();
		
		foreach($this->_O_connexion->projeter($S_requete,$A_paramsReq) as $O_articleEnBase)
		{
			// Si la classe mappée est incorrecte aucun message n'est envoyé, seulement une variable vide
			if($O_article = $this->hydrater($O_articleEnBase))
			{
				$A_articles[] = $O_article;
			}
		}
		
		return $A_articles;
	}
	
	public function compterEnLigne()
	{
		$S_requete = 'SELECT count(*) AS nb FROM ' . $this->recupererCible() .' WHERE en_ligne = 1';

        $A_enregistrements = $this->_O_connexion->projeter($S_requete);
        $O_enregistrement = $A_enregistrements[0];

        return $O_enregistrement->nb;	
	}
	
	public function trouverParEnLigne($I_debut = NULL,$I_fin = NULL)
	{
		$S_requete = "SELECT * FROM ".$this->_S_nomTable." WHERE en_ligne = 1 ORDER BY date DESC";
		$A_paramsReq=null;
		if (null !== $I_debut && null !== $I_fin)
		{
			$S_requete .= ' LIMIT :debut, :fin';
			$A_paramsReq['debut'] = array(intval($I_debut), Connexion::PARAM_ENTIER);
			$A_paramsReq['fin'] = array(intval($I_fin), Connexion::PARAM_ENTIER);
		}
		$A_articles = array();
		foreach($this->_O_connexion->projeter($S_requete,$A_paramsReq) as $O_articleEnBase)
		{
			// Si la classe mappée est incorrecte aucun message n'est envoyé pour l'instant
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
	
	public function trouverParCategorie(Categorie $O_categorie)
	{
		if(null===$O_categorie->donneIdentifiant())
		{
			throw new Exception("Impossible de trouver l'identifiant de la catégorie pour rechercher l'article.");
		}
		$S_requete = 'SELECT * FROM '.$this->_S_nomTable.' WHERE id_categorie = ?';
		$A_paramsReq = array($O_categorie->donneIdentifiant());
		$A_articles = array();
		foreach ($this->_O_connexion->projeter($S_requete,$A_paramsReq) as $O_articleEnBase)
		{
			if($O_article = $this->hydrater($O_articleEnBase))
			{
				$A_articles[] = $O_article;
			}
		}
		return $A_articles;
	}
	
	public function trouverParAuteur(Auteur $O_auteur)
	{
		if(null===$O_auteur->donneIdentifiant())
		{
			throw new Exception("Impossible de trouver l'identifiant de la catégorie pour rechercher l'article.");
		}
		$S_requete = 'SELECT * FROM '.$this->_S_nomTable.' WHERE id_auteur = ?';
		$A_paramsReq = array($O_auteur->donneIdentifiant());
		$A_articles = array();
		foreach ($this->_O_connexion->projeter($S_requete,$A_paramsReq) as $O_articleEnBase)
		{
			if($O_article = $this->hydrater($O_articleEnBase))
			{
				$A_articles[] = $O_article;
			}
		}
		return $A_articles;
	}
	
	public function creer(Article $O_article)
	{
		if(!$O_article->estValide())
		{
			throw new Exception("Impossible d'enregistre l'article, il manque des informations.");
		}
		
		$S_titre = $O_article->donneTitre();
		$S_contenu = $O_article->donneContenu();
		
		// Vérification des auteurs et des categories
		$O_auteur = $O_article->donneAuteur();
		if(!$O_auteur){
			throw new Exception("Impossible de mettre à jour l'article, l'auteur est manquant.");
		}
		$O_categorie = $O_article->donneCategorie();
		if(!$O_categorie)
		{
			throw new Exception("Impossible de mettre à jour l'article, la catégorie est manquante.");
		}
		$I_idAuteur = $O_auteur->donneIdentifiant();
		$I_idCategorie = $O_categorie->donneIdentifiant();
			
		if ( !$I_idAuteur || !$I_idCategorie) {
			throw new Exception ("Impossible de créer l'article, des informations sont manquantes");
		}
		
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
			$I_enLigne = $O_article->estEnLigne();
			
			// Vérification des auteurs et des categories
			$O_auteur = $O_article->donneAuteur();
			if(!$O_auteur){
				throw new Exception("Impossible de mettre à jour l'article, l'auteur est manquant.");
			}
			$O_categorie = $O_article->donneCategorie();
			if(!$O_categorie)
			{
				throw new Exception("Impossible de mettre à jour l'article, la catégorie est manquante.");
			}
			$I_idAuteur = $O_auteur->donneIdentifiant();
			$I_idCategorie = $O_categorie->donneIdentifiant();
			
			if ( !$I_idAuteur || !$I_idCategorie) {
				throw new Exception ("Impossible de créer l'article, des informations sont manquantes");
			}
			
			$S_requete = "UPDATE " . $this->_S_nomTable . " SET titre = ?, contenu = ?, id_auteur = ?, id_categorie = ?, en_ligne = ? WHERE id = ?";
			$A_paramsReq = array($S_titre,$S_contenu,$I_idAuteur,$I_idCategorie,$I_enLigne,$I_identifiant);
			
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