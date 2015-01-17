<?php

final class ControleurCategorie
{
	private $_S_urlDefaut;
	private $_S_urlCreation;
	private $_S_urlEdition;
	
	public function __construct()
	{
		Authentification::accesAdministrateur();
		$this->_S_urlDefaut = 'categorie';
		$this->_S_urlCreation = 'categorie';
		$this->_S_urlEdition = 'categorie/edit/';
	}
	
	public function defautAction()
	{
		BoiteAOutils::redirigerVers('categorie/liste');
	}
	
	public function listeAction(Array $A_parametres = null)
	{
		$I_page = isset($_SESSION["page_categorie"]) ? BoiteAOutils::recupererDepuisSession("page_categorie") : 1 ;
		$I_page = isset($A_parametres[0]) ? $A_parametres[0] : $I_page;
		$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
	
		$O_listeur = new Listeur($O_categorieMapper);
		$O_paginateur = new Paginateur($O_listeur);
		$O_paginateur->changeLimite(Constantes::NB_MAX_ARTICLES_PAR_PAGE);
	
		// on doit afficher puis installer la pagination
		try 
		{
			$A_categories = $O_paginateur->recupererPage($I_page);
		}
		catch (Exception $O_exception)
		{
			//Si la récupération de la 1ère page de la liste échoue on repart à la base du site, autrement on revient sur la précèdente
			$S_url = $I_page == 1 ? '' : $this->_S_urlDefaut;
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($S_url);
		}
	
		$A_pagination = $O_paginateur->paginer();
		
		BoiteAOutils::rangerDansSession("page_categorie", $I_page);
	
		//Affichage
		Vue::montrer('articles/categories/form');
		Vue::montrer ('articles/categories/liste', array('categories' => $A_categories, 'pagination' => $A_pagination));
	}
	
	public function creerAction()
	{
		//Récupération des données du formulaire
		$O_formulaire = new Formulaire(array(
			'categorie_nouveau_titre' => 'texte'	
		));
		
		//Vérification de ces données
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
		}
		
		//Création de la nouvelle catégorie
		$O_categorie = new Categorie();
		$O_categorie->changeTitre($O_formulaire->donneContenu('categorie_nouveau_titre'));
		
		//Enregistrement dans la base
		try
		{
			$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
			$O_categorie->changeIdentifiant($O_categorieMapper->creer($O_categorie));
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		
		//Préparation de l'affichage du message de confirmation et redirection
		BoiteAOutils::stockerMessage("La catégorie " . $O_categorie->donneTitre() . " est bien enregistrée.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function editAction(Array $A_parametres)
	{
		if(!$I_idCategorie = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de la catégorie à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		try
		{
			$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
			$O_categorie = $O_categorieMapper->trouverParidentifiant($I_idCategorie);
		}
		catch(Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		Vue::montrer('articles/categories/edit', array('categorie' => $O_categorie));	
	}
	
	public function miseajourAction(Array $A_parametres)
	{
		//Recherche de l'identifiant de la catégorie à modifier
		if(!$I_idCategorie = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de la catégorie à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Vérification de l'ancienne catégorie
		try
		{
			$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
			$O_categorie = $O_categorieMapper->trouverParIdentifiant($I_idCategorie);
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Vérification des données du formulaire
		$O_formulaire = new Formulaire(array(
			'categorie_titre_' . $I_idCategorie	=>	"texte"	
		));
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idCategorie);
			return false;
		}
		$S_titre = $O_formulaire->donneContenu('categorie_titre_'.$I_idCategorie);
		
		//Modification de l'objet
		if($O_categorie->donneTitre() != $S_titre)
		{
			$O_categorie->changeTitre($S_titre);
			try 
			{
				$O_categorieMapper->actualiser($O_categorie);
			}
			catch (Exception $O_exception)
			{
				BoiteAOutils::stockerErreur($O_exception->getMessage());
				BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idCategorie);
				return false;
			}
		}
		
		BoiteAOutils::stockerMessage("La catégorie d'identifiant " . $I_idCategorie . " est bien modifiée.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function supprAction(Array $A_parametres)
	{
		if(!$I_idCategorie = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de la catégorie à supprimer.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		try 
		{
			$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
			$O_categorie = $O_categorieMapper->trouverParIdentifiant($I_idCategorie);
			$O_categorieMapper->supprimer($O_categorie);	
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		BoiteAOutils::stockerMessage("La catégorie d'idenfitiant " . $I_idCategorie . " est supprimée.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
}