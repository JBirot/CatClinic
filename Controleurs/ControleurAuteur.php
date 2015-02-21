<?php

final class ControleurAuteur
{
	private $_S_urlDefaut;
	private $_S_urlCreation;
	private $_S_urlEdition;
	
	public function __construct()
	{
		Authentification::accesAdministrateur();	
		$this->_S_urlDefaut = 'auteur';
		$this->_S_urlCreation = 'auteur/creation';
		$this->_S_urlEdition = 'auteur/edit/';
	}
	
	public function defautAction()
	{
		BoiteAOutils::redirigerVers('auteur/liste/'.BoiteAOutils::recupererDepuisSession('page_auteur'));
	}
	
	public function changerLimiteAction()
	{
		$O_formulaire = new Formulaire(array(
				'limite_auteurs_new' => 'id'
		));
	
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
	
		BoiteAOutils::rangerDansSession('limite_auteurs', $O_formulaire->donneContenu('limite_auteurs_new'));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function changerOrdreAction(array $A_parametres)
	{
		if(count($A_parametres)<2)
		{
			BoiteAOutils::stockerErreur("Il manque des parametres pour changer le tri de la liste des auteurs.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		$I_champ = $A_parametres[0];
		$I_sens = $A_parametres[1];
		if($I_champ<0 || $I_champ >2 ||	($I_sens !== '0' && $I_sens !== '1'))
		{
			BoiteAOutils::stockerErreur("L'un des parametres pour changer le tri de la liste des auteurs est incorrect.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		BoiteAOutils::rangerDansSession('ordre_auteurs', array($I_champ,$I_sens));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function listeAction(Array $A_parametres = null)
	{
		$I_page = isset($_SESSION['page_auteur']) ? BoiteAOutils::recupererDepuisSession('page_auteur') : 1;
		$I_page = isset($A_parametres[0]) ? $A_parametres[0] : $I_page;
		$O_auteurMapper = FabriqueDeMappers::fabriquer('auteur', Connexion::recupererInstance());
	
		$O_listeur = new Listeur($O_auteurMapper);
		$O_paginateur = new Paginateur($O_listeur);
		$I_limite = BoiteAOutils::recupererDepuisSession('limite_auteurs') ? BoiteAOutils::recupererDepuisSession('limite_auteurs') : Constantes::NB_MAX_ARTICLES_PAR_PAGE;
		$O_paginateur->changeLimite($I_limite);
		$A_ordre = BoiteAOutils::recupererDepuisSession('ordre_auteurs');
		$O_paginateur->changeOrdre($A_ordre);
		
		// on doit afficher puis installer la pagination
		try
		{
			$A_auteurs = $O_paginateur->recupererPage($I_page);
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers('');
		}
		
		$A_pagination = $O_paginateur->paginer();
		
		BoiteAOutils::rangerDansSession('page_auteur', $I_page);

		Vue::montrer ('articles/auteurs/liste', array('auteurs' => $A_auteurs, 'pagination' => $A_pagination, 'ordre'=>$A_ordre));
	}
	
	public function validationAction()
	{	//Recherche des champs à valider
		if (isset($_POST['auteur_modif_all']))
		{
			if(isset($_POST['auteur_toMod']))
			{
				foreach ($_POST['auteur_toMod'] as $I_identifiant)
				{
					$A_ids[] = $I_identifiant;
					$A_champs['auteur_Nom_'.$I_identifiant] = 'Nom';
					$A_champs['auteur_Prenom_'.$I_identifiant] = 'Nom';
				}
			}
		}
		else
		{
			foreach($_POST as $S_inputName => $value)
			{
				if(preg_match("/^auteur_modif_[0-9]*/",$S_inputName ))
				{
					$I_identifiant = str_replace('auteur_modif_', '', $S_inputName);
					$A_ids[] = $I_identifiant;
					$A_champs['auteur_Nom_'.$I_identifiant] = 'Nom';
					$A_champs['auteur_Prenom_'.$I_identifiant] = 'Nom';
				}
			}
		}
		if(!empty($A_champs))
		{	//Recuperation des données
			$O_formulaire = new Formulaire($A_champs);
			//Validation des données
			if(!$O_formulaire->estValide())
			{
				BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
				BoiteAOutils::redirigerVers($this->_S_urlDefaut);
				return false;
			}
			//Enregistrement en base
			$O_auteurMapper = FabriqueDeMappers::fabriquer('auteur', Connexion::recupererInstance());
			foreach ($A_ids as $I_identifiant)
			{
				try{
					$O_auteur = $O_auteurMapper->trouverParIdentifiant($I_identifiant);
					$O_auteur->changeNom($O_formulaire->donneContenu('auteur_Nom_'.$I_identifiant));
					$O_auteur->changePrenom($O_formulaire->donneContenu('auteur_Prenom_'.$I_identifiant));
					$O_auteurMapper->actualiser($O_auteur);
				}catch (Exception $O_exception){
					BoiteAOutils::stockerErreur($O_exception->getMessage());
					BoiteAOutils::redirigerVers($this->_S_urlDefaut);
					return false;
				}
			}
			//Message de confirmation et redirection
			BoiteAOutils::stockerMessage('Modification des auteurs n°'.implode(', ',$A_ids));
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
		Vue::montrer('articles/auteurs/form');	
	}
	
	public function creerAction()
	{
		//On récupère les données de $_POST si elles existent
		$O_formulaire = new Formulaire(array(
			'auteur_nouveau_nom' 	=> 	'Nom',
			'auteur_nouveau_prenom'	=>	'Nom'			
		));
		
		//On vérifie l'existence et la validité de ces données
		if(!$O_formulaire->estValide())
		{
			//Redirection et affichage des erreurs
			BoiteAOutils::stockerErreur($O_formulaire->donnerErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		
		//Création du nouvel auteur
		$O_auteur = new Auteur();
		
		$O_auteur->changeNom($O_formulaire->donneContenu('auteur_nouveau_nom'));
		$O_auteur->changePrenom($O_formulaire->donneContenu('auteur_nouveau_prenom'));
		
		//Enregistrement dans la base
		$O_auteurMapper = FabriqueDeMappers::fabriquer('auteur', Connexion::recupererInstance());
		try
		{
			$O_auteur->changeIdentifiant($O_auteurMapper->creer($O_auteur));
		}
		catch (Exception $O_Exception)
		{
			BoiteAOutils::stockerErreur($O_Exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		
		//Préparation de l'affichage du message de confirmation et redirection
		BoiteAOutils::stockerMessage("L'auteur " . $O_auteur->donnePrenom() . " " . $O_auteur->donneNom() . " a bien été enregistré.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function editAction(Array $A_parametres)
	{
		If(!$I_idAuteur = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'auteur à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		try
		{
			$O_auteurMapper = FabriqueDeMappers::fabriquer('auteur', Connexion::recupererInstance());
			$O_auteur = $O_auteurMapper->trouverParIdentifiant($I_idAuteur);
		}
		catch(Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		Vue::montrer('articles/auteurs/edit', array('auteur' => $O_auteur));
	}
	
	public function miseajourAction(array $A_parametres)
	{
		//Recherche de l'identifiant de l'auteur à modifier
		if(!$I_idAuteur = $A_parametres[0])
		{
			//L'identifiant est absent
			//On prépare l'affichage de l'erreur et redirige l'utilisateur
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'auteur à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//On récupère les informations du formulaire
		$O_formulaire = new Formulaire(array(
				'auteur_nom_'.$I_idAuteur => "Nom",
				'auteur_prenom_'.$I_idAuteur => "Nom"
		));
		
		//On vérifie l'existence et la validité des données
		if (!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idAuteur);
			return false;
		}
		//On vérifie que l'auteur existe
		try
		{
			$O_auteurMapper = FabriqueDeMappers::fabriquer('Auteur', Connexion::recupererInstance());
			$O_auteur =  $O_auteurMapper->trouverParIdentifiant($I_idAuteur);
		}
		catch(Exception $O_exception)
		{
			//L'identifiant ne correspond pas
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		$S_nom = $O_formulaire->donneContenu('auteur_nom_'.$I_idAuteur);
		$S_prenom = $O_formulaire->donneContenu('auteur_prenom_'.$I_idAuteur);
		
		//Modification de l'objet si nécessaire
		if($S_nom != $O_auteur->donneNom() || $S_prenom != $O_auteur->donnePrenom())
		{
			$O_auteur->changeNom($S_nom);
			$O_auteur->changePrenom($S_prenom);
			try
			{
				$O_auteurMapper->actualiser($O_auteur);
			}
			catch (Exception $O_exception)
			{
				BoiteAOutils::stockerErreur($O_exception->getMessage());
				BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idAuteur);
			}
		}
		
		BoiteAOutils::stockerMessage("L'auteur d'identifiant " . $I_idAuteur . " est bien modifié.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function suppressionAction(array $A_parametres)
	{
		if(!$I_idAuteur = $A_parametres[0])
		{	//L'identifiant est absent
			//On prépare l'affichage de l'erreur et redirige l'utilisateur
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'auteur à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		try
		{
			$O_auteurMapper = FabriqueDeMappers::fabriquer('Auteur', Connexion::recupererInstance());
			$O_auteur =  $O_auteurMapper->trouverParIdentifiant($I_idAuteur);
			$A_auteurs = $O_auteurMapper->trouverParIntervalle();
			$O_articleMapper = FabriqueDeMappers::fabriquer('article', Connexion::recupererInstance());
			$A_articles = $O_articleMapper->trouverParAuteur($O_auteur);
		}
		catch(Exception $O_exception)
		{
			//L'identifiant ne correspond pas
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		Vue::montrer('articles/auteurs/suppr', array('auteur'=>$O_auteur,'auteurs'=>$A_auteurs,'articles'=>$A_articles));	
	}
	
	public function supprAction(Array $A_parametres)
	{
		if(!$I_idAuteur = $A_parametres[0])
		{
			//L'identifiant est absent
			//On prépare l'affichage de l'erreur et redirige l'utilisateur
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'auteur à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		//On vérifie que l'auteur existe
		try
		{
			$O_auteurMapper = FabriqueDeMappers::fabriquer('Auteur', Connexion::recupererInstance());
			$O_oldAuteur =  $O_auteurMapper->trouverParIdentifiant($I_idAuteur);
			$O_auteurRemplacement = isset($_POST['auteur_remplacement']) ? $O_auteurMapper->trouverParIdentifiant($_POST['auteur_remplacement']) : null;
			$O_auteurMapper->supprimer($O_oldAuteur,$O_auteurRemplacement);
		}
		catch(Exception $O_exception)
		{
			//L'identifiant ne correspond pas
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		BoiteAOutils::stockerMessage("L'auteur d'identifiant ".$I_idAuteur." est bien supprimé.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;		
	}
}