<?php
final class ControleurTest
{
	public function __construct()
	{
		Authentification::accesAdministrateur();
	}
	
	public function defautAction()
	{
		Vue::montrer('standard/testformvalidation');
	}
	
	public function validationAction()
	{
		$O_formulaire = new Formulaire(array(
			"login" => 'login',
			"mot_de_passe"=>'pwd',
			"mail" =>"mail",
			"date" =>"date",
			"time" => "time",
			"datetime"=>"datetime",
			"nom"=>"nom",
			"id"=>"id"		
		));
		if(!$O_formulaire->estValide())
		{
			BoiteAOutils::stockerErreur($O_formulaire->donneErreurs());
			BoiteAOutils::stockerMessage(implode('<br>', $O_formulaire->donneContenus()));
			BoiteAOutils::redirigerVers('Test');
			return false;
		}
		else
		{
			BoiteAOutils::stockerMessage('Ca passe!');
			BoiteAOutils::redirigerVers('Test');
			return true;
		} 
	}
	
	public function gestionAction()
	{
		$O_articleControleur = new ControleurArticle();
		$O_articleControleur->listeAction(array(1));
	}
}