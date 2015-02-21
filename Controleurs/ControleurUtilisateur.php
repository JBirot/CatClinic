<?php

// Ce controleur sert à manipuler des utilisateurs, chose que seul un admin peut faire !

final class ControleurUtilisateur
{
	private $_S_urlDefaut;
	private $_S_urlCreation;
	private $_S_urlEdition;
		
	public function __construct($S_method = null)
	{
		$this->_S_urlDefaut = Authentification::estAdministrateur() ? 'utilisateur':'inscription';
		$this->_S_urlCreation = Authentification::estAdministrateur() ? 'utilisateur/creation' : 'inscription';
		$this->_S_urlEdition = 'utilisateur/edit';
		if($S_method != 'creerAction')
		{
			Authentification::accesAdministrateur();
		}	
	}
	
	public function defautAction()
	{
		BoiteAOutils::redirigerVers('utilisateur/liste/'.BoiteAOutils::recupererDepuisSession('page_utilisateur'));
	}
	
	public function changerLimiteAction()
	{
		$O_formulaire = new Formulaire(array(
			'limite_utilisateurs_new' => 'id'	
		));
		
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		
		BoiteAOutils::rangerDansSession('limite_utilisateurs', $O_formulaire->donneContenu('limite_utilisateurs_new'));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
	public function changerOrdreAction(Array $A_parametres)
	{
		if(count($A_parametres)<2)
		{
			BoiteAOutils::stockerErreur("Il manque des parametres pour changer le tri de la liste des utilisateurs.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		$I_champ = $A_parametres[0];
		$I_sens = $A_parametres[1];
		if($I_champ <0 || $I_champ > 2 ||
				($I_sens !== '0' && $I_sens !== '1'))
		{
			BoiteAOutils::stockerErreur("L'un des parametres pour changer le tri de la liste des utilisateurs est incorrect.");
			BoiteAOutils::redirigerVers($this->_S_urlDefaut);
			return false;
		}
		BoiteAOutils::rangerDansSession('ordre_utilisateurs', array($I_champ,$I_sens));
		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
		return true;
	}
	
    public function listeAction(Array $A_parametres = null)
    {
    	$I_page = isset($_SESSION['page_utilisateur']) ? BoiteAOutils::recupererDepuisSession('page_utilisateur') : 1;
        $I_page = isset($A_parametres[0]) ? $A_parametres[0] : $I_page;      
        
        $O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());

        $I_limite = isset($_SESSION['limite_utilisateurs']) ? BoiteAOutils::recupererDepuisSession('limite_utilisateurs') : Constantes::NB_MAX_UTILISATEURS_PAR_PAGE;
        $A_ordre = BoiteAOutils::recupererDepuisSession('ordre_utilisateurs'); 
        $O_listeur = new Listeur($O_utilisateurMapper);
        $O_paginateur = new Paginateur($O_listeur);
        $O_paginateur->changeLimite($I_limite);
        $O_paginateur->changeOrdre($A_ordre);

        // on doit afficher puis installer la pagination
        try
        {
        	$A_utilisateurs = $O_paginateur->recupererPage($I_page);
        }
        catch (Exception $O_exception)
        {
        	BoiteAOutils::stockerErreur($O_exception->getMessage());
        	BoiteAOutils::redirigerVers('');	
        }
        
        $A_pagination = $O_paginateur->paginer();
        
        BoiteAOutils::rangerDansSession('page_utilisateur', $I_page);

        // voir ce qu'on met dans utilisateurs !
        Vue::montrer ('utilisateur/liste', array('utilisateurs' => $A_utilisateurs, 'pagination' => $A_pagination, 'ordre'=>$A_ordre));
    }
    
    public function validationAction()
    {  	//Recherche des champs à valider
    	if (isset($_POST['utilisateur_modif_all']))
    	{
    		if(isset($_POST['utilisateur_toMod']))
    		{
	    		foreach ($_POST['utilisateur_toMod'] as $I_identifiant)
	    		{
	    			$A_ids[] = $I_identifiant;
	    			$A_champs['utilisateur_Login_'.$I_identifiant] = 'login'; 
	    		}
    		}
    	}
    	else
    	{
    		foreach($_POST as $S_inputName => $value)
    		{
    			if(preg_match("/^utilisateur_modif_[0-9]*/",$S_inputName ))
    			{
    				$I_identifiant = str_replace('utilisateur_modif_', '', $S_inputName);
    				$A_ids[] = $I_identifiant;
    				$A_champs['utilisateur_Login_'.$I_identifiant] = 'login';
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
    		$O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());
    		foreach ($A_ids as $I_identifiant)
    		{
    			try {
	    			$O_utilisateur = $O_utilisateurMapper->trouverParIdentifiant($I_identifiant);
	    			$O_utilisateur->changeLogin($O_formulaire->donneContenu('utilisateur_Login_'.$I_identifiant));
	    			$O_utilisateurMapper->actualiser($O_utilisateur);
    			}catch (Exception $O_exception){
    				BoiteAOutils::stockerErreur($O_exception->getMessage());
    				BoiteAOutils::redirigerVers($this->_S_urlDefaut);
    				return false;
    			}
    		}
    		//Message de confirmation et redirection
    		BoiteAOutils::stockerMessage('Modification des utilisateurs n°'.implode(', ',$A_ids));
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
    	Vue::montrer('utilisateur/form');
    }
    
    public function creerAction()
    {    	
    	$O_formulaire = new Formulaire(array(
    		'identifiant' => 'login',
    		'mot_de_passe'  => 'pwd'		
    	));
    	
    	if(!$O_formulaire->estValide())
    	{
    		BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
    		BoiteAOutils::redirigerVers($this->_S_urlCreation);
    	}
    	
    	$O_utilisateur = new Utilisateur();
    	$O_utilisateur->changeLogin($O_formulaire->donneContenu('identifiant'));
    	$O_utilisateur->changeMotDePasse(BoiteAOutils::crypterMotDePasse($O_utilisateur, $O_formulaire->donneContenu('mot_de_passe')));
    	
    	try
    	{
    		$O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());    		
    		$O_utilisateur->changeIdentifiant($O_utilisateurMapper->creer($O_utilisateur));
    	}
    	catch(Exception $O_exception)
    	{
    		BoiteAOutils::stockerErreur($O_exception->getMessage());
    		BoiteAOutils::redirigerVers($this->_S_urlCreation);
    	}
    	
    	$S_message = Authentification::estAdministrateur() ? "L'utilisateur " . $O_utilisateur->donneLogin() . " est bien enregistré." : "Votre inscription s'est bien déroulée.";
    	$S_url = Authentification::estAdministrateur() ? 'utilisateur' : '';
    	
    	BoiteAOutils::stockerMessage($S_message);
    	BoiteAOutils::redirigerVers($S_url);
    	return true;
    }

    public function editAction(Array $A_parametres)
    {
        $I_identifiantUtilisateur = $A_parametres[0];

        if (!$I_identifiantUtilisateur)
        {
            // l'identifiant est absent, inutile de continuer !
            // on renvoit vers l'action par défaut, en l'occurrence, la liste des utilisateurs
            BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'utilisateur à modifier.");
            BoiteAOutils::redirigerVers('');
        } else
        {
            // l'identifiant donné correspond t-il à une entrée en base ?

            try {
                $O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());
                $O_utilisateur = $O_utilisateurMapper->trouverParIdentifiant($I_identifiantUtilisateur);
            } catch (Exception $O_exception)
            {
                // L'identifiant passé ne correspond à rien...
                BoiteAOutils::stockerErreur($O_exception->getMessage());
                BoiteAOutils::redirigerVers($this->_S_urlDefaut);
            }

            // Si l'on est ici c'est qu'on a tout ce qu'il nous faut (un utilisateur !)
            // Nous le passons à la vue correspondante
            Vue::montrer('utilisateur/edit/form', array('utilisateur' => $O_utilisateur));
        }
    }

    public function miseajourAction(Array $A_parametres)
    {
        if(!$I_identifiantUtilisateur = $A_parametres[0])
        {
        	BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'utilisateur à modifier.");
        	BoiteAOutils::redirigerVers($this->_S_urlDefaut);
        }
        
        $O_formulaire = new Formulaire(array(
        		'login' => 'login'
        ));
         
        if(!$O_formulaire->estValide())
        {
        	BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
        	BoiteAOutils::redirigerVers($this->_S_urlCreation);
        }
        
        $S_login = $O_formulaire->donneContenu('login');

        $O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());
        $O_utilisateur = $O_utilisateurMapper->trouverParIdentifiant($I_identifiantUtilisateur);

        if ($S_login != $O_utilisateur->donneLogin()) {
            $O_utilisateur->changeLogin($S_login);
            $O_utilisateurMapper->actualiser($O_utilisateur);
        }
        //message de confirmation
        BoiteAOutils::stockerMessage("L'utilisateur d'identifiant " . $I_identifiantUtilisateur . " a bien été modifié.");
        // on redirige vers la liste !
        BoiteAOutils::redirigerVers($this->_S_urlDefaut);
    }
    
    public function suppressionAction(array $A_parametres)
    {
    	if(!$I_idAuteur = $A_parametres[0])
    	{	//L'identifiant est absent
    		//On prépare l'affichage de l'erreur et redirige l'utilisateur
    		BoiteAOutils::stockerErreur("Impossible de trouver l'identifiant de l'utilisateur à supprimer.");
    		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
    		return false;
    	}
    	try
    	{
    		$O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());
    		$O_utilisateur =  $O_utilisateurMapper->trouverParIdentifiant($I_idAuteur);
    	}
    	catch(Exception $O_exception)
    	{
    		//L'identifiant ne correspond pas
    		BoiteAOutils::stockerErreur($O_exception->getMessage());
    		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
    		return false;
    	}
    
    	Vue::montrer('utilisateur/suppr', array('utilisateur'=>$O_utilisateur));
    }

    public function supprAction(Array $A_parametres)
    {
        $I_identifiantUtilisateur = $A_parametres[0];

        $O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());
        $O_utilisateur = $O_utilisateurMapper->trouverParIdentifiant($I_identifiantUtilisateur);
        try {
        	$O_utilisateurMapper->supprimer($O_utilisateur);
        }catch (Exception $O_exception){
        	BoiteAOutils::stockerErreur($O_exception->getMessage());
        	BoiteAOutils::redirigerVers($this->_S_urlDefaut);
        }
        //L'identifiant ne correspondant pas forcèment au rang d'enregistrement j'ai désactivé cette partie
        /*$O_listeur = new ListeurUtilisateur($O_utilisateurMapper);
        $O_paginateur = new Paginateur($O_listeur);
        $O_paginateur->changeLimite(Constantes::NB_MAX_UTILISATEURS_PAR_PAGE);
		
        
        // Je veux, à partir de l'identifiant de mon utilisateur, déterminer quelle était
        // sa page, afin de revenir dessus après suppression
        // Attention, ceci ne marche que si en base l'id = le rang de l'enregistrement
        /*$I_pageCible = 1;

        if ($I_identifiantUtilisateur > Constantes::NB_MAX_UTILISATEURS_PAR_PAGE)
        {
            $I_pageCible = ceil($I_identifiantUtilisateur / Constantes::NB_MAX_UTILISATEURS_PAR_PAGE);
        }

        $A_utilisateurs = array();

        while (!count($A_utilisateurs) && $I_pageCible > 0)
        {
            // Si j'efface le dernier de la page, je ne veux pas revenir sur sa page,
            // qui sera vide, mais sur la précédente !
            // Je dois éviter la boucle infinie si jamais j'éfface le dernier enregistrement !
            $A_utilisateurs = $O_paginateur->recupererPage($I_pageCible);
            $I_pageCible--;
        }

        $A_pagination = $O_paginateur->paginer();

        // voir ce qu'on met dans utilisateurs !
        Vue::montrer ('utilisateur/liste', array('utilisateurs' => $A_utilisateurs, 'pagination' => $A_pagination));*/
        
        BoiteAOutils::stockerMessage("L'utilisateur " . $O_utilisateur->donneLogin() . " a bien été supprimé.");
        BoiteAOutils::redirigerVers($this->_S_urlDefaut);
    }
}