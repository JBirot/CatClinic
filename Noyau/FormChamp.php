1<?php 
 final class FormChamp
 {
 	private $_S_nomDuChamp;
 	private $_S_contenuDuChamp;
 	private $_S_typeDuChamp;
 	private $_B_obligatoire;
 	private $_A_erreurs;
 	
 	public function __construct($S_nomDuChamp, $S_typeDuChamp, $B_obligatoire = false)
 	{
 		$this->_S_nomDuChamp = $S_nomDuChamp;
 		$this->_S_contenuDuChamp = isset($_POST[$S_nomDuChamp]) ? $_POST[$S_nomDuChamp] : null ;
 		$this->_S_typeDuChamp = ucfirst($S_typeDuChamp);
 		$this->_B_obligatoire = $B_obligatoire;
 	}
 	
 	//GETTERS
 	public function donneErreur($B_reset = FALSE)
 	{
 		//si la table des erreurs est 'null' le champ n'a pas été vérifié
 		if(is_null($this->_A_erreurs))
 		{
 			return "Le champ '".$this->_S_nomDuChamp."' n'a pas été vérifié !";
 		}
 		
 		$S_erreur = null;
 		if(count($this->_A_erreurs)>0)
 		{
 			$S_erreur =  str_replace('_', ' ', $this->_S_nomDuChamp)  .
 						'<ul>'.
 							'<li>'.implode("</li><li>", $this->_A_erreurs).'</li>'.
 						'</ul>';
 		}
 		
 		//Si l'option de reset est true, on reset la table d'erreur
 		$this->_A_erreurs = $B_reset ? null : $this->_A_erreurs;
 		
 		return $S_erreur;
 	}
 	
 	public function donneNom()
 	{
 		return $this->_S_nomDuChamp;
 	}
 	
 	public function donneContenu()
 	{
 		return $this->_S_contenuDuChamp;
 	}
 	
 	public function donneType()
 	{
 		return $this->_S_typeDuChamp;
 	}
 	
 	//AUTRE
 	public function estObligatoire()
 	{
 		return $this->_B_obligatoire;
 	}
 	
 	public function estVide()
 	{
 		return (null == $this->_S_contenuDuChamp); 
 	}
 	
 	
 	public function estValide()
 	{
 		$this->_A_erreurs = array();
 		
		if($this->estVide())
		{
			if($this->_B_obligatoire)
			{
				$this->_A_erreurs[0] = 'Le champ est vide.';
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			$S_method = 'tester' . $this->_S_typeDuChamp;
			
			if(method_exists('FormValidation', $S_method))
			{
				$this->_A_erreurs = FormValidation::$S_method($this->_S_contenuDuChamp);
				return empty($this->_A_erreurs);
			}
			else 
			{
				$this->_A_erreurs[9] = $S_method . " n'est pas un type de validation.";
				return false;
			}
		}
 	}
 	

 }
?>
