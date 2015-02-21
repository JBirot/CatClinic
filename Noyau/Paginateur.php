<?php

final class Paginateur implements PaginateurInterface {

    private $_O_listeur;

    private $_I_limite;
    
    private $_A_ordre;

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
    
    public function changeOrdre (array $A_ordre = NULL)
    {
    	$this->_A_ordre = $A_ordre;
    }

    public function paginer ($S_lien = "liste")
    {
        $I_nbPages = $this->nbPages();

        $S_controleurCible = $this->_O_listeur->recupererCible();
    
        $A_pagination = null;
        
        if ($I_nbPages > 1)
        {
        	$I_depart = $this->_I_pageCourante - 2;
        	$I_depart = $I_depart <= 0 ? 1: $I_depart;
        	$I_depart = ($I_depart > $I_nbPages-4)&&($I_nbPages>5)?$I_nbPages-4:$I_depart;
			if($I_depart > 1)
	        {
	        	$A_pagination['<<...'] = $S_controleurCible . '/'.$S_lien.'/1';
	        }
	        $I_fin = $I_depart + 4 < $I_nbPages?$I_depart+4:$I_nbPages;
            
            for ($i=$I_depart; $i <= $I_fin; $i++)
            {
                $A_pagination[$i] = null;
				
                if ($this->_I_pageCourante != $i)
                {
                    $A_pagination[$i] = $S_controleurCible . '/'.$S_lien.'/' . $i;
                }
            }
            if($I_depart< $I_nbPages-4)
            {
            	$A_pagination['...>>'] = $S_controleurCible . '/'.$S_lien.'/' . $I_nbPages;
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
        
        return $this->_O_listeur->lister($I_indexDebut, $this->_I_limite,$this->_A_ordre);
    }
    
    public function nbPages()
    {
    	$I_nbEnregistrements = $this->_O_listeur->recupererNbEnregistrements();
    	return ceil($I_nbEnregistrements/$this->_I_limite); 
    }

}