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
 	
 	public function donneTableErreurs($B_reset = FALSE)
 	{
 		//Si l'option est true on reset la table des erreurs
 		$this->_A_erreurs = $B_reset ? null : $this->_A_erreurs;
 		return $this->_A_erreurs;
 	}
 	
 	public function donneErreurs($B_reset = FALSE)
 	{
 		//Si la table des erreurs est vide, le formulaire n'est pas vérifié
 		if(is_null($this->_A_erreurs))
 		{
 			return "Le formulaire n'a pas été vérifié !";	
 		}
 		
 		//Si la table n'est pas vide, on affiche les erreurs
 		$S_erreurs = null;
 		if(count($this->_A_erreurs)>0)
 		{	
 			$S_erreurs = '<ul><li>'.implode('</li><li>', $this->_A_erreurs).'</li></ul>';
 		}
 		
 		//Si l'option est true on reset la table des erreurs
 		$this->_A_erreurs = $B_reset ? null : $this->_A_erreurs;
 		
 		return $S_erreurs;
 	}
 	
 	
 	
 	//VALIDATION
 	public function estValide()
 	{	//On reset les erreurs avant de commencer la validation.
 		$this->_A_erreurs = array();
 		
 		if(null == $this->_A_champs)
 		{
 			$this->_A_erreurs[] = 'Aucun objet champ touvé dans le formulaire';
 			return false;
 		}
 		
 		foreach($this->_A_champs as $O_champ)
 		{
 			if(!$O_champ->estValide())
 			{
 				$this->_A_erreurs[$O_champ->donneNom()] = $O_champ->donneErreur();
 			}
 		}
 		
 		return empty($this->_A_erreurs);
 	}
 	
 }
?>
