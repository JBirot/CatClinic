<?php 
 final class FormChamp
 {
 	private $_S_nomDuChamp;
 	private $_S_contenuDuChamp;
 	private $_S_typeDuChamp;
 	private $_B_obligatoire;
 	private $_S_erreur;
 	
 	public function __construct($S_nomDuChamp, $S_typeDuChamp, $B_obligatoire = false)
 	{
 		$this->_S_nomDuChamp = $S_nomDuChamp;
 		$this->_S_contenuDuChamp = isset($_POST[$S_nomDuChamp]) ? $_POST[$S_nomDuChamp] : null ;
 		$this->_S_typeDuChamp = ucfirst($S_typeDuChamp);
 		$this->_B_obligatoire = $B_obligatoire;
 	}
 	
 	//GETTERS
 	public function donneErreur()
 	{
 		if(!null == $this->_S_erreur)
 		{
 			return $this->_S_nomDuChamp . ' : ' . $this->_S_erreur;
 		}
 		else
 		{
 			return null;
 		}
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
 		$this->_S_erreur = null;
 		
		if($this->estVide())
		{
			if($this->_B_obligatoire)
			{
				$this->_S_erreur = 'Le champ est vide.';
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			try
			{
				$S_fonction = 'tester' . $this->_S_typeDuChamp;
				
				//TODO : verifier que la méthode existe
				if(!FormValidation::$S_fonction($this->_S_contenuDuChamp))
				{
					$this->_S_erreur = "Le champ est invalide.";
					return false;
				}
				else
				{
					return true;
				}
			}
			catch(Exception $O_exception)
			{
				$this->_S_erreur = 'Une erreur est survenue pendant la tentative de validation.';
				return false;
			}
			
			return false;
		}
 	}
 	

 }
?>
