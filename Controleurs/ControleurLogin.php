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
        // Ici la vérification des données entrées par l'utilisateur est inexistante
        // Regardez du côté de http://php.net/manual/fr/function.filter-var.php

        $S_login      = isset($_POST['login']) ? $_POST['login'] : null;
        $S_motdepasse = isset($_POST['motdepasse']) ? $_POST['motdepasse'] : null;

        // on va mémoriser l'identifiant de l'utilisateur, il n'aura pas à le retaper
        BoiteAOutils::rangerDansSession('login', $S_login);

        if (null == $S_login) {
            // On positionne le message d'erreur qui sera affiché
            $S_erreur = 'L\'identifiant est vide.';
            BoiteAOutils::stockerErreur($S_erreur);
            // On redirige, le message s'affichera dans la zone prévue à cet effet
            BoiteAOutils::redirigerVers('login');
        }

        if (null == $S_motdepasse) {
            // on va mémoriser l'identifiant de l'utilisateur, il n'aura pas à le retaper
            BoiteAOutils::rangerDansSession('login', $S_login);
            // On positionne le message d'erreur qui sera affiché
            BoiteAOutils::stockerErreur('Le mot de passe est vide.');
            BoiteAOutils::redirigerVers('login');
        }

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