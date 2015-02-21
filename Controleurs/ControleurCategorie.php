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
		BoiteAOutils::redirigerVers('categorie/liste/'.BoiteAOutils::recupererDepuisSession('page_categorie'));
	}
	
	public function changerLimiteAction()
	{
		$O_formulaire = new Formulaire(array(
				'limite_categories_new' => 'id'
		));
	
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
	
		BoiteAOutils::rangerDansSession('limite_categories', $O_formulaire->donneContenu('limite_categories_new'));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function changerOrdreAction(Array $A_parametres)
	{
		if(count($A_parametres)<2)
		{
			BoiteAOutils::stockerErreur("Il manque des parametres pour changer le tri de la liste des catégories.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		$I_champ = $A_parametres[0];
		$I_sens = $A_parametres[1];
		if(($I_champ !== '0' && $I_champ !== '1') || ($I_sens !== '0' && $I_sens !== '1'))
		{
			BoiteAOutils::stockerErreur("L'un des parametres pour changer le tri de la liste des catégories est incorrect.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		BoiteAOutils::rangerDansSession('ordre_categories', array($I_champ,$I_sens));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function listeAction(Array $A_parametres = null)
	{
		$I_page = isset($_SESSION["page_categorie"]) ? BoiteAOutils::recupererDepuisSession("page_categorie") : 1 ;
		$I_page = isset($A_parametres[0]) ? $A_parametres[0] : $I_page;
		$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
	
		$O_listeur = new Listeur($O_categorieMapper);
		$O_paginateur = new Paginateur($O_listeur);
		$I_limite = BoiteAOutils::recupererDepuisSession('limite_categories')?BoiteAOutils::recupererDepuisSession('limite_categories'):Constantes::NB_MAX_ARTICLES_PAR_PAGE;
		$A_ordre = BoiteAOutils::recupererDepuisSession('ordre_categories');
		$O_paginateur->changeLimite($I_limite);
		$O_paginateur->changeOrdre($A_ordre);
	
		// on doit afficher puis installer la pagination
		try 
		{
			$A_categories = $O_paginateur->recupererPage($I_page);
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers('');
		}
	
		$A_pagination = $O_paginateur->paginer();
		
		BoiteAOutils::rangerDansSession("page_categorie", $I_page);
	
		//Affichage
		Vue::montrer ('articles/categories/liste', array('categories' => $A_categories, 'pagination' => $A_pagination,'ordre'=>$A_ordre));
	}
	
	public function validationAction()
	{	//Recherche des champs à valider
		if (isset($_POST['categorie_modif_all']))
		{
			if(isset($_POST['categorie_toMod']))
			{
				foreach ($_POST['categorie_toMod'] as $I_identifiant)
				{
					$A_ids[] = $I_identifiant;
					$A_champs['categorie_Titre_'.$I_identifiant] = 'texte';
				}
			}
		}
		else
		{
			foreach($_POST as $S_inputName => $value)
			{
				if(preg_match("/^categorie_modif_[0-9]*/",$S_inputName ))
				{
					$I_identifiant = str_replace('categorie_modif_', '', $S_inputName);
					$A_ids[] = $I_identifiant;
					$A_champs['categorie_Titre_'.$I_identifiant] = 'texte';
				}
			}
		}
		if(!empty($A_champs))
		{	//Recuperation des données du formulaire
			$O_formulaire = new Formulaire($A_champs);
			//Validation
			if(!$O_formulaire->estValide())
			{
				BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
				BoiteAOutils::redirigerVers($this->_S_urlDefaut);
				return false;
			}
			//Enregistrement en base
			$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
			foreach ($A_ids as $I_identifiant)
			{
				try{
					$O_categorie = $O_categorieMapper->trouverParIdentifiant($I_identifiant);
					$O_categorie->changeTitre($O_formulaire->donneContenu('categorie_Titre_'.$I_identifiant));
					$O_categorieMapper->actualiser($O_categorie);
				}catch (Exception $O_exception){
					BoiteAOutils::stockerErreur($O_exception->getMessage());
					BoiteAOutils::redirigerVers($this->_S_urlDefaut);
					return false;
				}
			}
			//Message de confirmation et redirection
			BoiteAOutils::stockerMessage('Modification des catégories n°'.implode(', ', $A_ids));
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return true;
		}
		else
		{	//Aucune modification
			BoiteAOutils::stockerErreur('Aucune modification trouvée.');
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
	}
	
	public function creationAction()
	{
		Vue::montrer('/articles/categories/form');	
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
	
	public function suppressionAction(Array $A_parametres)
	{
		if(!$I_idCategorie = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de la catégorie à supprimer.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		try{
			$O_categorieMapper = FabriqueDeMappers::fabriquer('categorie', Connexion::recupererInstance());
			$O_categorie = $O_categorieMapper->trouverParIdentifiant($I_idCategorie);
			$A_categories = $O_categorieMapper->trouverParIntervalle();
			
			$O_articleMapper = FabriqueDeMappers::fabriquer('article', Connexion::recupererInstance());
			$A_articles = $O_articleMapper->trouverParCategorie($O_categorie);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		Vue::montrer('articles/categories/suppr',array('categorie'=>$O_categorie,'categories'=>$A_categories,'articles'=>$A_articles));
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
			$O_categorieRemplacement = isset($_POST['categorie_remplacement'])? $O_categorieMapper->trouverParIdentifiant($_POST['categorie_remplacement']) : null;
			$O_categorieMapper->supprimer($O_categorie,$O_categorieRemplacement);	
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