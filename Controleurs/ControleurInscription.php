<?php
final class ControleurInscription
{
	public function __construct()
	{
		Authentification::accesNonConnecte();	
	}
	
	public function defautAction()
	{
		Vue::montrer('inscription/form');
	}
}