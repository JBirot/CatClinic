<?php

class ProprietaireMapper extends SqlDataMapper
{
    private $A_liaison = array (
                                'cible' => 'chat',
                                'via' => array ('cible' => 'proprietaire_chat', 'cle_etrang' => 'id_chat'),
                                'cle_etrang' => 'id_proprietaire'
                        );

    public function __construct(Connexion $O_connexion)
    {
        parent::__construct(Constantes::TABLE_PROPRIETAIRE);
        $this->_S_classeMappee = 'Proprietaire';
        $this->_O_connexion = $O_connexion;
    }

    public function trouverParIntervalle ($I_debut=NULL, $I_fin=NULL,array $A_ordre = NULL)
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
        $A_proprietaires = array ();

        foreach ($this->_O_connexion->projeter($S_requete, $A_paramsRequete) as $O_proprietaireEnBase)
        {
            // $O_proprietaireEnBase est un objet de la classe prédéfinie StdClass

            $O_proprietaire = new Proprietaire ();

            // Je convertis mon objet StdClass trop "vague" en objet métier Proprietaire !
            $O_proprietaire->changeIdentifiant ($O_proprietaireEnBase->id);
            $O_proprietaire->changeNom ($O_proprietaireEnBase->nom);
            $O_proprietaire->changePrenom ($O_proprietaireEnBase->prenom);

            // je cherche les chats reliés à ce propriétaire
            if ($this->_aLiaison()) {

                $A_ids = $this->_O_connexion->projeter($this->_selectLiaison (), array($O_proprietaireEnBase->id));

                if (count($A_ids)) {
                    foreach ($A_ids as $O_standard) {
                        $O_chatMapper = FabriqueDeMappers::fabriquer('chat', $this->_O_connexion);
                        $O_chat = $O_chatMapper->trouverParIdentifiant((integer)$O_standard->id);
                        $O_proprietaire->ajouteChat($O_chat);
                    }
                }
            }

            // A ce stade j'ai réalisé en quelque sorte une copie de mon objet StdClass en un objet métier de mon application
            $A_proprietaires[] = $O_proprietaire;
        }

        // J'ai crée un tableau d'objets Proprietaire...je le renvoie !
        return $A_proprietaires;
    }

    public function trouverParIdentifiant ($I_identifiant)
    {
        $S_requete    = "SELECT nom, prenom FROM " . $this->_S_nomTable . " WHERE id = ?";
        $A_paramsRequete = array($I_identifiant);

        if ($A_proprietaire = $this->_O_connexion->projeter($S_requete, $A_paramsRequete))
        {
            // On sait donc qu'on aura 1 seul enregistrement dans notre tableau
            $O_proprietaireTemporaire = $A_proprietaire[0];

            if (is_object($O_proprietaireTemporaire)) {
                if (class_exists($this->_S_classeMappee)) {
                    $O_proprietaire = new $this->_S_classeMappee;

                    $O_proprietaire->changeIdentifiant($I_identifiant);
                    $O_proprietaire->changeNom($O_proprietaireTemporaire->nom);
                    $O_proprietaire->changePrenom($O_proprietaireTemporaire->prenom);
                } else {
                    throw new LogicException ('La classe "' . $this->_S_classeMappee . '" n\'existe pas');
                }
            } else {
                throw new LogicException ('Il n\'existe pas de propriétaire pour l\'identifiant ' . $I_identifiant);
            }

            // je cherche les chats reliés à ce propriétaire
            if ($this->_aLiaison()) {

                $A_ids = $this->_O_connexion->projeter($this->_selectLiaison (), array($I_identifiant));

                if (count($A_ids)) {
                    foreach ($A_ids as $O_standard) {
                        $O_chatMapper = FabriqueDeMappers::fabriquer('chat', $this->_O_connexion);
                        $O_chat = $O_chatMapper->trouverParIdentifiant((integer)$O_standard->id);
                        $O_proprietaire->ajouteChat($O_chat);
                    }
                }
            }

            return $O_proprietaire;
        }
        else
        {
            // Je n'ai rien trouvé, je lève une exception pour le signaler au client de ma classe
            throw new Exception ("Il n'existe pas de propriétaire d'identifiant '$I_identifiant'");
        }
    }

    public function creer (Proprietaire $O_proprietaire)
    {
        //$I_identifiantChat = $O_proprietaire->donneChat()->donneIdentifiant();
        $S_prenom = $O_proprietaire->donnePrenom();
        $S_nom = $O_proprietaire->donneNom();

        if (!$S_nom || !$S_prenom)
        {
            throw new Exception ("Impossible d'enregistrer le propriétaire, des informations sont manquantes");
        }

        $S_requete = "INSERT INTO " . $this->_S_nomTable . " (nom, prenom) VALUES (?, ?)";
        $A_paramsRequete = array($S_nom, $S_prenom);
        // j'insère en table et inserer me renvoie l'identifiant de mon nouvel enregistrement...je le stocke
        $O_proprietaire->changeIdentifiant($this->_O_connexion->inserer($S_requete, $A_paramsRequete));

        if (count($O_proprietaire->donneChats())) {
            foreach($O_proprietaire->donneChats() as $O_chat) {
                $O_chatMapper = FabriqueDeMappers::fabriquer('chat', $this->_O_connexion);
                try {
                    $O_chatEnBase = $O_chatMapper->trouverParIdentifiant((integer)$O_chat->donneIdentifiant());
                    $O_chatMapper->actualiser($O_chat);
                } catch (Exception $O_exception) {
                    $O_chatMapper->creer($O_chat);
                }

                $this->_O_connexion->inserer($this->_insertLiaison (), array($O_chat->donneIdentifiant(), $O_proprietaire->donneIdentifiant()));
            }          
        }
    }

    public function actualiser (Proprietaire $O_proprietaire)
    {
        $I_identifiant = $O_proprietaire->donneIdentifiant();

        if (null != $I_identifiant)
        {
            $S_prenom = $O_proprietaire->donnePrenom();
            $S_nom = $O_proprietaire->donneNom();

            if (!$S_nom || !$S_prenom)
            {
                throw new Exception ("Impossible d'enregistrer le propriétaire, des informations sont manquantes");
            }

            $S_requete = "UPDATE " . $this->_S_nomTable . " SET nom = ?, prenom = ? WHERE id = ?";
            $A_paramsRequete = array($S_nom, $S_prenom, $I_identifiant);

            $this->_O_connexion->modifier($S_requete, $A_paramsRequete);

            $O_chatMapper = FabriqueDeMappers::fabriquer('chat', $this->_O_connexion);
            
            // Doit-on créer ou mettre à jour des chats ?
            if (count($O_proprietaire->donneChats())) {

            	foreach($O_proprietaire->donneChats() as $O_chat) {
                	$O_chatEnBase = $O_chatMapper->trouverParIdentifiant((integer)$O_chat->donneIdentifiant());

                    try {
                        $O_chatMapper->actualiser($O_chat);
                    } catch (Exception $O_exception) {
                        $O_chatMapper->creer($O_chat);
                    }
                }
            }
            // Doit-on supprimer des chats ?
            $A_chatsASupprimer = $O_proprietaire->donneOperations(ObjetMetier::SUPPRESSION);
            
            if (count($A_chatsASupprimer)) {
	            foreach ($A_chatsASupprimer as $O_chatASupprimer) {
	            	$O_chatMapper->supprimer($O_chatASupprimer);
	            }
            }
            
            return true;
        }

        return false;
    }

    public function supprimer (Proprietaire $O_proprietaire)
    {
        if (null != $O_proprietaire->donneIdentifiant())
        {
            if (false === $this->_O_connexion->modifier($this->_deleteLiaison (), array($O_proprietaire->donneIdentifiant())))
            {
                throw new Exception ("Impossible de supprimer le proprietaire d'identifiant " . $O_proprietaire->donneIdentifiant());
            }

            // il me faut absolument un identifiant pour faire une suppression
            $S_requete   = "DELETE FROM " . $this->_S_nomTable . " WHERE id = ?";
            $A_paramsRequete = array($O_proprietaire->donneIdentifiant());

            // si modifier echoue elle me renvoie false, si aucun enregistrement n'est supprimé, elle renvoie zéro
            // attention donc à bien utiliser l'égalité stricte ici !
            if (false === $this->_O_connexion->modifier($S_requete, $A_paramsRequete))
            {
                throw new Exception ("Impossible de supprimer le proprietaire d'identifiant " . $O_proprietaire->donneIdentifiant());
            }

            if (count($O_proprietaire->donneChats())) {
                foreach($O_proprietaire->donneChats() as $O_chat) {
                    $O_chatMapper = FabriqueDeMappers::fabriquer('chat', $this->_O_connexion);
                    $O_chatMapper->supprimer($O_chat);
                }
            }

            return true;
        }

        return false;
    }

    private function _aLiaison () {
        return !empty($this->A_liaison);
    }

    private function _selectLiaison () {
        $S_requeteLiaison = 'SELECT DISTINCT(cible.id) from ' . $this->_S_nomTable . ' as source ';
        $S_requeteLiaison .= ' INNER JOIN ' . $this->A_liaison['via']['cible'] . ' AS liaison ON (source.id = liaison' . '.' . $this->A_liaison['cle_etrang'];
        $S_requeteLiaison .= ') INNER JOIN ' . $this->A_liaison['cible'] . ' AS cible';
        $S_requeteLiaison .= ' ON (cible.id = liaison.' . $this->A_liaison['via']['cle_etrang'] . ') WHERE source.id = ?';

        return $S_requeteLiaison;
    }

    private function _insertLiaison () {
        $S_requeteLiaison = 'INSERT INTO ' . $this->A_liaison['via']['cible'] . ' (';
        $S_requeteLiaison .= $this->A_liaison['via']['cle_etrang'] . ',' . $this->A_liaison['cle_etrang'] . ')';
        $S_requeteLiaison .= ' VALUES (?, ?)';

        return $S_requeteLiaison;
    }

    private function _deleteLiaison () {
        $S_requeteLiaison = 'DELETE FROM ' . $this->A_liaison['via']['cible'] . ' WHERE id_proprietaire = ?';

        return $S_requeteLiaison;
    }
}