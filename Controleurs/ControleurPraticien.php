<?php
final class ControleurPraticien
{
	private $_S_urlDefaut;
	private $_S_urlCreation;
	private $_S_urlEdition;
	
	public function __construct()
	{
		Authentification::accesAdministrateur();	
		$this->_S_urlDefaut = 'praticien';
		$this->_S_urlCreation = 'praticien/creation';
		$this->_S_urlEdition = 'praticien/edit/';
	}
	
	public function defautAction()
	{
		BoiteAOutils::redirigerVers('praticien/liste/'.BoiteAOutils::recupererDepuisSession('page_praticien'));
	}
	
	public function changerLimiteAction()
	{
		$O_formulaire = new Formulaire(array(
			'limite_praticiens_new' => 'id'	
		));
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		BoiteAOutils::rangerDansSession('limite_praticiens', $O_formulaire->donneContenu('limite_praticiens_new'));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function changerOrdreAction(array $A_parametres)
	{
		if(count($A_parametres)<2)
		{
			BoiteAOutils::stockerErreur("Il manque des paramètres pour changer le tri de la liste des praticiens.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		$I_champ = $A_parametres[0];
		$I_sens = $A_parametres[1];
		if ($I_champ<0 || $I_champ >2 || ($I_sens!=='0'&&$I_sens!=='1'))
		{
			BoiteAOutils::stockerErreur("L'un des paramètres pour changer le tri de la liste des praticiens est invalide.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		BoiteAOutils::rangerDansSession('ordre_praticiens', array($I_champ,$I_sens));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function listeAction(array $A_parametres = NULL)
	{
		$I_page= isset($_SESSION['page_praticien'])?BoiteAOutils::recupererDepuisSession('page_praticien'):1;
		$I_page= isset($A_parametres[0])?$A_parametres[0]:$I_page;
		$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
		$O_listeur = new Listeur($O_praticienMapper);
		$O_paginateur = new Paginateur($O_listeur);
		$I_limite = BoiteAOutils::recupererDepuisSession('limite_praticiens') ? BoiteAOutils::recupererDepuisSession('limite_praticiens'):Constantes::NB_MAX_PRATICIENS_PAR_PAGE;
		$O_paginateur->changeLimite($I_limite);
		$A_ordre = BoiteAOutils::recupererDepuisSession('ordre_praticiens');
		$O_paginateur->changeOrdre($A_ordre);
		
		try{
			$A_praticiens = $O_paginateur->recupererPage($I_page);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers('');
			return false;
		}
		
		$O_pagination = $O_paginateur->paginer();
		
		BoiteAOutils::rangerDansSession('page_praticien', $I_page);
		
		Vue::montrer('praticiens/liste',array('praticiens'=>$A_praticiens,'pagination'=>$O_pagination,'ordre'=>$A_ordre));
	}
	
	public function validationAction()
	{	//Recherche des champs à valider
		if (isset($_POST['praticien_modif_all']))
		{
			if(isset($_POST['praticien_toMod']))
			{
				foreach ($_POST['praticien_toMod'] as $I_identifiant)
				{
					$A_ids[] = $I_identifiant;
					$A_champs['praticien_Nom_'.$I_identifiant] = 'Nom';
					$A_champs['praticien_Prenom_'.$I_identifiant] = 'Nom';
				}
			}
		}
		else
		{
			foreach($_POST as $S_inputName => $value)
			{
				if(preg_match("/^praticien_modif_[0-9]*/",$S_inputName ))
				{
					$I_identifiant = str_replace('praticien_modif_', '', $S_inputName);
					$A_ids[] = $I_identifiant;
					$A_champs['praticien_Nom_'.$I_identifiant] = 'Nom';
					$A_champs['praticien_Prenom_'.$I_identifiant] = 'Nom';
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
			$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
			foreach ($A_ids as $I_identifiant)
			{
				try {
					$O_praticien = $O_praticienMapper->trouverParIdentifiant($I_identifiant);
					$O_praticien->changeNom($O_formulaire->donneContenu('praticien_Nom_'.$I_identifiant));
					$O_praticien->changePrenom($O_formulaire->donneContenu('praticien_Prenom_'.$I_identifiant));
					$O_praticienMapper->actualiser($O_praticien);
				}catch(Exception $O_exception){
					BoiteAOutils::stockerErreur($O_exception->getMessage());
					BoiteAOutils::redirigerVers($this->_S_urlDefaut);
					return false;
				}
				//Message de confirmation et redirection
				BoiteAOutils::stockerMessage('Modification des praticiens n°'.implode(', ',$A_ids));
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
		Vue::montrer('praticiens/form');
	}
	
	public function creerAction()
	{	//Recuperation des données
		$O_formulaire = new Formulaire(array(
			'praticien_nouveau_nom' => 'nom',
			'praticien_nouveau_prenom' => 'nom'	
		));
		//Verification des données
		if(!$O_formulaire->estValide())
		{	//Redirection et affichage des erreurs
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		//creation du nouveua praticien
		$O_praticien = new Praticien();

		$O_praticien->changeNom($O_formulaire->donneContenu('praticien_nouveau_nom'));
		$O_praticien->changePrenom($O_formulaire->donneContenu('praticien_nouveau_prenom'));
		
		//Enregistrement dans la base
		$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
		try{
			$O_praticien->changeIdentifiant($O_praticienMapper->creer($O_praticien));
		}catch(Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		
		//Préparation de l'affichage du message de confirmation et redirection
		BoiteAOutils::stockerMessage('Le praticien '.$O_praticien->donnePrenom().' '.$O_praticien->donneNom().' a bien été enregistré.');
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function editAction(Array $A_parametres)
	{
		If(!$I_idPraticien = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du praticien à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		try{
			$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
			$O_praticien = $O_praticienMapper->trouverParIdentifiant($I_idPraticien);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		Vue::montrer('praticiens/edit',array('praticien'=>$O_praticien));
	}
	
	public function miseajourAction(array $A_parametres)
	{	//Recherche de l'identifiant du praticien à modifier
		If(!$I_idPraticien = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du praticien à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Recuperation des donnees du formulaire
		$O_formulaire = new Formulaire(array(
			'praticien_nom_'.$I_idPraticien => 'nom',
			'praticien_prenom_'.$I_idPraticien => 'nom'	
		));
		//Validation des données
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idPraticien);
		}
		//Verification du praticien en base
		try{
			$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
			$O_praticien = $O_praticienMapper->trouverParIdentifiant($I_idPraticien);
		}catch(Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		$S_nom = $O_formulaire->donneContenu('praticien_nom_'.$I_idPraticien);
		$S_prenom = $O_formulaire->donneContenu('praticien_prenom_'.$I_idPraticien);
		//Modification de l'objet si necessaire
		if($S_nom != $O_praticien->donneNom() || $S_prenom != $O_praticien->donnePrenom())
		{
			$O_praticien->changeNom($S_nom);
			$O_praticien->changePrenom($S_prenom);
			try {
				$O_praticienMapper->actualiser($O_praticien);
			}catch (Exception $O_exception){
				BoiteAOutils::stockerErreur($O_exception->getMessage());
				BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idPraticien);
				return false;
			}
		}
		BoiteAOutils::stockerMessage("Le praticien d'identifiant n°".$I_idPraticien." a bien été modifié.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function suppressionAction(array $A_parametres)
	{
		if(!$I_idPraticien = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du praticien à supprimer.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;	
		}
		try{
			$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
			$O_praticien = $O_praticienMapper->trouverParIdentifiant($I_idPraticien);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		}
		
		Vue::montrer('praticiens/suppr',array('praticien'=>$O_praticien));
	}
	
	public function supprAction(array $A_parametres)
	{	//Verification de l'identifiant
		if(!$I_idPraticien = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du praticien à supprimer.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Verification du praticien en base et suppression
		try{
			$O_praticienMapper = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
			$O_praticien = $O_praticienMapper->trouverParIdentifiant($I_idPraticien);
			$O_praticienMapper->supprimer($O_praticien);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		//Message de confirmation et redirection
		BoiteAOutils::stockerMessage("Le praticien d'identifiant ".$I_idPraticien." est bien supprimé.");
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	
	}
}