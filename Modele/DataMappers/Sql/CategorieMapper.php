<?php

class CategorieMapper extends SqlDataMapper
{
	private $_S_tableArticle;
	
	public function __construct(Connexion $O_connexion)
	{
		parent::__construct(Constantes::TABLE_CATEGORIE);
		$this->_A_champsTriables = array('id','titre');
		$this->_S_tableArticle = Constantes::TABLE_ARTICLE;
		$this->_S_classeMappee = 'Categorie';
		$this->_O_connexion = $O_connexion;
	}
	
	public function trouverParIntervalle ($I_debut = NULL, $I_fin = NULL,array $A_ordre = NULL)
    {
        $S_requete = 'SELECT id, titre FROM ' . $this->_S_nomTable;
        $A_paramsReq = null;
		if($A_ordre)
		{
			if (isset($this->_A_champsTriables[$A_ordre[0]]))
			{
				$S_sens = $A_ordre[1] ? 'DESC':'';
				$S_requete .= ' ORDER BY '.$this->_A_champsTriables[$A_ordre[0]].' '.$S_sens;
			}
		}
        if (null!==$I_debut && null!==$I_fin)
        {
        	$S_requete .= ' LIMIT :debut, :fin';
        	$A_paramsReq['debut'] = array(intval($I_debut), Connexion::PARAM_ENTIER);
        	$A_paramsReq['fin'] = array(intval($I_fin), Connexion::PARAM_ENTIER);
        }

        $A_categories = array ();

        foreach ($this->_O_connexion->projeter($S_requete, $A_paramsReq) as $O_categorieEnBase)
        {
            if($O_categorie = $this->hydrater($O_categorieEnBase)){
            $A_categories[] = $O_categorie;}
        }

        // J'ai crée un tableau d'objets categorie...je le renvoie !
        return $A_categories;
    }
    
    public function trouverParIdentifiant($I_identifiant)
    {
    	if(isset($I_identifiant))
    	{
    		$S_requete	= 	"SELECT id, titre FROM " . $this->_S_nomTable . 
    						" WHERE id = ?";
    		$A_paramsRequete = array($I_identifiant);
    		
    		if($A_categorie = $this->_O_connexion->projeter($S_requete,$A_paramsRequete))
    		{
    			$O_categorieEnBase = $A_categorie[0];
    			
    			if(is_object($O_categorieEnBase))
    			{
    				$O_categorie = $this->hydrater($O_categorieEnBase);    					
    				return $O_categorie;    				
    			}
				else 
				{
					throw new Exception("Une erreur s'est produite pour la catégorie d'identifiant '$I_identifiant'");
				}
    		}
    		else 
    		{
    			// Je n'ai rien trouvé, je lève une exception pour le signaler au client de ma classe
    			throw new Exception ("Il n'existe pas de catégorie pour l'identifiant '$I_identifiant'");    			
    		}
    	}
    	else
    	{
    		throw new Exception ("L'identifiant d'une catégorie ne peut être vide et doit être un entier");
    	}
    }
    
    public function creer(Categorie $O_categorie)
    {
    	if(!$O_categorie->est_valide())
    	{
    		throw new Exception ("Impossible d'enregistrer la categorie, des informations sont manquantes");
    	}
    	
    	$S_titre = $O_categorie->donneTitre();
    	
    	$S_requete	=	"INSERT INTO " . $this->_S_nomTable . " (titre) VALUES (?) ";
    	$A_paramsRequete = array($S_titre);
    	
    	try
    	{
    		$O_categorie->changeIdentifiant($this->_O_connexion->inserer($S_requete,$A_paramsRequete));
    	}
        catch (Exception $O_exception)
    	{
    		if($O_exception->getCode() == 23000 )
    		{
    			throw new Exception("La categorie " . $O_categorie->donneTitre() . " existe déjà.");
    		}
    		else
    		{
    			throw new Exception($O_exception->getMessage());
    		}
    	}
    }
    
    public function actualiser(Categorie $O_categorie)
    {
    	if(null != $O_categorie->donneIdentifiant())
    	{
    		if(!$O_categorie->est_valide())
    		{
    			throw new Exception("Impossible de mettre à jour la catégorie d'identifiant " . $O_categorie->donneIdentifiant());
    		}
    		
    		$S_titre = $O_categorie->donneTitre();
    		$I_identifiant = $O_categorie->donneIdentifiant();
    		
    		$S_requete = "UPDATE " . $this->_S_nomTable . " SET titre = ? WHERE id = ?";
    		$A_paramsRequete = array($S_titre, $I_identifiant);
    		
    		$this->_O_connexion->modifier($S_requete, $A_paramsRequete);
    		
    		return true;
    	}
    	
    	return false;
    }
    
    public function supprimer(Categorie $O_categorie, Categorie $O_categorieRemplacement = null)
    {
    	if(null === $O_categorie->donneIdentifiant())
    	{
    		throw new Exception("Impossible de trouver l'identifiant de la catégorie à supprimer");
    	}
   		if(isset($O_categorieRemplacement))
   		{
   			if(null === $O_categorieRemplacement->donneIdentifiant())
   			{
   				throw new Exception("Impossible de trouver l'identifiant de la catégorie de remplacement.");
   			}
   			$S_requete = "UPDATE ".$this->_S_tableArticle." SET id_categorie = ? WHERE id_categorie = ?";
   			$A_paramsRequete = array($O_categorieRemplacement->donneIdentifiant(),$O_categorie->donneIdentifiant());
   			if(false=== $this->_O_connexion->modifier($S_requete, $A_paramsRequete))
   			{
   				throw new Exception("Impossible de remplacer la catégorie des articles afin de supprimer la catégorie d'identifiant ".$O_categorie->donneIdentifiant().".");
   			}	
   		}
   		
   		$S_requete = "DELETE FROM " . $this->_S_nomTable . " WHERE id = ?";
   		$A_paramsRequete = array($O_categorie->donneIdentifiant());
   		
   		if(false=== $this->_O_connexion->modifier($S_requete, $A_paramsRequete))
   		{
   			throw new Exception("Impossible de supprimer la catégorie d'identifiant " . $O_categorie->donneIdentifiant());
   		}
  		
   		return true;
    }
    
    private function hydrater($O_categorieEnBase)
    {    	
    	if(!class_exists($this->_S_classeMappee)) return false;
    	
    	$O_categorie = new $this->_S_classeMappee;

    	$O_categorie->changeIdentifiant ($O_categorieEnBase->id);
    	$O_categorie->changeTitre ($O_categorieEnBase->titre);

    	return $O_categorie;
    }
}