<?php

final class ControleurVisite
{
	private $_S_urlDefaut;
	private $_S_urlCreation;
	private $_S_urlEdition;
	
	public function __construct($S_method = NULL)
	{
		if($S_method == "pageAction")
		{
			Authentification::accesProprietaire();			
		}
		elseif ($S_method != "defautAction" && !Authentification::estProprietaire())
		{
			Authentification::accesAdministrateur();
		}
		$this->_S_urlDefaut = 'visite/liste/'.BoiteAOutils::recupererDepuisSession('page_visite');
		$this->_S_urlCreation = 'visite/creation';
		$this->_S_urlEdition = 'visite/edit/';	
	}
	
	public function defautAction()
	{
		if (Authentification::estAdministrateur())
		{
			BoiteAOutils::redirigerVers('visite/liste/'.BoiteAOutils::recupererDepuisSession('page_visite'));
			return true;
		}
		else 
		{
			BoiteAOutils::redirigerVers('visite/page/1');
		}		
	}
	
	public function pageAction(array $A_parametres)
	{
		if(count($A_parametres) < 1){
			BoiteAOutils::stockerErreur("Il manque des parametres pour l'affichage de vos visites.");
			BoiteAOutils::redirigerVers('');
			return false;
		}
		$I_chat = $A_parametres[0];
		$A_chats = BoiteAOutils::recupererDepuisSession('utilisateur')->donneProprietaire()->donneChats();
		if (!isset($A_chats[$I_chat-1]))
		{
			BoiteAOutils::stockerErreur("Impossible de trouver le chat pour l'affichage des visites.");
			BoiteAOutils::redirigerVers('');
			return false;
		}
		$O_chat = $A_chats[$I_chat-1];
		$O_visiteMapper = FabriqueDeMappers::fabriquer("visite", Connexion::recupererInstance());
		try {
			$A_visites = $O_visiteMapper->trouverParIdentifiantChat($O_chat->donneIdentifiant());
		}catch (Exception $O_exception){
			$A_visites = null;
		}
		Vue::montrer('visites/standard',array('chat'=>$O_chat,'chats'=>$A_chats,'visites'=>$A_visites));
	}
	
	public function changerLimiteAction()
	{
		$O_formulaire = new Formulaire(array(
				'limite_visites_new' => 'id'
		));
	
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
	
		BoiteAOutils::rangerDansSession('limite_visites', $O_formulaire->donneContenu('limite_visites_new'));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function changerOrdreAction(Array $A_parametres)
	{
		if(count($A_parametres)<2)
		{
			BoiteAOutils::stockerErreur("Il manque des parametres pour changer le tri de la liste des visites.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		$I_champ = $A_parametres[0];
		$I_sens = $A_parametres[1];
		if($I_champ<0 || $I_champ >4 ||	($I_sens !== '0' && $I_sens !== '1'))
		{
			BoiteAOutils::stockerErreur("L'un des parametres pour changer le tri de la liste des visites est incorrect.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		BoiteAOutils::rangerDansSession('ordre_visites', array($I_champ,$I_sens));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function listeAction(array $A_parametres)
	{
		$I_page = isset($_SESSION['page_visite']) ? BoiteAOutils::recupererDepuisSession('page_visite') : 1 ;
		$I_page = isset($A_parametres[0]) ? $A_parametres[0] : $I_page;
		$O_visiteMapper = FabriqueDeMappers::fabriquer('visite', Connexion::recupererInstance());
	
		$O_listeur = new Listeur($O_visiteMapper);
		$O_paginateur = new Paginateur($O_listeur);
		$I_limite = BoiteAOutils::recupererDepuisSession('limite_visites') ? BoiteAOutils::recupererDepuisSession('limite_visites') : Constantes::NB_MAX_ARTICLES_PAR_PAGE;
		$O_paginateur->changeLimite($I_limite);
		$A_ordre = BoiteAOutils::recupererDepuisSession('ordre_visites');
		$O_paginateur->changeOrdre($A_ordre);
	
		// on doit afficher puis installer la pagination
		try
		{
			$A_visites = $O_paginateur->recupererPage($I_page);
		}
		catch(Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers('');
		}
		
		$A_pagination = $O_paginateur->paginer();
		
		BoiteAOutils::rangerDansSession('page_visite', $I_page);
	
		// voir ce qu'on met dans utilisateurs !
		Vue::montrer ('visites/liste', array('visites' => $A_visites, 'pagination' => $A_pagination,'ordre'=>$A_ordre));
	}
	
	public function validationAction()
	{	//Recherche des champs à valider
		if (isset($_POST['visite_modif_all']))
		{
			if(isset($_POST['visite_toMod']))
			{
				foreach ($_POST['visite_toMod'] as $I_identifiant)
				{
					$A_ids[] = $I_identifiant;
					// TODO: champs modifiables
					//$A_champs['visite_Prix_'.$I_identifiant] = 'texte';
				}
			}
		}
		else
		{
			foreach($_POST as $S_inputName => $value)
			{
				if(preg_match("/^visite_modif_[0-9]*/",$S_inputName ))
				{
					$I_identifiant = str_replace('visite_modif_', '', $S_inputName);
					$A_ids[] = $I_identifiant;
					// TODO: champs modifiables
					//$A_champs['visite_Prix_'.$I_identifiant] = 'texte';
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
    		$O_visiteMapper = FabriqueDeMappers::fabriquer('visite', Connexion::recupererInstance());
    		foreach ($A_ids as $I_identifiant)
    		{
    			try{
    				$O_visite = $O_visiteMapper->trouverParIdentifiant($I_identifiant);
    				// TODO : modfication de l'objet
    				$O_visiteMapper->actualiser($O_visite);
    			}catch (Exception $O_exception){
    				BoiteAOutils::stockerErreur($O_exception->getMessage());
    				BoiteAOutils::redirigerVers($this->_S_urlDefaut);
    				return false;
    			}
    		}
    		//Message de confirmation et redirection
    		BoiteAOutils::stockerMessage('Modification des visites n°'.implode(', ', $A_ids));
    		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
    		return true;			
		}
		else
		{
			BoiteAOutils::stockerErreur('Aucune modification trouvée.');
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
	}
	
	public function creationAction()
	{
		try 
		{
			$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
			$A_praticiens = $O_praticienMapper->trouverParIntervalle();
			
			$O_chatMapper = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
			$A_chats = $O_chatMapper->trouverParIntervalle();
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		Vue::montrer('visites/form', array('praticiens' => $A_praticiens, 'chats' => $A_chats));	
	}
	
	public function creerAction()
	{
		//Récupération des données du formulaire
		$O_formulaire = new Formulaire(array(
			'visite_nouveau_prix' => 'prix',
			'visite_nouveau_date'	=> 'dateTime',
			'visite_nouveau_praticien'	=> 'id',
			'visite_nouveau_chat'	=> 'id'			
		),array('visite_nouveau_observations' => 'texte'));
		//Vérification de ces données
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		//Vérification du praticien
		try 
		{
			$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
			$O_praticien = $O_praticienMapper->trouverParIdentifiant($O_formulaire->donneContenu('visite_nouveau_praticien'));
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		//Vérification du chat
		try 
		{
			$O_chatMapper = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
			$O_chat = $O_chatMapper->trouverParIdentifiant($O_formulaire->donneContenu('visite_nouveau_chat'));
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		//Création de la nouvelle visite
		$O_visite = new Visite();
		$O_visite->hydrater(array($O_praticien,
								$O_chat,
								new DateTime($O_formulaire->donneContenu('visite_nouveau_date')),
								$O_formulaire->donneContenu('visite_nouveau_prix'),
								$O_formulaire->donneContenu('visite_nouveau_observations')));
		//Enregistrement dans la base
		try 
		{
			$O_visiteMapper = FabriqueDeMappers::fabriquer('visite', Connexion::recupererInstance());
			$O_visite->changeIdentifiant($O_visiteMapper->creer($O_visite));
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		
		//Préparation de la confirmation et redirection
		BoiteAOutils::stockerMessage("La visite est bien créé.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function editAction(array $A_parametres)
	{
		if (!$I_idVisite = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'visite à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}

		try 
		{
			$O_visiteMapper = FabriqueDeMappers::fabriquer('visite', Connexion::recupererInstance());
			$O_visite = $O_visiteMapper->trouverParIdentifiant($I_idVisite);

			$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
			$A_praticiens = $O_praticienMapper->trouverParIntervalle();
			
			$O_chatMapper = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
			$A_chats = $O_chatMapper->trouverParIntervalle();
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		Vue::montrer('visites/edit', array('visite' => $O_visite, 'praticiens' => $A_praticiens, 'chats' => $A_chats));
	}
	
	public function miseajourAction(array $A_parametres)
	{	
		//Recherche de l'identifiant de l'visite à modifier
		if(!$I_idVisite = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'visite à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Vérification de l'ancienne visite
		try 
		{
			$O_visiteMapper = FabriqueDeMappers::fabriquer('visite', Connexion::recupererInstance());
			$O_visite = $O_visiteMapper->trouverParIdentifiant($I_idVisite);	
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Vérification ddes données du formulaire
		$O_formulaire = new Formulaire(array(
			'visite_prix_'.$I_idVisite => 'prix',
			'visite_date_'.$I_idVisite	=> 'dateTime',
			'visite_praticien_'.$I_idVisite	=> 'id',
			'visite_chat_'.$I_idVisite	=> 'id'			
		),array('visite_observations_'.$I_idVisite => 'texte'));
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idVisite);
			return false;
		}
		$S_observations = $O_formulaire->donneContenu('visite_observations_'.$I_idVisite);
		$S_date = $O_formulaire->donneContenu('visite_date_'.$I_idVisite);
		$F_prix = $O_formulaire->donneContenu('visite_prix_'.$I_idVisite);
		$I_idPraticien = $O_formulaire->donneContenu('visite_praticien_'.$I_idVisite);
		$I_idChat = $O_formulaire->donneContenu('visite_chat_'.$I_idVisite);
		
		//Modification de l'objet si nécessaire
		if($O_visite->donneObservations() != $S_observations || $O_visite->donneDate() != $S_date || $O_visite->donnePrix() != $F_prix ||
				$O_visite->donnePraticien()->donneIdentifiant() != $I_idPraticien ||
				$O_visite->donneChat()->donneIdentifiant() != $I_idChat)
		{	
			$O_visite->changeObservations($S_observations);
			$O_visite->changePrix($F_prix);
			$O_visite->changeDate(new DateTime($S_date));		
			//Vérification du praticien si nécessaire
			if($I_idPraticien != $O_visite->donnePraticien()->donneIdentifiant())
			{	
				try 
				{
					$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
					$O_visite->changePraticien($O_praticienMapper->trouverParIdentifiant($O_formulaire->donneContenu('visite_praticien_'.$I_idVisite)));
				}
				catch (Exception $O_exception)
				{
					BoiteAOutils::stockerErreur($O_exception->getMessage());
					BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idVisite);
					return false;
				}
			}
			//Vérification du chat si nécessaire
			if($I_idChat != $O_visite->donneChat()->donneIdentifiant())
			{
				try 
				{
					$O_chatMapper = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
					$O_visite->changeChat($O_chatMapper->trouverParIdentifiant($O_formulaire->donneContenu('visite_chat_'.$I_idVisite)));	
				}
				catch (Exception $O_exception)
				{
					BoiteAOutils::stockerErreur($O_exception->getMessage());
					BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idVisite);
					return false;
				}
			}
			//Enregistrement en base
			try 
			{
				$O_visiteMapper->actualiser($O_visite);
			}
			catch (Exception $O_exception)
			{
				BoiteAOutils::stockerErreur($O_exception->getMessage());
				BoiteAOutils::redirigerVers($this->_S_urlEdition);
				return false;
			}			
		}
		
		BoiteAOutils::stockerMessage("La visite d'identifiant ".$I_idVisite." a bien été modifié.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function suppressionAction(array $A_parametres)
	{
		if(!$I_idVisite = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de la visite à supprimer.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		try{
			$O_visiteMapper = FabriqueDeMappers::fabriquer('visite', Connexion::recupererInstance());
			$O_visite = $O_visiteMapper->trouverParIdentifiant($I_idVisite);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		}
	
		Vue::montrer('visites/suppr',array('visite'=>$O_visite));
	}
	
	public function supprAction(Array $A_parametres)
	{
		if(!$I_idVisite = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de la visite à supprimer.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		try 
		{
			$O_visiteMapper = FabriqueDeMappers::fabriquer('visite', Connexion::recupererInstance());
			$O_visite = $O_visiteMapper->trouverParIdentifiant($I_idVisite);
			$O_visiteMapper->supprimer($O_visite);
		}
		catch (Exception $O_exception)
		{
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		BoiteAOutils::stockerMessage("La visite d'identifiant ".$I_idVisite." a bien été supprimé.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
}
