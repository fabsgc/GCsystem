{{php: $var='<?php
	namespace entity;

	use \system\Entity\Entity;

	class '.$class.' extends Entity{
		public function setTableDefinition(){
			$this->setTable(\''.$class.'\');
'.$column.'		}
	}';
}}
{$var}