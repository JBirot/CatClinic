<?php

// Ce controleur sert à manipuler des utilisateurs, chose que seul un admin peut faire !

final class ControleurUtilisateur
{
	private $_S_urlDefaut;
	
	public function __construct($S_method = null)
	{
		$this->_S_urlDefaut = Authentification::estAdministrateur() ? 'utilisateur':'inscription';
		if($S_method != 'creerAction')
		{
			Authentification::accesAdministrateur();
		}	
	}
	
	public function defautAction()
	{
		BoiteAOutils::redirigerVers('utilisateur/liste');
	}
	
    public function listeAction(Array $A_parametres = null)
    {
    	$I_page = isset($_SESSION['page_utilisateur']) ? BoiteAOutils::recupererDepuisSession('page_utilisateur') : 1;
        $I_page = isset($A_parametres[0]) ? $A_parametres[0] : $I_page;      
        
        $O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());

        $O_listeur = new ListeurUtilisateur($O_utilisateurMapper);
        $O_paginateur = new Paginateur($O_listeur);
        $O_paginateur->changeLimite(Constantes::NB_MAX_UTILISATEURS_PAR_PAGE);

        // on doit afficher puis installer la pagination
        try
        {
        	$A_utilisateurs = $O_paginateur->recupererPage($I_page);
        }
        catch (Exception $O_exception)
        {
        	BoiteAOutils::stockerMessage($O_exception->getMessage());
        	BoiteAOutils::redirigerVers($this->_S_urlDefaut);	
        }
        
        $A_pagination = $O_paginateur->paginer();
        
        BoiteAOutils::rangerDansSession('page_utilisateur', $I_page);

        // voir ce qu'on met dans utilisateurs !
        Vue::montrer('utilisateur/form');
        Vue::montrer ('utilisateur/liste', array('utilisateurs' => $A_utilisateurs, 'pagination' => $A_pagination));
    }
    
    public function creerAction()
    {
    	$S_url = Authentification::estAdministrateur() ? 'utilisateur' : ''; 
    	
    	$O_formulaire = new Formulaire(array(
    		'login' => 'login',
    		'motdepasse'  => 'pwd'		
    	));
    	
    	if(!$O_formulaire->estValide())
    	{
    		BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
    		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
    	}
    	
    	$O_utilisateur = new Utilisateur();
    	$O_utilisateur->changeLogin($O_formulaire->donneContenu('login'));
    	$O_utilisateur->changeMotDePasse(BoiteAOutils::crypterMotDePasse($O_utilisateur, $O_formulaire->donneContenu('motdepasse')));
    	
    	try
    	{
    		$O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());    		
    		$O_utilisateur->changeIdentifiant($O_utilisateurMapper->creer($O_utilisateur));
    	}
    	catch(Exception $O_exception)
    	{
    		BoiteAOutils::stockerErreur($O_exception->getMessage());
    		BoiteAOutils::redirigerVers($this->_S_urlDefaut);
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
                BoiteAOutils::redirigerVers('');
            }

            // Si l'on est ici c'est qu'on a tout ce qu'il nous faut (un utilisateur !)
            // Nous le passons à la vue correspondante
            Vue::montrer('utilisateur/edit/form', array('utilisateur' => $O_utilisateur));
        }
    }

    public function miseajourAction(Array $A_parametres)
    {
        $I_identifiantUtilisateur = $A_parametres[0];
        $S_login = $_POST['login'];
        // TODO: vérifications sur l'input, même si PDO nettoie derrière

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

    public function supprAction(Array $A_parametres)
    {
        $I_identifiantUtilisateur = $A_parametres[0];

        $O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());
        $O_utilisateur = $O_utilisateurMapper->trouverParIdentifiant($I_identifiantUtilisateur);
        $O_utilisateurMapper->supprimer($O_utilisateur);
		
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