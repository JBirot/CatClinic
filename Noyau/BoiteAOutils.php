<?php

// C'est notre "magasin général" :)

final class BoiteAOutils
{
    public static function demarrerSession()
    {
        // Je donne un nom explicite à ma session, autre que le traditionnel (et ennuyeux) PHPSESSID
        session_name('catclinic_user');
        session_start();
    }
    
    public static function regenererIdentifiantSession()
    {
        // Destiné à éviter les attaques par fixation de session
        session_regenerate_id();
    }

    public static function recupererDepuisSession($S_nom, $B_detruire = false)
    {
        if (isset($_SESSION[$S_nom]))
        {
            $M_valeur = $_SESSION[$S_nom];

            if ($B_detruire) {
                // On détruit la variable après l'avoir récupérée
                unset($_SESSION[$S_nom]);
            }

            return $M_valeur;
        }
        
        return null;
    }
    
    public static function rangerDansSession($S_nom, $M_valeur)
    {
        $_SESSION[$S_nom] = $M_valeur;
    }
    
    public static function stockerErreur($S_erreur)
    {
        self::rangerDansSession('erreur', $S_erreur);
    }
    
    public static function stockerMessage($S_message)
    {
    	self::rangerDansSession('message', $S_message);
    }
    
    public static function redirigerVers ($S_url)
    {
        $S_url = '/' . strtolower($S_url);

        // lorsque l'on redirige, on ne doit rien renvoyer d'autre qu'une entête HTTP (je vous renvoie au cours)
        // il ne doit pas y avoir de corps, voilà la raison de la présence du die
        // je vous invite également à lire : http://php.net/manual/fr/function.header.php
        die(header('Location: ' . $S_url));
    }
    
    public static function crypterMotDePasse (Utilisateur $O_utilisateur, $S_motdePasse, $S_algorithme = 'sha1')
    {
        if (function_exists($S_algorithme)) {
            return $S_algorithme($O_utilisateur->donneLogin() . $S_motdePasse);
        }

        throw new BadFunctionCallException("L'algorithme de cryptage demandé n'existe pas ou n'est pas disponible");
    }
}