<?php

class VisiteMapper extends SqlDataMapper
{
    public function __construct(Connexion $O_connexion)
    {
        parent::__construct(Constantes::TABLE_VISITE);
        $this->_A_champsTriables=array('id','id_praticien','id_chat','date','prix');
        $this->_S_classeMappee = 'Visite';
        $this->_O_connexion = $O_connexion;
    }

    public function trouverParIntervalle ($I_debut=NULL, $I_fin=NULL,array $A_ordre = NULL)
    {
        $S_requete = 'SELECT id, id_praticien, id_chat, date, prix, observations  FROM ' . $this->_S_nomTable;

    	$A_paramsRequete = null;
		if ($A_ordre)
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
		
        $A_visites = array ();

        foreach ($this->_O_connexion->projeter($S_requete, $A_paramsRequete) as $O_visiteEnBase)
        {
            // $O_visiteEnBase est un objet de la classe prédéfinie StdClass

            $O_visite = new Visite ();

            // Je convertis mon objet StdClass trop "vague" en objet métier Visite !
            $O_visite->changeIdentifiant ($O_visiteEnBase->id);
            $O_visite->changePrix ($O_visiteEnBase->prix);
            $O_visite->changeDate (new DateTime($O_visiteEnBase->date));
            $O_visite->changeObservations ($O_visiteEnBase->observations);

            $O_mapperPraticien = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
            $O_praticien = $O_mapperPraticien->trouverParIdentifiant($O_visiteEnBase->id_praticien);
            $O_visite->changePraticien($O_praticien);

            $O_mapperChat = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
            $O_chat = $O_mapperChat->trouverParIdentifiant($O_visiteEnBase->id_chat);
            $O_visite->changeChat($O_chat);            

            // A ce stade j'ai réalisé en quelque sorte une copie de mon objet StdClass en un objet métier de mon application
            $A_visites[] = $O_visite;
        }

        // J'ai crée un tableau d'objets Proprietaire...je le renvoie !
        return $A_visites;
    }

    public function trouverParIdentifiant ($I_identifiant)
    {
        $S_requete    = "SELECT id, id_praticien, id_chat, date, prix, observations FROM " . $this->_S_nomTable .
                        " WHERE id = $I_identifiant";

        if ($A_visite = $this->_O_connexion->projeter($S_requete))
        {
            $O_visiteTemporaire = $A_visite[0];

            if (is_object($O_visiteTemporaire)) {
                if (class_exists($this->_S_classeMappee)) {
                    $O_visite = new $this->_S_classeMappee;

                    $O_visite->changeIdentifiant($O_visiteTemporaire->id);
                    $O_visite->changePrix($O_visiteTemporaire->prix);
                    $O_visite->changeDate(new DateTime($O_visiteTemporaire->date));
                    $O_visite->changeObservations($O_visiteTemporaire->observations);

                    $O_mapperPraticien = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
                    $O_praticien = $O_mapperPraticien->trouverParIdentifiant($O_visiteTemporaire->id_praticien);
                    $O_visite->changePraticien($O_praticien);

                    $O_mapperChat = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
                    $O_chat = $O_mapperChat->trouverParIdentifiant($O_visiteTemporaire->id_chat);
                    $O_visite->changeChat($O_chat);

                    return $O_visite;
                }
            }
        }
        else
        {
            // Je n'ai rien trouvé, je lève une exception pour le signaler au client de ma classe
            throw new Exception ("Il n'existe pas de visite pour l'identifiant '$I_identifiant'");
        }
    }

    public function trouverParIdentifiantChat ($I_identifiantChat)
    {
        $S_requete    = "SELECT id_praticien, id_chat, date, prix, observations FROM " . $this->_S_nomTable .
                        " WHERE id_chat = ?"; // on peut renvoyer plusieurs visites pour un chat
        $A_paramsRequete = array($I_identifiantChat);

        if ($A_visite = $this->_O_connexion->projeter($S_requete, $A_paramsRequete))
        {
            $A_visites = null;

            if (class_exists($this->_S_classeMappee)) {

                foreach ($A_visite as $O_visiteEnBase)
                {
                    $O_visite = new $this->_S_classeMappee;
                    $O_visite->changePrix($O_visiteEnBase->prix);
                    $O_visite->changeDate(new DateTime($O_visiteEnBase->date));
                    $O_visite->changeObservations($O_visiteEnBase->observations);

                    $O_mapperPraticien = FabriqueDeMappers::fabriquer('praticien', Connexion::recupererInstance());
                    $O_praticien = $O_mapperPraticien->trouverParIdentifiant($O_visiteEnBase->id_praticien);
                    $O_visite->changePraticien($O_praticien);

                    $O_mapperChat = FabriqueDeMappers::fabriquer('chat', Connexion::recupererInstance());
                    $O_chat = $O_mapperChat->trouverParIdentifiant($O_visiteEnBase->id_chat);
                    $O_visite->changeChat($O_chat);

                    $A_visites[] = $O_visite;
                }
            }

            return $A_visites;
        }
        else
        {
            // Je n'ai rien trouvé, je lève une exception pour le signaler au client de ma classe
            throw new Exception ("Il n'existe pas de visite pour l'identifiant de chat $I_identifiantChat");
        }
    }

    public function creer(Visite $O_visite)
    {
        $F_prix = $O_visite->donnePrix();
        $S_date = $O_visite->donneDate();

        if (!$F_prix || !$S_date)
        {
            throw new Exception ("Impossible de créer la visite, des informations sont manquantes");
        }

        // Les observations sont facultatives
        $S_observations = $O_visite->donneObservations();

        // Par contre, il faut un chat ET un praticien pour valider une visite

        $O_praticien = $O_visite->donnePraticien();

        if (!$O_praticien) {
            throw new Exception ("Impossible de créer la visite, le praticien est manquant");
        }

        $O_chat = $O_visite->donneChat();

        if (!$O_chat) {
            throw new Exception ("Impossible de créer la visite, le chat est manquant");
        }

        $I_identifiantChat = $O_chat->donneIdentifiant();
        $I_identifiantPraticien = $O_praticien->donneIdentifiant();

        if ( !$I_identifiantChat || !$I_identifiantPraticien) {
            throw new Exception ("Impossible de créer la visite, des informations sont manquantes");
        }

        $S_requete = "INSERT INTO " . $this->_S_nomTable . " (id_praticien, id_chat, date, prix, observations ) VALUES (?, ?, ?, ?, ?)";
        $A_paramsRequete = array($I_identifiantPraticien, $I_identifiantChat, $S_date, $F_prix, $S_observations);

        // j'insère en table et inserer me renvoie l'identifiant de mon nouvel enregistrement...je le stocke
        $O_visite->changeIdentifiant($this->_O_connexion->inserer($S_requete, $A_paramsRequete));
    }

    public function actualiser(Visite $O_visite)
    {
        if (!is_null($O_visite->donneIdentifiant()))
        {
        	$I_identifiant = $O_visite->donneIdentifiant();
			$S_observations = $O_visite->donneObservations();
			$S_date = $O_visite->donneDate();
			$F_prix = $O_visite->donnePrix();
			$I_idPraticien = $O_visite->donnePraticien()->donneIdentifiant();
			$I_idChat = $O_visite->donneChat()->donneIdentifiant();            

            $S_requete   = "UPDATE " . $this->_S_nomTable . " SET id_praticien = ?, id_chat = ?, date = ?, prix = ?, observations = ? WHERE id = ?";
            $A_paramsRequete = array(	$I_idPraticien,
            							$I_idChat,
            							$S_date,
            							$F_prix,
            							$S_observations,
            							$I_identifiant);

            $this->_O_connexion->modifier($S_requete, $A_paramsRequete);

            return true;
        }

        return false;
    }

    public function supprimer(Visite $O_visite)
    {
        if (!is_null($O_visite->donneIdentifiant()))
        {
            // il me faut absolument un identifiant pour faire une suppression
            $S_requete   = "DELETE FROM " . $this->_S_nomTable . " WHERE id = ?";
            $A_paramsRequete = array($O_visite->donneIdentifiant());

            // si modifier echoue elle me renvoie false, si aucun enregistrement n'est supprimé, elle renvoie zéro
            // attention donc à bien utiliser l'égalité stricte ici !
            if (false === $this->_O_connexion->modifier($S_requete, $A_paramsRequete))
            {
                throw new Exception ("Impossible de supprimer la visite d'identifiant " . $O_visite->donneIdentifiant());
            }

            return true;
        }

        return false;
    }
}