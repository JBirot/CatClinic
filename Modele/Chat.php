<?php

final class Chat extends ObjetMetier
{
    private $_I_identifiant;

    private $_I_age;

    private $_S_tatouage;

    private $_S_nom;

    // Mes mutateurs (setters)
    public function changeIdentifiant ($S_identifiant)
    {
        $this->_I_identifiant = $S_identifiant;
    }

    public function changeAge ($I_age)
    {
        $this->_I_age = $I_age;
    }

    public function changeNom ($S_nom)
    {
        $this->_S_nom = $S_nom;
    }

    public function changeTatouage ($S_tatouage)
    {
        $this->_S_tatouage = $S_tatouage;
    }

    // Mes accesseurs (getters)

    public function donneIdentifiant ()
    {
        return $this->_I_identifiant;
    }

    public function donneNom ()
    {
        return $this->_S_nom;
    }

    public function donneAge ()
    {
        return $this->_I_age;
    }

    public function donneTatouage ()
    {
        return $this->_S_tatouage;
    }
}