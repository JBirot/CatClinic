<?php
final class ControleurChat
{
	private $_S_urldefault;
	private $_S_urlCreation;
	private $_S_urlEdition;
	
	public function __construct()
	{
		Authentification::accesAdministrateur();
		$this->_S_urldefault = 'chat';
		$this->_S_urlCreation = 'chat/creation';
		$this->_S_urlEdition = 'chat/edit/';
	}
	
	public function defautAction()
	{
		BoiteAOutils::redirigerVers('chat/liste/'.BoiteAOutils::recupererDepuisSession('page_chat'));
	}
	
	public function changerLimiteAction()
	{
		$O_formulaire = new Formulaire(array(
			'limite_chats_new' => 'id'	
		));
		
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urldefault);
			return false;
		}
		
		BoiteAOutils::rangerDansSession('limite_chats', $O_formulaire->donneContenu('limite_chats_new'));
		BoiteAOutils::redirigerVers($this->_S_urldefault);
		return true;
	}
	
	public function changerOrdreAction(array $A_parametres)
	{
		if(count($A_parametres)<2)
		{
			BoiteAOutils::stockerErreur("Il manque des paramètres pour changer le tri de la liste des chats.");
			BoiteAOutils::redirigerVers($this->_S_urldefault);
			return false;
		}
		$I_champ = $A_parametres[0];
		$I_sens = $A_parametres[1];
		if($I_champ<0 || $I_champ>2 || ($I_sens!=='0' && $I_sens !== '1'))
		{
			BoiteAOutils::stockerErreur("L'un des paramètres pour changer le tri de la liste de chats est incorrect.");
			BoiteAOutils::redirigerVers($this->_S_urldefault);
			return false;
		}
		BoiteAOutils::rangerDansSession('ordre_chats', array($I_champ,$I_sens));
		BoiteAOutils::redirigerVers($this->_S_urldefault);
		return true;
	}
	
	public function listeAction(Array $A_parametres = NULL)
	{
		$I_page = isset($_SESSION['page_chat']) ? BoiteAOutils::recupererDepuisSession('page_chat') : 1;
		$I_page = isset($A_parametres[0]) ? $A_parametres[0] : $I_page;
		$O_chatMapper = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
		$O_listeur = new Listeur($O_chatMapper);
		$O_paginateur = new Paginateur($O_listeur);
		$I_limite = BoiteAOutils::recupererDepuisSession('limite_chats') ? BoiteAOutils::recupererDepuisSession('limite_chats') : Constantes::NB_MAX_CHATS_PAR_PAGE;
		$O_paginateur->changeLimite($I_limite);
		$A_ordre = BoiteAOutils::recupererDepuisSession('ordre_chats');
		$O_paginateur->changeOrdre($A_ordre);
		
		try {
			$A_chats = $O_paginateur->recupererPage($I_page);
		}catch(Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers('');
		}
		
		$A_pagination = $O_paginateur->paginer();
		
		BoiteAOutils::rangerDansSession('page_chat', $I_page);
		
		Vue::montrer('chats/liste',array('chats'=>$A_chats,'pagination'=>$A_pagination,'ordre'=>$A_ordre));
	}
	
	public function validationAction()
	{
		if (isset($_POST['chat_modif_all']))
		{
			if(isset($_POST['chat_toMod']))
			{
				foreach ($_POST['chat_toMod'] as $I_identifiant)
				{
					$A_ids[] = $I_identifiant;
					$A_champs['chat_Nom_'.$I_identifiant] = 'Nom';
					$A_champs['chat_Naissance_'.$I_identifiant] = 'date';
					$A_champs['chat_Tatouage_'.$I_identifiant] = 'texte';
				}
			}
		}
		else
		{
			foreach($_POST as $S_inputName => $value)
			{
				if(preg_match("/^chat_modif_[0-9]*/",$S_inputName ))
				{
					$I_identifiant = str_replace('chat_modif_', '', $S_inputName);
					$A_ids[] = $I_identifiant;
					$A_champs['chat_Nom_'.$I_identifiant] = 'Nom';
					$A_champs['chat_Naissance_'.$I_identifiant] = 'date';
					$A_champs['chat_Tatouage_'.$I_identifiant] = 'texte';
				}
			}
		}
		if(!empty($A_champs))
		{	//Récuperation des données
			$O_formulaire = new Formulaire($A_champs);
			//Validation des données
			if(!$O_formulaire->estValide())
			{
				BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
				BoiteAOutils::redirigerVers($this->_S_urldefault);
				return false;
			}
			//Verification et modification des objets
			$O_chatMapper = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
			foreach ($A_ids as $I_identifiant)
			{
				try {
					$O_chat = $O_chatMapper->trouverParIdentifiant($I_identifiant);
					$O_chat->hydrater(array($O_formulaire->donneContenu('chat_Nom_'.$I_identifiant),
											$O_formulaire->donneContenu('chat_Naissance_'.$I_identifiant),
											$O_formulaire->donneContenu('chat_Tatouage_'.$I_identifiant)));
					$O_chatMapper->actualiser($O_chat);
				}catch (Exception $O_exception){
					BoiteAOutils::stockerErreur($O_exception->getMessage());
					BoiteAOutils::redirigerVers($this->_S_urldefault);
					return false;
				}
			}
			//Message de confirmation et redirection
			BoiteAOutils::stockerMessage("Modification des chats n°".implode(', ',$A_ids));
			BoiteAOutils::redirigerVers($this->_S_urldefault);
			return true;			
		}else{
			//Aucune modification
			BoiteAOutils::stockerErreur("Aucune modification à enregistrer.");
			BoiteAOutils::redirigerVers($this->_S_urldefault);
			return false;
		}
	}
	
	public function creationAction()
	{
		Vue::montrer('chats/form');
	}
	
	public function creerAction()
	{	//On récupère les données de $_POST si elles existent
		$O_formulaire = new Formulaire(array(
			'chat_nouveau_nom' => 'Nom',
			'chat_nouveau_age' => 'date',
			'chat_nouveau_tatouage' => 'Texte'	
		));
		//On vérifie l'existence et la validité de ces données
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}

		//Création du nouveau chat
		$O_chat = new Chat();
		$O_chat->hydrater($O_formulaire->donneContenus());
		//Enregistrement dans la base
		try {
			$O_chatMapper = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
			$O_chat->changeIdentifiant($O_chatMapper->creer($O_chat));
		} catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urlCreation);
			return false;
		}
		
		//préparation de la confirmation et redirection
		BoiteAOutils::stockerMessage("Le chat ".$O_chat->donneNom()." a bien été créé.");
		BoiteAOutils::redirigerVers($this->_S_urldefault);
		return true;
	}
	
	public function editAction(array $A_parametres)
	{
		if(!$I_idChat = $A_parametres[0])
		{	//Pas d'identifiant: message d'erreur
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du chat à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urldefault);
			return false;			
		}
		try{ //Recuperation du chat
			$O_chatMapper = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
			$O_chat = $O_chatMapper->trouverParIdentifiant($I_idChat);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urldefault);
			return false;
		}
		//Affichagerdu formulaire de modification
		Vue::montrer('chats/edit',array('chat'=>$O_chat));
	}
	
	public function miseajourAction(array $A_parametres)
	{
		if(!$I_idChat = $A_parametres[0])
		{
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du chat à modifier.");
			BoiteAOutils::redirigerVers($this->_S_urldefault);
			Return false;
		}
		
		//recuperation des données du formulaire
		$O_formulaire = new Formulaire(array(
				'chat_nom_'.$I_idChat => "Nom",
				'chat_naissance_'.$I_idChat => "date",
				'chat_tatouage_'.$I_idChat => "texte"
		));
		//vérification des données
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idChat);
			return false;
		}
		//On vérifie que le chat existe
		try{
			$O_chatMapper = FabriqueDeMappers::fabriquer('Chat', Connexion::recupererInstance());
			$O_chat = $O_chatMapper->trouverParIdentifiant($I_idChat);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urldefault);
		}
		$S_nom = $O_formulaire->donneContenu('chat_nom_'.$I_idChat);
		$S_date= $O_formulaire->donneContenu('chat_naissance_'.$I_idChat); 
		$S_tatouage = $O_formulaire->donneContenu('chat_tatouage_'.$I_idChat);
		//Modification de l'objet si nécessaire
		if($S_nom != $O_chat->donneNom() || $S_date != $O_chat->donneDate() || $S_tatouage != $O_chat->donneTatouage)
		{
			$O_chat->changeNom($S_nom);
			$O_chat->changeAge($S_date);
			$O_chat->changeTatouage($S_tatouage);
			try {
				$O_chatMapper->actualiser($O_chat);
			}catch (Exception $O_exception){
				BoiteAOutils::stockerErreur($O_exception->getMessage());
				BoiteAOutils::redirigerVers($this->_S_urlEdition.$I_idChat);
			}
		}
		
		BoiteAOutils::stockerMessage("Le chat d'identifiant ".$I_idChat." est bien modifié.");
		BoiteAOutils::redirigerVers($this->_S_urldefault);
		return true;
	}
	
	public function suppressionAction(array $A_parametres)
	{
		if(!$I_idChat = $A_parametres[0])
		{	//L'identifiant est absent: message d'erreur
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du chat à supprimer.");
			BoiteAOutils::redirigerVers($this->_S_urldefault);
			return false;			
		}
		try {
			$O_chatMapper = FabriqueDeMappers::fabriquer("chat", Connexion::recupererInstance());
			$O_chat = $O_chatMapper->trouverParIdentifiant($I_idChat);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urldefault);
			return false;
		}
		Vue::montrer('chats/suppr',array('chat'=>$O_chat));
	}
	
	public function supprAction(array $A_parametres)
	{
		if(!$I_idChat = $A_parametres[0])
		{
			//Pas d'identifiant, message d'erreur
			BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant du chat à supprimer.");
			BoiteAOutils::recupererDepuisSession($this->_S_urldefault);
			return false;
		}
		
		//On vérifie que le chat existe:
		try{
			$O_chatMapper = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
			$O_chat = $O_chatMapper->trouverParIdentifiant($I_idChat);
			$O_chatMapper->supprimer($O_chat);
		}catch (Exception $O_exception){
			BoiteAOutils::stockerErreur($O_exception->getMessage());
			BoiteAOutils::redirigerVers($this->_S_urldefault);
			return false;
		}
		BoiteAOutils::stockerMessage("Le chat d'identifiant ".$O_chat->donneIdentifiant()." a bien été supprimé.");
		BoiteAOutils::redirigerVers($this->_S_urldefault);
		return true;
	}
}