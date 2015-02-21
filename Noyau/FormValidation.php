<?php
/*
 * FormValidation.php
 * Created on 19 ao�t 2014
 */
 
 final class FormValidation
 {
	public static function testerTexte($contenuDuChamp)
	{
		$A_erreurs = array();
		return $A_erreurs;
	}
	
	public static function testerLogin($contenuDuChamp)
	{
		$A_erreurs = array();
		
		if(strlen($contenuDuChamp)<4 || strlen($contenuDuChamp)>24)
		{
			$A_erreurs[2] = 'Un identifiant doit comporter entre 4 et 24 caractères.';
		}
		$pattern = "/^([a-zA-Z0-9]{4,24})$/";
		if (!preg_match($pattern, $contenuDuChamp))
		{
			$A_erreurs[1] = "Un identifiant ne peut comporter que des lettres et des chiffres.";
		}
		return $A_erreurs;
	}
	
	public static function testerMail($contenuDuChamp)
	{
		$A_erreurs = array();
		if(!filter_var($contenuDuChamp,FILTER_VALIDATE_EMAIL))
		{
			$A_erreurs[1] = 'Le champ doit être une adresse mail valide : Exemple@mail.com';
		}
		return $A_erreurs;
	}
	
	public static function testerPwd($contenuDuChamp)
	{
		$A_erreurs = array();
		if(strlen($contenuDuChamp)<6 || strlen($contenuDuChamp)>24)
		{
			$A_erreurs[2] = 'Un mot de passe doit contenir entre 6 et 24 caractères.';
		}
		return $A_erreurs;
	}
	
	public static function testerDate($contenuDuChamp)
	{
		$A_erreurs = array();
		$pattern = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/";
		if(!preg_match($pattern, $contenuDuChamp))
		{
			$A_erreurs[1] = 'La date doit être au format yyyy-mm-dd.';
		}
		return $A_erreurs;
	}
	
	public static function testerTime($contenuDuChamp)
	{
		$A_erreurs = array();
		$pattern = "/^([0-1][0-9]|2[0-4]):[0-5][0-9](:[0-9]{2}.[0-9]{2})?$/";
		if(!preg_match($pattern, $contenuDuChamp))
		{
			$A_erreurs[1] = "L'heure doit être au format hh:mm ou hh:mm:ss.ss";
		}
		return $A_erreurs;
	}
	
	public static function testerDateTime($contenuDuChamp)
	{
		$A_erreurs = array();
		$pattern = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-T([0-1][0-9]|2[0-4]):[0-5][0-9](:[0-9]{2}.?[0-9]{0-2})?Z$/";
		$pattern2="/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([0-1][0-9]|2[0-4]):[0-5][0-9]$/";
		if(!preg_match($pattern, $contenuDuChamp)&&!preg_match($pattern2, $contenuDuChamp))
		{
			$A_erreurs[1] = "La date doit être au format yyyy-mm-dd hh:mm.";
		}
		return $A_erreurs;
	}
	
	public static function testerNom($contenuDuChamp)
	{
		$A_erreurs = array();
		if (strlen($contenuDuChamp)<3 || strlen($contenuDuChamp)>24)
		{
			$A_erreurs[2] = 'Un nom doit comporter entre 3 et 24 lettres.';
		}
		$pattern = "/^[a-zA-Z][a-zA-Z ]*[a-zA-Z]$/";
		if(!preg_match($pattern, $contenuDuChamp))
		{
			$A_erreurs[1] = 'Un nom ne peut contenir que des lettres ou des espaces.';
		}
		return $A_erreurs;
	}
	public static function testerId($contenuDuChamp)
	{
		$A_erreurs = array();
		if(filter_var($contenuDuChamp,FILTER_VALIDATE_INT,array('options' => array('min_range'=>0))) === false)
		{
			$A_erreurs[1] = 'Le champ doit être un entier positif.';
		}
		return $A_erreurs;
	}
	public static function testerPrix($contenuDuChamp)
	{
		$A_erreurs = array();
		$pattern = "/^[0-9]*(,|.)?[0-9]{0,2}$/";
		if (!preg_match($pattern, $contenuDuChamp))
		{
			$A_erreurs[1] = "Le prix doit être au format XX,XX";
		}
		return $A_erreurs;
	}
 }
?>
