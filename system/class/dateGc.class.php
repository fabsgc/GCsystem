<?php
	/*\
	 | ------------------------------------------------------
	 | @file : dateGc.class.php
	 | @author : fab@c++
	 | @description : class g�rant les dates
	 | @version : 2.0 b�ta
	 | ------------------------------------------------------
	\*/
	
	class dateGc{
		private $error              = array() ; //array contenant toutes les erreurs enregistr�es
		
		public  function __construct(){
		}
		
		private function _showError(){
			foreach($this->error as $error){
				$erreur .=$error."<br />";
			}
			return $erreur;
		}
		
		private function _addError($error){
			array_push($this->error, $error);
		}
		
		public  function __desctuct(){
		
		}
	}
?>