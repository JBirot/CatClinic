<?php
final class ControleurProprietaire
{
	private $_S_urlDefaut;
	private $_S_urlCreation;
	private $_S_urlEdition;
	
	public function __construct()
	{
		Authentification::accesAdministrateur();	
		$this->_S_urlDefaut = 'proprietaire';
		$this->_S_urlCreation = 'proprietaire/creation';
		$this->_S_urlEdition = 'proprietaire/edit/';
	}
	
	public function defautAction()
	{
		BoiteAOutils::redirigerVers('proprietaire/liste/'.BoiteAOutils::recupererDepuisSession('page_proprietaire'));
	}
	
	public function changerLimiteAction()
	{
		$O_formulaire = new Formulaire(array(
			'limite_proprietaires_new' => 'id'	
		));
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		BoiteAOutils::rangerDansSession('limite_proprietaires', $O_formulaire->donneContenu('limite_proprietaires_new'));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function changerOrdreAction(array $A_parametres)
	{
		if(count($A_parametres)<2)
		{
			BoiteAOutils::stockerErreur("Il manque des paramètres pour changer le tri de la liste des proprietaires.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		$I_champ = $A_parametres[0];
		$I_sens = $A_parametres[1];
		if ($I_champ<0 || $I_champ >2 || ($I_sens!=='0'&&$I_sens!=='1'))
		{
			BoiteAOutils::stockerErreur("L'un des paramètres pour changer le tri de la liste des proprietaires est invalide.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		BoiteAOutils::rangerDansSession('ordre_proprietaires', array($I_champ,$I_sens));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function listeAction(array $A_parametres = NULL)
	{
		$I_page= isset($_SESSION['page_proprietaire'])?BoiteAOutils::recupererDepuisSession('page_proprietaire'):1;
		$I_page= isset($A_parametres[0])?$A_parametres[0]:$I_page;
		$O_proprietaireMapper = FabriqueDeMappers::fabriquer('proprietaire', Connexion::recupererInstance());
		$O_listeur = new Listeur($O_proprietaireMapper);
		$O_paginateur = new Paginateur($O_listeur);
		$I_limite = BoiteAOutils::recupererDepuisSession('limite_proprietaires') ? BoiteAOutils::recupererDepuisSession('limite_proprietaires'):Constantes::NB_MAX_PRATICIENS_PAR_PAGE;
		$O_paginateur->changeLimite($I_limite);
		$A_ordre = BoiteAOutils::recupererDepuisSession('ordre_proprietaires');
		$O_paginateur->changeOrdre($A_ordre);
		
		try{
			$A_proprietaires = $O_paginateur->recupererPage($I_page);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers('');
			return false;
		}
		
		$O_pagination = $O_paginateur->paginer();
		
		BoiteAOutils::rangerDansSession('page_proprietaire', $I_page);
		
		Vue::montrer('proprietaires/liste',array('proprietaires'=>$A_proprietaires,'pagination'=>$O_pagination,'ordre'=>$A_ordre));
	}
	
	public function validationAction()
	{	//Recherche des champs à valider
		if (isset($_POST['proprietaire_modif_all']))
		{
			if(isset($_POST['proprietaire_toMod']))
			{
				foreach ($_POST['proprietaire_toMod'] as $I_identifiant)
				{
					$A_ids[] = $I_identifiant;
					$A_champs['proprietaire_Nom_'.$I_identifiant] = 'Nom';
					$A_champs['proprietaire_Prenom_'.$I_identifiant] = 'Nom';
				}
			}
		}
		else
		{
			foreach($_POST as $S_inputName => $value)
			{
				if(preg_match("/^proprietaire_modif_[0-9]*/",$S_inputName ))
				{
					$I_identifiant = str_replace('proprietaire_modif_', '', $S_inputName);
					$A_ids[] = $I_identifiant;
					$A_champs['proprietaire_Nom_'.$I_identifiant] = 'Nom';
					$A_champs['proprietaire_Prenom_'.$I_identifiant] = 'Nom';
				}
			}
		}
		if(!empty($A_champs))
		{	//Recuperation des données
			$O_formulaire = new Formulaire($A_champs);
			//Validation
			if(!$O_formulaire->estValide())
			{
				BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
				BoiteAOutils::redirigerVers($this->_S_urlDefaut);
				return false;
			}
			//Enregistrement en base
			$O_proprietaireMapper = FabriqueDeMappers::fabriquer('proprietaire', Connexion::recupererInstance());
			foreach ($A_ids as $I_identifiant)
			{
				try {
					$O_proprietaire = $O_proprietaireMapper->trouverParIdentifiant($I_identifiant);
					$O_proprietaire->changeNom($O_formulaire->donneContenu('proprietaire_Nom_'.$I_identifiant));
					$O_proprietaire->changePrenom($O_formulaire->donneContenu('proprietaire_Prenom_'.$I_identifiant));
					$O_proprietaireMapper->actualiser($O_proprietaire);
				}catch(Exception $O_exception){
					BoiteAOutils::stockerErreur($O_exception->getMessage());
					BoiteAOutils::redirigerVers($this->_S_urlDefaut);
					return false;
				}
				//Message de confirmation et redirection
				BoiteAOutils::stockerMessage('Modification des proprietaires n°'.implode(', ',$A_ids));
				BoiteAOutils::redirigerVers($this->_S_urlDefaut);
				return true;
			}			
		}else{
			//Aucune modification
			BoiteAOutils::stockerErreur("Aucune modification trouvée.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}	
	}
	
	public function creationAction()
	{
		Vue::montrer('proprietaires/form');
	}
	
	public function creerAction()
	{	//Recuperation des données
		$O_formulaire = new Formulaire(array(
			'proprietaire_nouveau_nom' => 'nom',
			'proprietaire_nouveau_prenom' => 'nom'	
		));
		//Verification des données
		if(!$O_formulaire->estValide())
		{	//Redirection et affichage des erreurs
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		//creation du nouveua proprietaire
		$O_proprietaire = new Proprietaire();

		$O_proprietaire->changeNom($O_formulaire->donneContenu('proprietaire_nouveau_nom'));
		$O_proprietaire->changePrenom($O_formulaire->donneContenu('proprietaire_nouveau_prenom'));
		
		//Enregistrement dans la base
		$O_proprietaireMapper = FabriqueDeMappers::fabriquer('proprietaire', Connexion::recupererInstance());
		try{
			$O_proprietaire->changeIdentifiant($O_proprietaireMapper->creer($O_proprietaire));
		}catch(Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		
		//Préparation de l'affichage du message de confirmation et redirection
		BoiteAOutils::stockerMessage('Le proprietaire '.$O_proprietaire->donnePrenom().' '.$O_proprietaire->donneNom().' a bien été enregistré.');
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function editAction(Array $A_parametres)
	{
		If(!$I_idProprietaire = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du proprietaire à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		try{
			$O_proprietaireMapper = FabriqueDeMappers::fabriquer('proprietaire', Connexion::recupererInstance());
			$O_proprietaire = $O_proprietaireMapper->trouverParIdentifiant($I_idProprietaire);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		Vue::montrer('proprietaires/edit',array('proprietaire'=>$O_proprietaire));
	}
	
	public function miseajourAction(array $A_parametres)
	{	//Recherche de l'identifiant du proprietaire à modifier
		If(!$I_idProprietaire = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du proprietaire à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Recuperation des donnees du formulaire
		$O_formulaire = new Formulaire(array(
			'proprietaire_nom_'.$I_idProprietaire => 'nom',
			'proprietaire_prenom_'.$I_idProprietaire => 'nom'	
		));
		//Validation des données
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idProprietaire);
		}
		//Verification du proprietaire en base
		try{
			$O_proprietaireMapper = FabriqueDeMappers::fabriquer('proprietaire', Connexion::recupererInstance());
			$O_proprietaire = $O_proprietaireMapper->trouverParIdentifiant($I_idProprietaire);
		}catch(Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		$S_nom = $O_formulaire->donneContenu('proprietaire_nom_'.$I_idProprietaire);
		$S_prenom = $O_formulaire->donneContenu('proprietaire_prenom_'.$I_idProprietaire);
		//Modification de l'objet si necessaire
		if($S_nom != $O_proprietaire->donneNom() || $S_prenom != $O_proprietaire->donnePrenom())
		{
			$O_proprietaire->changeNom($S_nom);
			$O_proprietaire->changePrenom($S_prenom);
			try {
				$O_proprietaireMapper->actualiser($O_proprietaire);
			}catch (Exception $O_exception){
				BoiteAOutils::stockerErreur($O_exception->getMessage());
				BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idProprietaire);
				return false;
			}
		}
		BoiteAOutils::stockerMessage("Le proprietaire d'identifiant n°".$I_idProprietaire." a bien été modifié.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function suppressionAction(array $A_parametres)
	{
		if(!$I_idProprietaire = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du proprietaire à supprimer.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;	
		}
		try{
			$O_proprietaireMapper = FabriqueDeMappers::fabriquer('proprietaire', Connexion::recupererInstance());
			$O_proprietaire = $O_proprietaireMapper->trouverParIdentifiant($I_idProprietaire);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		}
		
		Vue::montrer('proprietaires/suppr',array('proprietaire'=>$O_proprietaire));
	}
	
	public function supprAction(array $A_parametres)
	{	//Verification de l'identifiant
		if(!$I_idProprietaire = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du proprietaire à supprimer.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Verification du proprietaire en base et suppression
		try{
			$O_proprietaireMapper = FabriqueDeMappers::fabriquer('proprietaire', Connexion::recupererInstance());
			$O_proprietaire = $O_proprietaireMapper->trouverParIdentifiant($I_idProprietaire);
			$O_proprietaireMapper->supprimer($O_proprietaire);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Message de confirmation et redirection
		BoiteAOutils::stockerMessage("Le proprietaire d'identifiant ".$I_idProprietaire." est bien supprimé.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	
	}
}