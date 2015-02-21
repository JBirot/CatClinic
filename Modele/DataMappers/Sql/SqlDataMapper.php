<?php

// Toute classe qui correspond en base à une table doit dériver cette classe abstraite

abstract class SqlDataMapper
{
    // le nom de la table cible en base
    protected $_S_nomTable;
    
    //le nom des champs pouvant servir à un tri
    protected $_A_champsTriables = array();

    // l'identifiant de l'enregistrement courant (servira aux mises à jour et aux suppressions)
    protected $_I_identifiant = null;

    // Le nom de la classe à instancier dans le Mapper
    protected $_S_classeMappee = null;

    // La connexion
    protected $_O_connexion = null;

    // constructeur. Une classe abstraite n'est jamais instanciée donc on ne passera jamais ici DIRECTEMENT
    // Seulement, les classes filles de cette classe feront appel à ce constructeur via un appel statique parent::
    // parce qu'elles font rigoureusement la même chose au nom de la table près, on factorise donc ici le traitement
    public function __construct ($S_nomTable)
    {
        $this->_S_nomTable = $S_nomTable;
    }

    public function donneIdentifiant()
    {
        return $this->_I_identifiant;
    }

    public function recupererCible()
    {
        return $this->_S_nomTable;
    }

    public function recupererNbEnregistrements()
    {
        $S_requete = 'SELECT count(*) AS nb FROM ' . $this->recupererCible();

        $A_enregistrements = $this->_O_connexion->projeter($S_requete);
        $O_enregistrement = $A_enregistrements[0];

        return $O_enregistrement->nb;
    }
}