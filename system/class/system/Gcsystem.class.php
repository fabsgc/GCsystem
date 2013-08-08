<?php
	/**
	 * @file : rubrique.class.php
	 * @author : fab@c++
	 * @description : class mère de l'application
	 * @version : 2.0 bêta
	*/
	
	class Gcsystem{
		use errorGc, langInstance, generalGc, urlRegex, domGc;                            //trait
		/* --- infos d'en tete -- */

		protected $_output                     ;
		protected $_configInstance             ;
		protected $_cronInstance               ;
		protected $_routerInstance             ;
		protected $_routeInstance              ;
		
		protected $_initInstance               ;
		protected $_devTool              = true;

		protected $_cacheRoute           = 0   ;
		protected $_cache                = null;
		
		/* ---------- CONSTRUCTEURS --------- */
		
		public  function __construct($lang=""){
			if($lang==""){ $this->_lang=$this->getLangClient(); } else { $this->_lang=$lang; }
			$this->_createLangInstance();
		}
		
		public function init(){
			if($this->_initInstance == 0){
				$this->_checkHeaderStream($this->getUri());
				$this->_checkEnvironment();
				$this->_checkError();
				$this->_checkFunctionGenerique();
				$this->_checkSecureVar();
				$this->setErrorLog(LOG_HISTORY,'Page rewrite : http://'.$this->getHost().$this->getUri().' rubrique : '.$this->getServerName().$this->getPhpSelf().'?'.$this->getQuery().' / origine : '.$this->getReferer().' / IP : '.$this->getIp());
				$this->_configInstance = new configGc();
				$this->_initInstance = 1;
			}
		}
		
		private function _getRubrique(){
			$this->_routerInstance = new routerGc($this);
			$this->_domXml = new DomDocument('1.0', CHARSET);
			
			if($this->_domXml->load(ROUTE)){
				$this->_addError('Le fichier de route " '.ROUTE.'" a bien été chargé', __FILE__, __LINE__, INFORMATION);
				$this->_nodeXml = $this->_domXml->getElementsByTagName('routes')->item(0);
				$routes = $this->_nodeXml->getElementsByTagName('route');
				
				foreach($routes as $route){
					$vars = array();
					
					if ($route->hasAttribute('vars')){
						$vars = explode(',', $route->getAttribute('vars'));
					}

					$this->_routerInstance->addRoute(new routeGc($route->getAttribute('url'), $route->getAttribute('rubrique'), $route->getAttribute('action'), $route->getAttribute('id'), $route->getAttribute('cache'), $vars));
				}

				if($matchedRoute = $this->_routerInstance->getRoute(preg_replace('`\?'.preg_quote($this->getQuery()).'`isU', '', $this->getUri()))){
					$_GET = array_merge($_GET, $matchedRoute->vars());
					$_GET['rubrique']  = $matchedRoute->module();
					$_GET['action']    = $matchedRoute->action();
					$_GET['pageid']    = $matchedRoute->id();
					$this->_cacheRoute = $matchedRoute->cache();
				}
				else{
					$_GET['rubrique'] = "";
				}
			}
			else{
				$this->_addError('Le routage a échoué car le fichier "'.ROUTE.'" n\'a pas pu être chargé', __FILE__, __LINE__, ERROR);
			}
		}
		
		public function route(){
			if(REWRITE == true){ $this->_getRubrique(); }
			
			if(isset($_GET['rubrique'])){
				$this->_domXml = new DomDocument('1.0', CHARSET);
				
				if($this->_domXml->load(ROUTE)){
					$this->_nodeXml = $this->_domXml->getElementsByTagName('routes')->item(0);
					$this->_markupXml = $this->_nodeXml->getElementsByTagName('route');
					
					$rubrique = ""; //on initialise la variable
					
					foreach($this->_markupXml as $sentence){
						if ($sentence->getAttribute('rubrique') == $_GET['rubrique']){
							$rubrique =  $sentence->getAttribute('rubrique');
						}
					}
					
					if($rubrique!=""){
						$plugin = new pluginGc();
						$this->_cronInstance  = new cronGc(); //les crons ont besoin des plugins

						if($this->_cacheRoute > 0){ //le cache de la page est supérieur à 0 secondes et le rewrite activé
							if($this->_setRubrique($rubrique) == true){  //on inclut les fichiers necéssaire à l'utilisation d'une rubrique
								$class = new $rubrique($this->_lang);
								if(SECURITY == false || $class->setFirewall() == true){
									if(ANTISPAM == false || $class->setAntispam() == true){
										$this->_cache = new cacheGc('page_'.preg_replace('#\/#isU', '-slash-', $this->getUri()), "", $this->_cacheRoute);

										if($this->_cache->isDie()){
											ob_start('ob_gzhandler');
   											register_shutdown_function('ob_end_flush');
												$class->init();
														
												if($_GET['action']!=""){
													if(method_exists($class, 'action'.ucfirst($_GET['action']))){
														$action = 'action'.ucfirst($_GET['action']);
														$class->$action();
														$this->_addError('Appel du contrôleur "action'.ucfirst($_GET['action']).'" de la rubrique "'.$rubrique.'" réussi', __FILE__, __LINE__, INFORMATION);
													}
													else{
														$action = 'actionDefault';
														$class->$action();
														$this->_addError('L\'appel de l\'action "action'.ucfirst($_GET['action']).'" de la rubrique "'.$rubrique.'" a échoué. Appel de l\'action par défaut "actionDefault"', __FILE__, __LINE__, WARNING);
													}
												}
												elseif($_GET['action']==""){
													$_GET['action'] = 'default';
													$action = 'actionDefault';
													$class->$action();
												}

												$class->end();
											$this->_output = ob_get_contents();
											ob_get_clean();

											$this->_cache->setVal($this->_output);
											$this->_cache->setCache();
											$this->_output = $this->_cache->getCache();
										}
										else{
											$this->_output = $this->_cache->getCache();
										}
									}
									else{
									}
								}
								else{

								}
							}
							else{
								$this->_addError('L\'instanciation du contrôleur de la rubrique "'.$rubrique.'" a échoué', __FILE__, __LINE__, ERROR);
								$this->_addError('La rubrique '.$_GET['rubrique'].' n\'a pas été trouvée', __FILE__,  __LINE__, ERROR);
								$this->redirect404();
							}
						}
						else{
							if($this->_setRubrique($rubrique) == true){
								ob_start ();
									$class = new $rubrique($this->_lang);
									if(SECURITY == false || $class->setFirewall() == true){
										if(ANTISPAM == false || $class->setAntispam() == true){
											$class->init();

											if($_GET['action']!=""){
												if(method_exists($class, 'action'.ucfirst($_GET['action']))){
													$action = 'action'.ucfirst($_GET['action']);
													$class->$action();
													$this->_addError('Appel du contrôleur "action'.ucfirst($_GET['action']).'" de la rubrique "'.$rubrique.'" réussi', __FILE__, __LINE__, INFORMATION);
												}
												else{
													$action = 'actionDefault';
													$class->$action();
													$this->_addError('L\'appel de l\'action "action'.ucfirst($_GET['action']).'" de la rubrique "'.$rubrique.'" a échoué. Appel de l\'action par défaut "actionDefault"', __FILE__, __LINE__, WARNING);
												}
											}
											elseif($_GET['action']==""){
												$_GET['action'] = 'default';
												$action = 'actionDefault';
												$class->$action();
											}

											$class->end();
										}
										else{
										}
									}
									else{
									}
								$this->_output = ob_get_contents();
								ob_get_clean();
							}
							else{
								$this->_addError('L\'instanciation du contrôleur de la rubrique "'.$rubrique.'" a échoué', __FILE__, __LINE__, ERROR);
								$this->_addError('La rubrique '.$_GET['rubrique'].' n\'a pas été trouvée', __FILE__,  __LINE__, ERROR);
								$this->redirect404();
							}
						}
					}
					else{
						$this->_addError('La rubrique \'unknow\'car le route a echoué. Requête http : http://'.$this->getHost().$this->getUri(), __FILE__, __LINE__, ERROR);
						$this->_addError('La rubrique '.$_GET['rubrique'].' n\'a pas été trouvée car le route a échoué. URL : http://'.$this->getHost().$this->getUri(), __FILE__,  __LINE__, ERROR);
							$this->redirect404();
					}
				}				
			}
			else{
				if(is_file(RUBRIQUE_PATH.'index'.RUBRIQUE_EXT.'.php') && file_exists(RUBRIQUE_PATH.'index'.RUBRIQUE_EXT.'.php') && is_readable(RUBRIQUE_PATH.'index'.RUBRIQUE_EXT.'.php')){ 
					$this->_setRubrique('index');
				}
				else{
					$this->_addError('La rubrique '.$_GET['rubrique'].' n\'a pas été trouvée', __FILE__,  __LINE__, ERROR);
					$this->redirect404();
				}
			}
		}
		
		private function _setRubrique($rubrique){
			if(file_exists(RUBRIQUE_PATH.$rubrique.RUBRIQUE_EXT.'.php')){
				if(file_exists(MODEL_PATH.$rubrique.MODEL_EXT.'.php')){
					require_once(MODEL_PATH.$rubrique.MODEL_EXT.'.php');
					$this->_addError('Chargement des fichiers "'.RUBRIQUE_PATH.$rubrique.RUBRIQUE_EXT.'.php" et "'.MODEL_PATH.$rubrique.MODEL_EXT.'.php"', __FILE__, __LINE__, INFORMATION);
				}
				require_once(RUBRIQUE_PATH.$rubrique.RUBRIQUE_EXT.'.php');
				return true;
			}
			else{ 
				$this->_addError($this->useLang('rubriquenotfound', array('rubrique' => $rubrique)), __FILE__, __LINE__, ERROR);
				$this->_addError('Echec lors du chargement des fichiers "'.RUBRIQUE_PATH.$rubrique.RUBRIQUE_EXT.'.php" et "'.MODEL_PATH.$rubrique.MODEL_EXT.'.php"', __FILE__, __LINE__, ERROR);
				return false;
			}
		}
		
		public function run(){
			echo $this->_output;
			$this->_addErrorHr();

			if($this->checkContentType() == false){
				$GLOBALS['appDevGc']->setShow(false);
			}
		}

		private function checkContentType(){ //renvoie false si on a pas affaire à du html et si on a une directive content-type
			$header = headers_list();
			
			if(in_array('content-type: text/html', $header)){
				return true;
			}

			foreach ($header as $key => $value) {
				if(preg_match('#content-type#', $value)){
					return false; //on a un content-type qui n'est pas html
				}
			}

			return true;
		}
		
		private function _checkHeaderStream($url){
			$extension = explode('.', $url);
			
			switch($extension[count($extension)-1]){
				case 'html':
					header('Content-Type: text/html; charset='.CHARSET.'');	
					$this->_addError('Content-Type : "Content-Type: text/html; charset='.CHARSET.'"', __FILE__, __LINE__, INFORMATION);
				break;
				
				case 'xml':
					header('Content-Type: text/xml; charset='.CHARSET.'');
					$GLOBALS['appDevGc']->setShow(false);
					$this->_addError('Content-Type : "Content-Type: text/html; charset='.CHARSET.'"', __FILE__, __LINE__, INFORMATION);
				break;
				
				case 'json':
					header('Content-Type: application/json; charset='.CHARSET.'');
					$GLOBALS['appDevGc']->setShow(false);
					$this->_addError('Content-Type : "Content-Type: text/html; charset='.CHARSET.'"', __FILE__, __LINE__, INFORMATION);
				break;
				
				default:
					header('Content-Type: text/html; charset='.CHARSET.'');
					$this->_addError('Content-Type : "Content-Type: text/html; charset='.CHARSET.'"', __FILE__, __LINE__, INFORMATION);
				break;
			}
		}
			
		private function _createLangInstance(){
			$this->_langInstance = new langGc($this->_lang);
		}
		
		private function useLang($sentence, $var = array()){
			return $this->_langInstance->loadSentence($sentence, $var);
		}
		
		private function _checkEnvironment(){
			switch(ENVIRONMENT){	
				case 'development' :		
					error_reporting(E_ALL | E_NOTICE);			
				break;

				case 'production' :	
					error_reporting(0);					
				break;					
			}
		}
		
		private function _checkError(){
			$c = new TestErrorHandling(); 
		}
		
		private function _checkFunctionGenerique(){
			require_once(FUNCTION_GENERIQUE);
		}
		
		private function _checkSecureVar(){
			if(SECUREGET == true && isset($_GET)){
				foreach($_GET as $cle => $val){
					$_GET[$cle] = htmlentities($val);
				}
			}
			
			if(SECUREPOST == true && isset($_POST)){
				foreach($_POST as $cle => $val){
					$_POST[$cle] = htmlentities($val);
				}
			}
		}
		
		public function setMaintenance(){
			$tpl = new templateGC(GCSYSTEM_PATH.'GCmaintenance', 'GCmaintenance', 0, $this->_lang);				
			$tpl->show();
		}

		public  function __destruct(){
		}
	}