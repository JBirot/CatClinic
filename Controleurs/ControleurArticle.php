<?php

final class ControleurArticle
{
	private $_S_urlDefaut;
	private $_S_urlCreation;
	private $_S_urlEdition;
	
	public function __construct($S_method = NULL)
	{
		Authentification::accesAdministrateur();
		$this->_S_urlDefaut = 'article';
		$this->_S_urlCreation = 'article';
		$this->_S_urlEdition = 'article/edit/';	
	}
	
	public function defautAction()
	{
		BoiteAOutils::redirigerVers('article/liste');
	}
	
	public function listeAction(array $A_parametres)
	{
		$I_page = isset($_SESSION['page_article']) ? BoiteAOutils::recupererDepuisSession('page_article') : 1 ;
		$I_page = isset($A_parametres[0]) ? $A_parametres[0] : $I_page;
		$O_articleMapper = FabriqueDeMappers::fabriquer('article', Connexion::recupererInstance());
	
		$O_listeur = new Listeur($O_articleMapper);
		$O_paginateur = new Paginateur($O_listeur);
		$O_paginateur->changeLimite(Constantes::NB_MAX_ARTICLES_PAR_PAGE);
	
		// on doit afficher puis installer la pagination
		try
		{
			$A_articles = $O_paginateur->recupererPage($I_page);
		}
		catch(Exception $O_exception)
		{
			//Si la récupération de la 1ère page de la liste échoue on repart à la base du site, autrement on revient sur la précèdente
			$S_url = $I_page == 1 ? '' : $this->_S_urlDefaut;
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($S_url);
		}
		
		$A_pagination = $O_paginateur->paginer();
		
		BoiteAOutils::rangerDansSession('page_article', $I_page);
		
		//récupération des catégories et des auteurs
		try 
		{
			$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
			$A_categories = $O_categorieMapper->trouverParIntervalle(null,null);
			$O_auteurMapper = FabriqueDeMappers::fabriquer('auteur', Connexion::recupererInstance());
			$A_auteurs = $O_auteurMapper->trouverParIntervalle(null,null);	
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers('');
			return false;
		}
	
		// voir ce qu'on met dans utilisateurs !
		Vue::montrer ('articles/form', array('categories' => $A_categories, 'auteurs' => $A_auteurs));
		Vue::montrer ('articles/liste', array('articles' => $A_articles, 'pagination' => $A_pagination));
	}
	
	public function creerAction()
	{
		//Récupération des données du formulaire
		$O_formulaire = new Formulaire(array(
			'article_nouveau_titre' => 'Texte',
			'article_nouveau_contenu'	=> 'Texte',
			'article_nouveau_categorie'	=> 'Id',
			'article_nouveau_auteur'	=> 'Id'			
		));
		//Vérification de ces données
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		//Vérification de la catégorie
		try 
		{
			$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
			$O_categorie = $O_categorieMapper->trouverParIdentifiant($O_formulaire->donneContenu('article_nouveau_categorie'));
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		//Vérification de l'auteur
		try 
		{
			$O_auteurMapper = FabriqueDeMappers::fabriquer('auteur', Connexion::recupererInstance());
			$O_auteur = $O_auteurMapper->trouverParIdentifiant($O_formulaire->donneContenu('article_nouveau_auteur'));
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		//Création du nouvel article
		$O_article = new Article();
		$O_article->hydrater(	$O_formulaire->donneContenu('article_nouveau_titre'),
								$O_formulaire->donneContenu('article_nouveau_contenu'),
								$O_categorie,
								$O_auteur	);
		//Enregistrement dans la base
		try 
		{
			$O_articleMapper = FabriqueDeMappers::fabriquer('article', Connexion::recupererInstance());
			$O_article->changeIdentifiant($O_articleMapper->creer($O_article));
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		
		//Préparation de la confirmation et redirection
		BoiteAOutils::stockerMessage("L'article " . $O_article->donneTitre() . " est bien créé.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function editAction(array $A_parametres)
	{
		if (!$I_idArticle = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'article à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}

		try 
		{
			$O_articleMapper = FabriqueDeMappers::fabriquer('article', Connexion::recupererInstance());
			$O_article = $O_articleMapper->trouverParIdentifiant($I_idArticle);

			$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
			$A_categories = $O_categorieMapper->trouverParIntervalle();
			
			$O_auteurMapper = FabriqueDeMappers::fabriquer('auteur', Connexion::recupererInstance());
			$A_auteurs = $O_auteurMapper->trouverParIntervalle();
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		Vue::montrer('articles/edit', array('article' => $O_article, 'categories' => $A_categories, 'auteurs' => $A_auteurs));
	}
	
	public function miseajourAction(array $A_parametres)
	{	
		//Recherche de l'identifiant de l'article à modifier
		if(!$I_idArticle = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'article à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Vérification de l'ancien article
		try 
		{
			$O_articleMapper = FabriqueDeMappers::fabriquer('article', Connexion::recupererInstance());
			$O_article = $O_articleMapper->trouverParIdentifiant($I_idArticle);	
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Vérification ddes données du formulaire
		$O_formulaire = new Formulaire(array(
			'article_titre_'.$I_idArticle	=> 'Texte',
			'article_contenu_'.$I_idArticle => 'Texte',
			'article_categorie_'.$I_idArticle => 'Id',
			'article_auteur_'.$I_idArticle	=> 'Id'	
		));
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idArticle);
			return false;
		}
		$S_titre = $O_formulaire->donneContenu('article_titre_'.$I_idArticle);
		$S_contenu = $O_formulaire->donneContenu('article_contenu_'.$I_idArticle);
		$I_idCategorie = $O_formulaire->donneContenu('article_categorie_'.$I_idArticle);
		$I_idAuteur = $O_formulaire->donneContenu('article_auteur_'.$I_idArticle);
		
		//Modification de l'objet si nécessaire
		if($O_article->donneTitre() != $S_titre || $O_article->donneContenu() != $S_contenu ||
				$O_article->donneCategorieId() != $I_idCategorie ||
				$O_article->donneAuteurId() != $I_idAuteur)
		{	
			$O_article->changeTitre($S_titre);
			$O_article->changeContenu($S_contenu);		
			//Vérification de la catégorie si nécessaire
			if($I_idCategorie != $O_article->donneCategorie()->donneIdentifiant())
			{	
				try 
				{
					$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
					$O_article->changeCategorie($O_categorieMapper->trouverParIdentifiant($O_formulaire->donneContenu('article_categorie_'.$I_idArticle)));
				}
				catch (Exception $O_exception)
				{
					BoiteAOutils::stockerErreur($O_exception->getMessage());
					BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idArticle);
					return false;
				}
			}
			//Vérification de l'auteur si nécessaire
			if($I_idAuteur != $O_article->donneAuteur()->donneIdentifiant())
			{
				try 
				{
					$O_auteurMapper = FabriqueDeMappers::fabriquer('auteur', Connexion::recupererInstance());
					$O_article->changeAuteur($O_auteurMapper->trouverParIdentifiant($O_formulaire->donneContenu('article_auteur_'.$I_idArticle)));	
				}
				catch (Exception $O_exception)
				{
					BoiteAOutils::stockerErreur($O_exception->getMessage());
					BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idArticle);
					return false;
				}
			}
			//Enregistrement en base
			try 
			{
				$O_articleMapper->actualiser($O_article);
			}
			catch (Exception $O_exception)
			{
				BoiteAOutils::stockerErreur($O_exception->getMessage());
				BoiteAOutils::redirigerVers($this->_S_urlEdition);
				return false;
			}			
		}
		
		BoiteAOutils::stockerMessage("L'article d'identifiant ".$I_idArticle." a bien été modifié.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function supprAction(Array $A_parametres)
	{
		if(!$I_idArticle = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'article à supprimer.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		try 
		{
			$O_articleMapper = FabriqueDeMappers::fabriquer('article', Connexion::recupererInstance());
			$O_article = $O_articleMapper->trouverParIdentifiant($I_idArticle);
			$O_articleMapper->supprimer($O_article);
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		BoiteAOutils::stockerMessage("L'article d'identifiant ".$I_idArticle." a bien été supprimé.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
}