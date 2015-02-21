<?php

class PraticienMapper extends SqlDataMapper
{
    public function __construct(Connexion $O_connexion)
    {
        parent::__construct(Constantes::TABLE_PRATICIEN);
        $this->_S_classeMappee = 'Praticien';
        $this->_A_champsTriables = array('id','nom','prenom');
        $this->_O_connexion = $O_connexion;
    }

    public function trouverParIntervalle ($I_debut=NULL, $I_fin=NULL, array $A_ordre = NULL)
    {
        $S_requete = 'SELECT id, nom, prenom FROM ' . $this->_S_nomTable;
		$A_paramsRequete = null;
		if($A_ordre)
		{
			if(isset($this->_A_champsTriables[$A_ordre[0]]))
			{
				$S_sens = $A_ordre[1]?'DESC':'';
				$S_requete.=' ORDER BY '.$this->_A_champsTriables[$A_ordre[0]].' '.$S_sens;
			}
		}
    	if(null !== $I_debut && null !== $I_fin)
		{
			$S_requete.= ' LIMIT :debut, :fin';
			$A_paramsRequete['debut']=array(intval($I_debut),Connexion::PARAM_ENTIER);
			$A_paramsRequete['fin']=array(intval($I_fin),Connexion::PARAM_ENTIER);
		}
        $A_praticiens = array ();

        foreach ($this->_O_connexion->projeter($S_requete, $A_paramsRequete) as $O_praticienEnBase)
        {
            // $O_praticienEnBase est un objet de la classe prédéfinie StdClass

            $O_praticien = new Praticien ();

            // Je convertis mon objet StdClass trop "vague" en objet métier Praticien !
            $O_praticien->changeIdentifiant ($O_praticienEnBase->id);
            $O_praticien->changeNom ($O_praticienEnBase->nom);
            $O_praticien->changePrenom ($O_praticienEnBase->prenom);

            // A ce stade j'ai réalisé en quelque sorte une copie de mon objet StdClass en un objet métier de mon application
            $A_praticiens[] = $O_praticien;
        }

        // J'ai crée un tableau d'objets Praticien...je le renvoie !
        return $A_praticiens;
    }

    public function trouverParIdentifiant ($I_identifiant)
    {
        $S_requete    = "SELECT nom, prenom FROM " . $this->_S_nomTable . " WHERE id = ?";
        $A_paramsRequete = array($I_identifiant);

        if ($A_praticien = $this->_O_connexion->projeter($S_requete, $A_paramsRequete))
        {
            // On sait donc qu'on aura 1 seul enregistrement dans notre tableau
            $O_praticienTemporaire = $A_praticien[0];

            if (is_object($O_praticienTemporaire)) {
                if (class_exists($this->_S_classeMappee)) {
                    $O_praticien = new $this->_S_classeMappee;

                    $O_praticien->changeIdentifiant($I_identifiant);
                    $O_praticien->changeNom($O_praticienTemporaire->nom);
                    $O_praticien->changePrenom($O_praticienTemporaire->prenom);
                } else {
                    throw new LogicException ('La classe "' . $this->_S_classeMappee . '" n\'existe pas');
                }
            } else {
                throw new LogicException ('Il n\'existe pas de praticien pour l\'identifiant ' . $I_identifiant);
            }

            return $O_praticien;
        }
        else
        {
            // Je n'ai rien trouvé, je lève une exception pour le signaler au client de ma classe
            throw new Exception ("Il n'existe pas de praticien d'identifiant '$I_identifiant'");
        }
    }

    public function creer (Praticien $O_praticien)
    {
        $S_prenom = $O_praticien->donnePrenom();
        $S_nom = $O_praticien->donneNom();

        if (!$S_nom || !$S_prenom)
        {
            throw new Exception ("Impossible d'enregistrer le praticien, des informations sont manquantes");
        }

        $S_requete = "INSERT INTO " . $this->_S_nomTable . " (nom, prenom) VALUES (?, ?)";
        $A_paramsRequete = array($S_nom, $S_prenom);
        // j'insère en table et inserer me renvoie l'identifiant de mon nouvel enregistrement...je le stocke
        $O_praticien->changeIdentifiant($this->_O_connexion->inserer($S_requete, $A_paramsRequete));
    }

    public function actualiser (Praticien $O_praticien)
    {
        $I_identifiant = $O_praticien->donneIdentifiant();

        if (null != $I_identifiant)
        {
            $S_prenom = $O_praticien->donnePrenom();
            $S_nom = $O_praticien->donneNom();

            if (!$S_nom || !$S_prenom)
            {
                throw new Exception ("Impossible de mettre à jour le praticien, des informations sont manquantes");
            }

            $S_requete = "UPDATE " . $this->_S_nomTable . " SET nom = ?, prenom = ? WHERE id = ?";
            $A_paramsRequete = array($S_nom, $S_prenom, $I_identifiant);

            $this->_O_connexion->modifier($S_requete, $A_paramsRequete);

            return true;
        }

        return false;
    }

    public function supprimer (Praticien $O_praticien)
    {
        if (null != $O_praticien->donneIdentifiant())
        {
            // il me faut absolument un identifiant pour faire une suppression
            $S_requete   = "DELETE FROM " . $this->_S_nomTable . " WHERE id = ?";
            $A_paramsRequete = array($O_praticien->donneIdentifiant());

            // si modifier echoue elle me renvoie false, si aucun enregistrement n'est supprimé, elle renvoie zéro
            // attention donc à bien utiliser l'égalité stricte ici !
            if (false === $this->_O_connexion->modifier($S_requete, $A_paramsRequete))
            {
                throw new Exception ("Impossible de supprimer le praticien d'identifiant " . $O_praticien->donneIdentifiant());
            }

            return true;
        }

        return false;
    }
}