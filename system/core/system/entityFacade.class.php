<?php
	/*\
	 | ------------------------------------------------------
	 | @file : entityFace.class.php
	 | @author : fab@c++
	 | @description : easier way to instanciate entities
	 | @version : 3.0 Bêta
	 | ------------------------------------------------------
	\*/
	
	namespace system{
		class entityFacade{
			use error, facades, langInstance;

			protected $bdd;

			/**
			 * Constructor
			 * @access public
			 * @param &$profiler \system\profiler
			 * @param &$config \system\config
			 * @param &$request \system\request
			 * @param &$response \system\response
			 * @param $lang string
			 * @param $bdd pdo
			 * @since 3.0
 			 * @package system
			*/

			final public function __construct(&$profiler, &$config, &$request, &$response, $lang, $bdd){
				$this->profiler = $profiler;
				$this->config   =   $config;
				$this->request  =  $request;
				$this->response = $response;
				$this->lang     =     $lang;
				$this->bdd      =      $bdd;
			}

			/**
			 * instantiate the good entity
			 * @access public
			 * @param $name string
			 * @param $arguments array
			 * @return entity
			 * @throws exception
			 * @since 3.0
 			 * @package system
			*/
			
			public function __call($name, $arguments){
				if(file_exists(APP_RESOURCE_ENTITY_PATH.$name.EXT_ENTITY.'.php')){
					include_once(APP_RESOURCE_ENTITY_PATH.$name.EXT_ENTITY.'.php');

					$class = '\entity\\'.$name;

					$params = array(
						&$this->profiler, 
						&$this->config  , 
						&$this->request ,
						&$this->response ,
						$this->lang,
						$this->bdd
					);

					foreach ($arguments as $key => $value) {
						array_push($params, $value);
					}

					$reflect  = new \ReflectionClass($class);
					return $reflect->newInstanceArgs($params);
				}
				else{
					$file = '';
					$line = '';
					$stack = debug_backtrace(0);
					$trace = $this->getStackTraceFacade($stack);

					foreach ($trace as $value) {
						if($value['function'] == $name){
							$file = $value['file'];
							$line = $value['line'];
							break;
						}
					}

					throw new exception('undefined entity "'.$name.'" in "'.$file.'" line '.$line, 1);
				}
			}

			/**
			 * Destructor
			 * @access public
			 * @return void
			 * @since 3.0
 			 * @package system
			*/

			public function __destruct(){
			}
		}
	}