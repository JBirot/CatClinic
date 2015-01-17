<?php
/*
 * Formulaire.php
 * Created on 19 ao�t 2014
 */
 
 final class Formulaire
 {
 	private $_A_champs;
 	private $_A_erreurs;
 	
 	public function __construct(array $A_listeDesChampsObligatoires = null, array $A_listeDesChamps = null)
 	{
 		//On récupère les champs obligatoires
 		if(!null == $A_listeDesChampsObligatoires)
 		{
 			foreach($A_listeDesChampsObligatoires as $S_nomDuChamp => $S_type)
 			{
 				$this->_A_champs[$S_nomDuChamp] = new FormChamp($S_nomDuChamp, $S_type, true);
 			}
 		}
 		//On récupère les autres champs
 		if(!null == $A_listeDesChamps)
 		{
 			foreach($A_listeDesChamps as $S_nomDuChamp => $S_type)
 			{
 				$this->_A_champs[$S_nomDuChamp] = new FormChamp($S_nomDuChamp, $S_type, false);
 			}
 		}	
 	}
 	
 	//GETTERS
 	public function donneContenu($S_nomDuChamp)
 	{
 		return $this->_A_champs[$S_nomDuChamp]->donneContenu();
 	}
 	
 	public function donneContenus()
 	{
 		$A_contenus = null;
 		foreach($this->_A_champs as $O_champ)
 		{
 			$A_contenus[$O_champ->donneNom()] = $O_champ->donneContenu();
 		}
 		return $A_contenus;
 	}
 	
 	public function donneErreurChamp($S_nomDuChamp)
 	{
 		return $this->_A_champs[$S_nomDuChamp]->donneErreur();
 	}
 	
 	public function donneTableErreurs()
 	{
 		return $this->_A_erreurs;
 	}
 	
 	public function donneErreurs()
 	{
 		$S_erreurs = null;
 		
 		//TODO : explode du tableau d'erreurs pour en faire une chaîne de caractères.
 		foreach($this->_A_erreurs as $S_erreur)
 		{
 			$S_erreurs .=  $S_erreur . "</br>";
 		}
 		
 		return $S_erreurs;
 	}
 	
 	
 	
 	//VALIDATION
 	public function estValide()
 	{
 		//TODO : sauvegarde des informations valides pour éviter la répétition de la saisie.
 		//On reset les erreurs avant de commencer la validation.
 		$this->_A_erreurs = null;
 		
 		if(null == $this->_A_champs)
 		{
 			$this->_A_erreurs[] = 'Aucun objet champ touvé dans le formulaire';
 			return false;
 		}
 		
 		foreach($this->_A_champs as $O_champ)
 		{
 			if(!$O_champ->estValide()&&$O_champ->estObligatoire())
 			{
 				$this->_A_erreurs[$O_champ->donneNom()] = $O_champ->donneErreur();
 			}
 		}
 		
 		if(null == $this->_A_erreurs)
 		{
 			return true;
 		}
 		
 		return false;
 	}
 	
 }
?>
