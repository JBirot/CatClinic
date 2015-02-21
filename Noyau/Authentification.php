<?php

final class Authentification
{
    public static function estConnecte()
    {
        return !is_null(BoiteAOutils::recupererDepuisSession('utilisateur'));
    }
    
    public static function estProprietaire()
    {
    	return !is_null((BoiteAOutils::recupererDepuisSession('utilisateur') ? BoiteAOutils::recupererDepuisSession('utilisateur')->donneProprietaire():null));
    }
    
    public static function estAdministrateur()
    {
        // pour qu'un utilisateur soit admin il faut qu'il soit loggué ET admin
        return self::estConnecte() && BoiteAOutils::recupererDepuisSession('utilisateur')->estAdministrateur();
    }
    
    public static function accesAdministrateur($S_url = '')
    {
    	//Redirigera vers l'accueil avec un message d'erreur, tout utilisateur qui n'est pas administrateur
    	if(!self::estAdministrateur())
    	{
    		BoiteAOutils::stockerErreur("Vous n'êtes pas administrateur.");
    		BoiteAOutils::redirigerVers($S_url);
    		exit;
    	}
    }
    
    public static function accesNonConnecte($S_url = '')
    {
    	//Redirige sur la page par défaut si l'utilisateur est connecté
    	if(self::estConnecte())
    	{
    		BoiteAOutils::stockerErreur("Vous ne pouvez pas accéder à cette page en étant connecté.");
    		BoiteAOutils::redirigerVers($S_url);
    		exit;
    	}
    }
    
    public static function accesConnecte($S_url = '')
    {
    	//Redirige l'utilisateur s'il n'est pas connecté
    	if(!self::estConnecte())
    	{
    		BoiteAOutils::stockerErreur("Vous devez être connecté pour accéder à cette page.");
    		BoiteAOutils::redirigerVers($S_url);
    		exit;
    	}
    }
    
    public static function accesProprietaire($S_url ='')
    {	//Redirige un utilisateur s'il n'est pas proprietaire
    	if(!self::estProprietaire())
    	{
    		BoiteAOutils::stockerErreur("Vous devez être propriètaire pour accéder à cette page.");
    		BoiteAOutils::redirigerVers($S_url);
    		exit;
    	}    	
    }
    
    public static function authentifier (Utilisateur $O_utilisateur, $S_motdePasse, $S_algorithme = 'sha1')
    {
        $O_authentificateur = self::fabrique($S_algorithme);
        return $O_authentificateur->authentifier($O_utilisateur, $S_motdePasse);
    }
    
    protected static function fabrique($S_algorithme)
    {
        // mon usine a fabriquer des authentificateurs...
        // on lui donne le type d'authentificateur souhaité et elle le retourne (s'il existe...évidemment !)
        $S_type  = ucfirst(strtolower($S_algorithme));
        $S_classe= "Authentificateur" . $S_type;

        if (class_exists($S_classe)) {
            return new $S_classe;
        } else {
            throw new AuthentificationException ($S_type . ' n\'est pas un module d\'authentification valide.');    
        }
    }
}