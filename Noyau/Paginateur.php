<?php

final class Paginateur implements PaginateurInterface {

    private $_O_listeur;

    private $_I_limite;

    private $_I_pageCourante;

    public function __construct (ListeurInterface $O_listeur) {
        $this->_O_listeur = $O_listeur;
    }

    public function changeListeur (ListeurInterface $O_listeur) {
        $this->_O_listeur = $O_listeur;
    }

    public function changeLimite ($I_limite)
    {
        $this->_I_limite = $I_limite;
    }

    public function paginer ()
    {
        $I_nbEnregistrements = $this->_O_listeur->recupererNbEnregistrements();

        $I_nbPages = ceil($I_nbEnregistrements / $this->_I_limite);

        $S_controleurCible = $this->_O_listeur->recupererCible();
    
        $A_pagination = null;

        if ($I_nbPages > 1)
        {
            for ($i=1; $i <= $I_nbPages; $i++)
            {
                $A_pagination[$i] = null;

                if ($this->_I_pageCourante != $i)
                {
                    $A_pagination[$i] = $S_controleurCible . '/liste/' . $i;
                }
            }
        }

        return $A_pagination;
    }

    public function recupererPage ($I_numeroPage)
    {
        if ($I_numeroPage <= 0)
        {
            throw new InvalidArgumentException('Le numéro de page ' . $I_numeroPage . ' est invalide');
        }
        
        if ($I_numeroPage > $this->nbPages() && $I_numeroPage != 1)
        {
        	$I_numeroPage = $this->nbPages() == 0 ? 1 : $this->nbPages();
        }

        $this->_I_pageCourante = $I_numeroPage;
	
        $I_indexDebut = $I_numeroPage == 1 ? 0 : (($I_numeroPage - 1) * $this->_I_limite);
		
        return $this->_O_listeur->lister($I_indexDebut, $this->_I_limite);
    }
    
    public function nbPages()
    {
    	$I_nbEnregistrements = $this->_O_listeur->recupererNbEnregistrements();
    	return ($I_nbEnregistrements/$this->_I_limite); 
    }

}