<?php

class Listeur implements ListeurInterface {

    protected $_O_mapper;

    public function __construct($O_mapper)
    {
        $this->_O_mapper = $O_mapper;
    }

    public function recupererNbEnregistrements() {
        return $this->_O_mapper->recupererNbEnregistrements();
    }
    
    public function lister ($I_debut = null, $I_fin = null, $A_ordre = NULL)
    {
   		return $this->_O_mapper->trouverParIntervalle($I_debut, $I_fin, $A_ordre);
    }
    
    public function recupererCible()
    {
    	return $this->_O_mapper->recupererCible();
    }
}