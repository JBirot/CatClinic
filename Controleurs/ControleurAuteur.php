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
		$this->_S_urlCreation = 'auteur';
		$this->_S_urlEdition = 'auteur/edit/';
	}
	
	public function defautAction()
	{
		BoiteAOutils::redirigerVers('auteur/liste');
	}
	
	public static function listeAction(Array $A_parametres = null)
	{
		$I_page = isset($_SESSION['page_auteur']) ? BoiteAOutils::recupererDepuisSession('page_auteur') : 1;
		$I_page = isset($A_parametres[0]) ? $A_parametres[0] : $I_page;
		$O_auteurMapper = FabriqueDeMappers::fabriquer('auteur', Connexion::recupererInstance());
	
		$O_listeur = new Listeur($O_auteurMapper);
		$O_paginateur = new Paginateur($O_listeur);
		$O_paginateur->changeLimite(Constantes::NB_MAX_ARTICLES_PAR_PAGE);
		
		// on doit afficher puis installer la pagination
		try
		{
			$A_auteurs = $O_paginateur->recupererPage($I_page);
		}
		catch (Exception $O_exception)
		{
			//Si la récupération de la 1ère page de la liste échoue on repart à la base du site, autrement on revient sur la précèdente
			$S_url = $I_page == 1 ? '' : $this->_S_urlDefaut;
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($S_url);
		}
		
		$A_pagination = $O_paginateur->paginer();
		
		BoiteAOutils::rangerDansSession('page_auteur', $I_page);
	
		// voir ce qu'on met dans utilisateurs !
		Vue::montrer ('articles/auteurs/form');
		Vue::montrer ('articles/auteurs/liste', array('auteurs' => $A_auteurs, 'pagination' => $A_pagination));
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
		//Recherche de l'identifiant de la catégorie à modifier
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
		$S_nom = $O_formulaire->donneContenu('auteur_nom_'.$I_idAuteur);
		$S_prenom = $O_formulaire->donneContenu('auteur_prenom_'.$I_idAuteur);
		
		//Modification de l'objet si nécessaire
		if($S_nom != $O_auteur->donneNom() || $S_prenom != $O_auteur->donnePrenom())
		{
			$O_auteur->changeNom($S_nom);
			$O_auteur->cahngePrenom($S_prenom);
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
		
		BoiteAOutils::stockerErreur("L'auteur d'identifiant " . $I_idAuteur . " est bien modifié.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
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
			$O_auteurMapper->supprimer($O_oldAuteur);
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