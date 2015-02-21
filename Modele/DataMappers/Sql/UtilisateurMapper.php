<?php

class UtilisateurMapper extends SqlDataMapper
{
    public function __construct(Connexion $O_connexion)
    {
        parent::__construct(Constantes::TABLE_UTILISATEUR);
        $this->_A_champsTriables = array('id','login','admin');
        $this->_S_classeMappee = 'utilisateur';
        $this->_O_connexion = $O_connexion;
    }

    public function trouverParIntervalle ($I_debut = null, $I_fin = null,array $A_ordre = null) {
        $S_requete = 'SELECT id, login, motdepasse, admin FROM ' . $this->_S_nomTable;
        $A_paramsReq = null;
        
        if($A_ordre)
        {
        	if(isset($this->_A_champsTriables[$A_ordre[0]]))
        	{
        		$S_sens = $A_ordre[1] ? 'DESC':'';
        		$S_requete .= ' ORDER BY '.$this->_A_champsTriables[$A_ordre[0]].' '.$S_sens;
        
        	}
        }
        
        if (null !== $I_debut && null !== $I_fin)
        {
        	$S_requete .= ' LIMIT :debut, :fin';
        	$A_paramsReq['debut'] = array(intval($I_debut), Connexion::PARAM_ENTIER);
        	$A_paramsReq['fin'] = array(intval($I_fin), Connexion::PARAM_ENTIER);
        }
        
        $A_utilisateurs = array();
        
        foreach ($this->_O_connexion->projeter($S_requete, $A_paramsReq) as $O_utilisateurEnBase)
        {
            // $O_utilisateurEnBase est un objet de la classe prédéfinie StdClass
        	// Je convertis mon objet StdClass trop "vague" en objet métier Utilisateur !
        	// Si la classe mappée est incorrecte aucun message d'erreur n'est envoyé ici, seulement une variable vide.
            if($O_utilisateur = $this->hydrater($O_utilisateurEnBase))
            {
            	// A ce stade j'ai réalisé en quelque sorte une copie de mon objet StdClass en un objet métier de mon application
            	$A_utilisateurs[] = $O_utilisateur;
            }
        }

        // J'ai (si des enregistrements existent, évidemment) crée un tableau d'objets Utilisateur...je le renvoie !
        return $A_utilisateurs;
    }

    public function trouverParIdentifiant ($I_identifiant)
    {
    	if(!isset($I_identifiant))
    	{
    		throw new Exception("L'identifiant d'un utilisateur ne peut être vide.");
    	}
        $S_requete    = "SELECT id, login, motdepasse, admin FROM " . $this->_S_nomTable .
                        " WHERE id = ?";
        $A_paramsRequete = array($I_identifiant);

        if ($A_utilisateur = $this->_O_connexion->projeter($S_requete, $A_paramsRequete))
        {
            // on sait donc qu'on aura 1 seul enregistrement dans notre tableau au max
            // c'est un objet de type stdClass
            $O_utilisateurEnBase = $A_utilisateur[0];

             if (is_object($O_utilisateurEnBase)) {
                if(!$O_utilisateur = $this->hydrater($O_utilisateurEnBase))throw new Exception("La classe mappée " . $this->_S_classeMappee . " est introuvable.");
                return $O_utilisateur;
            }
            else 
            {
            	throw new Exception("Une erreur s'est produite avec l'utilisateur d'identifiant " . $I_identifiant);
            }
        }
        else
        {
            // Je n'ai rien trouvé, je lève une exception pour le signaler au client de ma classe
            throw new Exception ("Il n'existe pas d'utilisateur d'identifiant '$I_identifiant'");
        }
    }

    public function trouverParLogin ($S_login)
    {
        $S_requete = "SELECT id, login, motdepasse, admin, id_proprietaire FROM " . $this->_S_nomTable . " WHERE login = ?";
        $A_paramsRequete = array($S_login);

        if ($A_utilisateur = $this->_O_connexion->projeter($S_requete, $A_paramsRequete))
        {
            // on sait donc qu'on aura 1 seul enregistrement dans notre tableau, car login est unique
        	$O_utilisateurEnBase = $A_utilisateur[0];

             if (is_object($O_utilisateurEnBase)) {
                if(!$O_utilisateur = $this->hydrater($O_utilisateurEnBase))throw new Exception("La classe mappée " . $this->_S_classeMappee . " est introuvable.");
            }
            else 
            {
            	throw new Exception("Une erreur est survenue avec l'utilisateur de login " . $S_login);
            }

            // je regarde si un propriétaire est relié à mon compte utilisateur
            // mais seulement si je ne suis pas admin

            if (!$O_utilisateur->estAdministrateur())
            {
                // Un utilisateur n'est pas forcément un propriétaire, mais s'il l'est
                // il faut récupérer ses données de propriétaire !
                try {
                    $O_proprietaireMapper = FabriqueDeMappers::fabriquer('proprietaire', $this->_O_connexion);
                    $O_proprietaire = $O_proprietaireMapper->trouverParIdentifiant($O_utilisateurEnBase->id_proprietaire);
                } catch (Exception $O_exception) {
                    $O_proprietaire = null;
                }

                $O_utilisateur->changeProprietaire($O_proprietaire);
            }

            return $O_utilisateur;
        }
        else
        {
            throw new Exception ("Il n'existe pas d'utilisateur pour ce login");
        }
    }
    
    public function creer (Utilisateur $O_utilisateur)
    {
    	if (!$O_utilisateur->donneLogin() || !$O_utilisateur->donneMotDePasse())
    	{
    		throw new Exception ("Impossible de créer l'utilisateur, des informations sont manquantes.");
    	}
    
    	$S_login = $O_utilisateur->donneLogin();
    	$S_motDePasse = $O_utilisateur->donneMotDePasse();
    	$B_estAdmin = $O_utilisateur->estAdministrateur();
    	$I_idProprietaire = null;
    
    	if ($O_utilisateur->estProprietaire()) {
    		$I_idProprietaire = $O_utilisateur->donneProprietaire()->donneIdentifiant();
    	}
    
    	$S_requete = "INSERT INTO " . $this->_S_nomTable . " (login, motdepasse, admin, id_proprietaire) VALUES (?, ?, ?, ?)";
    	$A_paramsRequete = array($S_login, $S_motDePasse, $B_estAdmin, $I_idProprietaire);
    
    	// j'insère en table et inserer me renvoie l'identifiant de mon nouvel enregistrement...je le stocke
    	try
    	{
    		$O_utilisateur->changeIdentifiant($this->_O_connexion->inserer($S_requete, $A_paramsRequete));
    	}
    	catch (Exception $O_exception)
    	{
    		if($O_exception->getCode() == 23000 )
    		{
    			throw new Exception("L'utilisateur " . $O_utilisateur->donneLogin() . " existe déjà.");
    		}
    		else
    		{
    			throw new Exception($O_exception->getMessage());
    		}
    	}
    
    }

    public function actualiser (Utilisateur $O_utilisateur)
    {
        if (is_null($O_utilisateur->donneIdentifiant()))
        {
        	throw new Exception("Impossible de trouver l'identifiant de l'utilisateur à modifier.");
        }
        if (!$O_utilisateur->donneLogin())
        {
            throw new Exception ("Impossible de mettre à jour l'utilisateur, des informations sont manquantes");
        }

        $S_login = $O_utilisateur->donneLogin();
        $I_identifiant = $O_utilisateur->donneIdentifiant();

        $S_requete   = "UPDATE " . $this->_S_nomTable . " SET login = ? WHERE id = ?";
        $A_paramsRequete = array($S_login, $I_identifiant);

        if(false===$this->_O_connexion->modifier($S_requete, $A_paramsRequete))
        {
        	throw new Exception("Impossible de modifier l'utilisateur d'identifiant " . $I_identifiant);
        }

        return true;
    }

    // Attention : dans notre schéma de base de données, nous avons mis une clause de suppression de type
    // "cascade" au niveau de la table des propriétaires. Ce qui signifie qu'une suppression d'un utilisateur
    // entraine celle du propriétaire associé !

    public function supprimer (Utilisateur $O_utilisateur)
    {
        if (is_null($O_utilisateur->donneIdentifiant()))
        {
        	throw new Exception("Impossible de trouver l'identifiant de l'utilisateur à supprimer.");
        }
        // il me faut absolument un identifiant pour faire une suppression
        $S_requete   = "DELETE FROM " . $this->_S_nomTable . " WHERE id = ?";
        $A_paramsRequete = array($O_utilisateur->donneIdentifiant());

        // si modifier echoue elle me renvoie false, si aucun enregistrement n'est supprimé, elle renvoie zéro
        // attention donc à bien utiliser l'égalité stricte ici !
        if (false === $this->_O_connexion->modifier($S_requete, $A_paramsRequete))
        {
            throw new Exception ("Impossible de supprimer l'utilisateur d'identifiant " . $O_utilisateur->donneIdentifiant());
        }
        return true;
    }
    
    private function hydrater($O_utilisateurEnBase)
    {
    	if(!class_exists($this->_S_classeMappee)){ return false;}
    	
    	$O_utilisateur = new $this->_S_classeMappee;
    	$O_utilisateur->changeIdentifiant($O_utilisateurEnBase->id);
    	$O_utilisateur->changeLogin($O_utilisateurEnBase->login);
    	$O_utilisateur->changeMotDePasse($O_utilisateurEnBase->motdepasse);
    	$O_utilisateur->changeAdmin($O_utilisateurEnBase->admin);
    	return $O_utilisateur;
    }
}