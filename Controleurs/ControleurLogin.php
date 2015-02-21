<?php

final class ControleurLogin
{
	public function __construct()
	{
		Authentification::accesNonConnecte();	
	}
	
    public function defautAction()
    {
        Vue::montrer('login/form');
    }
    
    public function validationAction()
    {
        //Reception et vérifications des données du formulaire
        $O_formulaire = new Formulaire(array(
        	'identifiant'=>	'Login',
        	'mot_de_passe' => 'Pwd'
        ));
        
        if(!$O_formulaire->estValide())
        {
        	BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
        	BoiteAOutils::redirigerVers('');
        	return false;
        }

        $S_login      = $O_formulaire->donneContenu('identifiant');
        $S_motdepasse = $O_formulaire->donneContenu('mot_de_passe');

        // on va mémoriser l'identifiant de l'utilisateur, il n'aura pas à le retaper
        BoiteAOutils::rangerDansSession('login', $S_login);

        try
        {
            $O_utilisateurMapper = FabriqueDeMappers::fabriquer('utilisateur', Connexion::recupererInstance());
            // on part quand même du principe qu'un utilisateur a un login unique (on a mis un index UNIQUE sur login) !
            $O_utilisateur = $O_utilisateurMapper->trouverParLogin($S_login);
        } catch (InvalidArgumentException $O_exception)
        {
            BoiteAOutils::stockerErreur('Une erreur s\'est produite, l\'utilisateur n\'a pas pu être trouvé');
            BoiteAOutils::redirigerVers('login');   
        } catch (Exception $O_exception)
        {
            // On positionne le message d'erreur qui sera affiché
            BoiteAOutils::stockerErreur($O_exception->getMessage());
            BoiteAOutils::redirigerVers('login');
        } 

        // On a trouvé un utilisateur qui a l'identifiant passé, il faut vérifier son mot de passe 
        if (Authentification::authentifier($O_utilisateur, $S_motdepasse))
        {
            // Avant d'enregistrer l'utilisateur en session, on prend soin de retirer le mot de passe
            // et de regenerer l'identifiant de session
            BoiteAOutils::regenererIdentifiantSession();
            $O_utilisateur->changeMotDePasse(null);
            BoiteAOutils::rangerDansSession('utilisateur', $O_utilisateur);
            // Tout s'est bien passé, le message d'erreur est vidé
            BoiteAOutils::stockerErreur(null);
            BoiteAOutils::redirigerVers('');
        }
        else {
            // L'authentification a échouée...
            BoiteAOutils::stockerErreur('Le mot de passe ou l\'identifiant est incorrect.');
            // On renvoie l'utilisateur vers le login
            BoiteAOutils::redirigerVers('login');
        }
    }
}