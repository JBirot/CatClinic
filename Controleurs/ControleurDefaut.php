<?php

final class ControleurDefaut
{
    public function defautAction()
    {
        if (!Authentification::estConnecte()) {
            BoiteAOutils::redirigerVers('login');
        }
        else {
            // l'utilisateur est connecte
            // c'est soit un admin
            // soit un proprietaire
            // soit un client "normal"...l'affichage va donc varier

			if(Authentification::estAdministrateur())
			{
				BoiteAOutils::redirigerVers('utilisateur');
			}
			else
			{
				BoiteAOutils::redirigerVers('article');
			}
        }
    }
}
