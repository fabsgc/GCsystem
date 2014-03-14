<?php
	/**
	 * @file : routeGc.class.php
	 * @author : fab@c++
	 * @description : class gérant l'url rewriting
	 * @version : 2.2 bêta
	*/
	
	class routerGc{
		use errorGc;								//trait
	
		protected $routes = array();
		
		public function addRoute(routeGc $route){
			if (!in_array($route, $this->routes)){
				$this->routes[] = $route;
			}
		}
		
		public function getRoute($url){
			foreach ($this->routes as $route){
				$url2 = substr($url, strlen(FOLDER), strlen($url));
				if (($varsValues = $route->match($url2)) != false){
					// Si elle a des variables
					if ($route->hasVars()){
						$varsNames = $route->varsNames();
						$listVars = array();
						
						// (clé = nom de la variable, valeur = sa valeur)
						foreach ($varsValues as $key => $match){
							// La première valeur contient entièrement la chaine capturée (voir la doc sur preg_match)
							if ($key !== 0){
								$listVars[$varsNames[$key - 1]] = $match;
							}
						}
						
						$route->setVars($listVars);
					}
					
					return $route;
				}
			}
		}

		public  function __destruct(){
		}
	}
	
	class routeGc{
		use errorGc;								//trait
		
		protected $action;
		protected $module;
		protected $id;
		protected $cache;
		protected $url;
		protected $varsNames;
		protected $vars = array();
		
		public function __construct($url, $module, $action, $id, $cache, $varsNames = array()){
			$this->setUrl($url);
			$this->setModule($module);
			$this->setAction($action);
			$this->setId($id);
			$this->setCache($cache);
			$this->setVarsNames($varsNames);
		}
		
		public function hasVars(){
			return !empty($this->varsNames);
		}
		
		public function match($url){
			if (preg_match('`^'.$this->url.'$`', $url, $matches)){
				return $matches;
			}
			else{
				return false;
			}
		}
		
		public function setAction($action){
			if (is_string($action)){
				$this->action = $action;
			}
		}
		
		public function setId($id){
			if (is_string($id)){
				$this->id = $id;
			}
		}

		public function setCache($cache){
			$this->cache = $cache;
		}
		
		public function setModule($module){
			if (is_string($module)){
				$this->module = $module;
			}
		}
		
		public function setUrl($url){
			if (is_string($url)){
				$this->url = $url;
			}
		}
		
		public function setVarsNames(array $varsNames){
			$this->varsNames = $varsNames;
		}
		
		public function setVars(array $vars){
			$this->vars = $vars;
		}
		
		public function action(){
			return $this->action;
		}
		
		public function id(){
			return $this->id;
		}

		public function cache(){
			return $this->cache;
		}
		
		public function url(){
			return $this->url;
		}
		
		public function module(){
			return $this->module;
		}
		
		public function vars(){
			return $this->vars;
		}
		
		public function varsNames(){
			return $this->varsNames;
		}

		public  function __destruct(){
		}
	}