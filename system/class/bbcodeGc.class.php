<?php
	/*\
	 | ------------------------------------------------------
	 | @file : bbcodeGc.class.php
	 | @author : fab@c++
	 | @description : class g�rant le parsage des messages et l'affichage d'un �diteur plus ou moins avanc�
	 | @version : 2.0 b�ta
	 | ------------------------------------------------------
	\*/
	
	class bbcodeGc{
		private $contenu                                     ; //contenu � parser
		private $error                    = array()          ; //array contenant toutes les erreurs enregistr�es
		const TAGSTART                    = '['              ; //constante contenant les tag pour la syntaxe
		const TAGSTART2                   = '[/'             ;
		const TAGEND                      = ']'              ;
		const PARSETAGSTART               = '<'              ;
		const PARSETAGSTART2              = '</'             ;
		const PARSETAGEND                 = '>'              ;
		const PARSETAGENDAUTO             = '/>'             ;
		const BBCODE                      = 'bbcode'         ; //r�pertoire contenant les images
		private $lang                                        ; //gestion des langues via des fichiers XML
		private $langInstance                                ; //instance de la class langGc
		private $id                                          ; //id uniq pour la cr�ation d'un formulaire de bbcode
		private $name                                        ; //name pour la cr�ation d'un formulaire de bbcode
		private $preview                  =true              ; //la pr�visualisation est activ�e
		private $previewInstantanee       =true              ; //la pr�visualisation instantan�e est activ�e
		
		private $bbcodeWidth              ='600px'           ; //largeur de l'�diteur
		private $bbcodeHeight             ='250px'           ; //largeur de la zone de texte de l'�diteur
		private $bbcodeBgColor            ='#9d9d9d'         ; //couleur de fond de l'�diteur
		private $bbcodeButton             ='blue'            ; //couleur de la barre d'option et du bouton pr�visualiser.
															   //valeur : button blue red green pinkish maroonish golden brownish 
															   //grayish skinish yellowish goldenish pink violet orange seagreen personalize
		private $bbcodeButtonColor        = array()          ; //dans le cas de couleurs personnalis�es
		
		private $bbCode = array (
			'abbr'   => array ('abbr title=&quot;(.*)&quot;', 'abbr', 'abbr title="$1"', 'abbr', '$2'),
			'quote'  => array ('quote title=&quot;(.*)&quot;', 'quote', 'blockquote title="$1"', 'blockquote', '$2'),
			'sup'    => array ('sup', 'sup', 'sup', 'sup', '$1'),
			'sub'    => array ('sub', 'sub', 'sub', 'sub', '$1'),
			'a'      => array ('a', 'a', 'a href="$1"', 'a', '$1'),
			'a2'     => array ('a url=&quot;(.*)&quot;', 'a', 'a href="$1"', 'a', '$2'),
			'h1'     => array ('h1', 'h1', 'h1', 'h1', '$1'),
			'h2'     => array ('h2', 'h2', 'h2', 'h2', '$1'),
			'h3'     => array ('h3', 'h3', 'h3', 'h3', '$1'),
			'h4'     => array ('h4', 'h4', 'h4', 'h4', '$1'),
			'h5'     => array ('h5', 'h5', 'h5', 'h5', '$1'),
			'h6'     => array ('h6', 'h6', 'h6', 'h6', '$1'),
			'em'     => array ('em', 'em', 'em', 'em', '$1'),
			'img'    => array ('img', 'img', 'img src="$1"', '', ''),
			'audio'  => array ('audio', 'audio', 'audio src="$1" controls="controls"', 'audio', '$1'),
			'ins'    => array ('ins', 'ins', 'ins', 'ins', '$1'),
			'del'    => array ('del', 'del', 'del', 'del', '$1'),
			'dfn'    => array ('dfn', 'dfn', 'dfn', 'dfn', '$1'),
			'strong' => array ('strong', 'strong', 'strong', 'strong', '$1'),
			'pre'    => array ('pre', 'pre', 'pre', 'pre', '$1'),
			'align'  => array ('align val=&quot;(.*)&quot;', 'align', 'span style="display: inline-block; text-align: $1;"', 'span', '$2'),
			'float'  => array ('float val=&quot;(.*)&quot;', 'float', 'span style="float: $1;"', 'span', '$2'),
			'email'  => array ('email', 'email', 'a href="mailto:$1"', 'a', '$1'),
			'color'  => array ('color val=&quot;(.*)&quot;', 'color', 'span style="color: $1;"', 'span', '$1'),
			'taille' => array ('size val=&quot;(.*)&quot;', 'size', 'span style="font-size: $1em;"', 'span', '$1'),
			'police' => array ('font val=&quot;(.*)&quot;', 'font', 'span style="font-family: $1;"', 'span', '$1'),
		);

		private $bbCodeSmiley = array (
			':)'       => array('smile.png', ':)'),
			':D'       => array('heureux.png', ':D'), 
			':p'       => array('langue.png', ':p'),
			':rire:'   => array('rire.png', ':rire:'),
			':euh:'    => array('unsure.png', ':euh' ),
			':('       => array('triste.png', ':('),
			':o'       => array('huh.png', ':o'),
			':colere:' => array('mechant.png', ':colere'),
			'^^'       => array('hihi.png', '^^'),
			':-&deg;'  => array('siffle.png', ':-�'),
			':think:'  => array('think.png', ':think:'),
			':pleure:'     => array('pleure.png', ':pleure:')
		);
		
		private $bbCodeJs = array (
			'abbr'   => array ('abbr title="(.*)"', 'abbr', 'abbr title="$1"', 'abbr', '$2'),
			'quote'  => array ('quote title="(.*)"', 'quote', 'blockquote title="$1"', 'blockquote', '$2'),
			'sup'    => array ('sup', 'sup', 'sup', 'sup', '$1'),
			'sub'    => array ('sub', 'sub', 'sub', 'sub', '$1'),
			'a'      => array ('a', 'a', 'a href="$1"', 'a', '$1'),
			'a2'     => array ('a url="(.*)"', 'a', 'a href="$1"', 'a', '$2'),
			'h1'     => array ('h1', 'h1', 'h1', 'h1', '$1'),
			'h2'     => array ('h2', 'h2', 'h2', 'h2', '$1'),
			'h3'     => array ('h3', 'h3', 'h3', 'h3', '$1'),
			'h4'     => array ('h4', 'h4', 'h4', 'h4', '$1'),
			'h5'     => array ('h5', 'h5', 'h5', 'h5', '$1'),
			'h6'     => array ('h6', 'h6', 'h6', 'h6', '$1'),
			'h6'     => array ('em', 'em', 'em', 'em', '$1'),
			'img'    => array ('img', 'img', 'img src="$1"', '', ''),
			'audio'  => array ('audio', 'audio', 'audio src="$1" controls="controls"', 'audio', '$1'),
			'ins'    => array ('ins', 'ins', 'ins', 'ins', '$1'),
			'del'    => array ('del', 'del', 'del', 'del', '$1'),
			'dfn'    => array ('dfn', 'dfn', 'dfn', 'dfn', '$1'),
			'strong' => array ('strong', 'strong', 'strong', 'strong', '$1'),
			'pre'    => array ('pre', 'pre', 'pre', 'pre', '$1'),
			'align'  => array ('align val="(.*)"', 'align', 'span style="display: inline-block; text-align: $1;"', 'span', '$2'),
			'float'  => array ('float val="(.*)"', 'float', 'span style="float: $1;"', 'span', '$2'),
			'email'  => array ('email', 'email', 'a href="mailto:$1"', 'a', '$1'),
			'color'  => array ('color val="(.*)"', 'color', 'span style="color: $1;"', 'span', '$2'),
			'taille' => array ('size val="(.*)"', 'size', 'span style="font-size: $1em;"', 'span', '$2'),
			'police' => array ('font val="(.*)"', 'font', 'span style="font-family: $1;"', 'span', '$2'),
		);
		
		private $bbCodeS = array ('ul', 'li', 'ol', 'table', 'tr', 'td', 'th');
		
		private $bbCodeSJs = array (
			'ul'    => array('ul'),
			'li'    => array('li'), 
			'ol'    => array('ol'), 
			'table' => array('table'), 
			'tr'    => array('tr'), 
			'td'    => array('td'), 
			'th'    =>  array('th')
		);
		
		private $bbCodeEditor = array (
			'strong' => array ('strong', 'strong', 'gras.png'),
			'em'     => array ('em', 'em', 'italique.png'),
			'ins'    => array ('ins', 'ins', 'souligne.png'),
			'del'    => array ('del', 'del', 'barre.png'),
			'h3'     => array ('h3', 'h3', 'titre1.png'),
			'h4'     => array ('h4', 'h4', 'titre2.png'),
			'a2'     => array ('a url=&quot;&quot;', 'a', 'lien.png'),
			'img'    => array ('img', 'img', 'image.png'),
			'liste'  => array ('ul][li', 'li][/ul', 'liste.png'),
			'tableau'=> array ('table][tr][td', 'td][/tr][/table', 'tab.png'),
			'quote'  => array ('quote title=&quot;&quot;', 'quote', 'citation.png'),
			'email'  => array ('email', 'email', 'email.png'),
			'sup'    => array ('sup', 'sup', 'sup.png'),
			'sub'    => array ('sub', 'sub', 'sub.png'),
			'align'  => array ('align val=&quot;&quot;', 'align', 'align.png'),
			'float'  => array ('float val=&quot;&quot;', 'float', 'float.png'),
			'color'  => array ('color val=&quot;&quot;', 'color', 'color.png'),
			'taille' => array ('size val=&quot;&quot;', 'size', 'sizeup.png'),
			'police' => array ('font val=&quot;&quot;', 'font', 'style.png'),
			'audio'  => array ('audio', 'audio', 'son.png'),
			'video'  => array ('video', 'video', 'video.png'),
			'code'   => array ('code type=&quot;&quot;', 'code', 'code.png'),
		);

		public  function __construct($lang=""){
			require_once(GESHI);
			
			$this->langInstance;
			$this->_createLangInstance();
			if($lang==""){ $this->lang=$this->getLangClient(); } else { $this->lang=$lang; }
		}
		
		public function getLangClient(){
			if(!array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER) || !$_SERVER['HTTP_ACCEPT_LANGUAGE'] ) { return DEFAULTLANG; }
			else{
				$langcode = (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
				$langcode = (!empty($langcode)) ? explode(";", $langcode) : $langcode;
				$langcode = (!empty($langcode['0'])) ? explode(",", $langcode['0']) : $langcode;
				$langcode = (!empty($langcode['0'])) ? explode("-", $langcode['0']) : $langcode;
				return $langcode['0'];
			}
		}
		
		private function _createLangInstance(){
			$this->langInstance = new langGc($this->lang);
		}
			
		public function useLang($sentence){
			return $this->langInstance->loadSentence($sentence);
		}
		
		public function parse($contenu){
			//securite
			$this->contenu = preg_replace(
				'`'.preg_quote(self::TAGSTART).'img'.preg_quote(self::TAGEND).'javascript:(.*)'.preg_quote(self::TAGSTART2).'img'.preg_quote(self::TAGEND).'`isU', 
				'script supprim�', 
				$this->contenu
			);
			$this->contenu = preg_replace(
				'`'.preg_quote(self::TAGSTART).'a'.preg_quote(self::TAGEND).'javascript:(.*)'.preg_quote(self::TAGSTART2).'a'.preg_quote(self::TAGEND).'`isU', 
				'script supprim�', 
				$this->contenu
			);
			$this->contenu = preg_replace(
				'`'.preg_quote(self::TAGSTART).'a url="javascript:(.*)"'.preg_quote(self::TAGEND).'(.*)'.preg_quote(self::TAGSTART2).'a'.preg_quote(self::TAGEND).'`isU', 
				'script supprim�', 
				$this->contenu
			);
			
			$this->contenu = htmlentities($contenu);
			
			foreach($this->bbCode as $cle => $valeur){
				if($valeur[3]!=""){
					$this->contenu = preg_replace(
						'`'.preg_quote(self::TAGSTART).$valeur[0].preg_quote(self::TAGEND).'(.*)'.preg_quote(self::TAGSTART2).$valeur[1].preg_quote(self::TAGEND).'`isU', 
							self::PARSETAGSTART.$valeur[2].self::PARSETAGEND.$valeur[4].self::PARSETAGSTART2.$valeur[3].self::PARSETAGEND.' ', 
							$this->contenu
					);
				}
				else{ //cas des balises auto fermantes
					$this->contenu = preg_replace(
						'`'.preg_quote(self::TAGSTART).$valeur[0].preg_quote(self::TAGEND).'(.*)'.preg_quote(self::TAGSTART2).$valeur[1].preg_quote(self::TAGEND).'`isU', 
							self::PARSETAGSTART.$valeur[2].self::PARSETAGENDAUTO.' ', 
							$this->contenu
					);
				}
			}
			
			foreach($this->bbCodeS as $valeur){
					$this->contenu = preg_replace(
						'`'.preg_quote(self::TAGSTART).$valeur.preg_quote(self::TAGEND).'`isU', 
							self::PARSETAGSTART.$valeur.self::PARSETAGEND, 
							$this->contenu
					);
					
					$this->contenu = preg_replace(
						'`'.preg_quote(self::TAGSTART2).$valeur.preg_quote(self::TAGEND).'`isU', 
							self::PARSETAGSTART2.$valeur.self::PARSETAGEND, 
							$this->contenu
					);

			}
			
			foreach($this->bbCodeSmiley as $cle => $valeur){
				$this->contenu = preg_replace(
					'`'.preg_quote($cle).'`isU', 
						self::PARSETAGSTART.'img src="'.IMG_PATH.'bbcode/'.$valeur[0].'" alt="'.$valeur[1].'" '.self::PARSETAGENDAUTO.' ', 
						$this->contenu
				);
			}
			
			//balise non html
			$this->contenu = preg_replace_callback(
				'`'.preg_quote(self::TAGSTART).'video'.preg_quote(self::TAGEND).'(.*)'.preg_quote(self::TAGSTART2).'video'.preg_quote(self::TAGEND).'`isU', 
				array('bbcodeGc', '_video'), $this->contenu
			);
			$this->contenu = preg_replace_callback(
				'`'.preg_quote(self::TAGSTART).'code type=&quot;(.*)&quot;'.preg_quote(self::TAGEND).'(.*)'.preg_quote(self::TAGSTART2).'code'.preg_quote(self::TAGEND).'`isU', 
				array('bbcodeGc', '_highlight'), $this->contenu
			);
			$this->contenu =  preg_replace_callback(
				'`\s((?:https?|ftp)://\S+?)(?=[]\).,;:!?]?(?:\s|\Z)|\Z)`isU', 
				array('bbcodeGc', '_link'), $this->contenu);
		
			$this->contenu = nl2br($this->contenu);
			$this->contenu = preg_replace('`><br />`isU', '>', $this->contenu);
			return $this->contenu;
		}
		
		public function setContenu($contenu){
			$this->contenu = $this->contenu;
		}
		
		public function setLang($Lang){
			$this->lang=$Lang;
			$this->langInstance->setLang($this->lang);
		} 
		
		public function setPreview($preview){
			$this->preview=$preview;
		} 
		
		public function setInstantane($preview){
			$this->previewInstantanee=$preview;
		} 
		
		private function _highlight($contenu){
			$contenu[1] = html_entity_decode($contenu[1]);
			$contenu[2] = html_entity_decode($contenu[2]);
			
			$code = new GeSHi($contenu[2], $contenu[1]);
			$code->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 2);
			$code->set_line_style('background: #fefefe;', 'background: #f0f0f0;'); 
			$code->enable_keyword_links(false);
			$parse = $code->parse_code();
			return self::PARSETAGSTART.'code'.self::PARSETAGEND.
				self::PARSETAGSTART.'div class="code_nom"'.self::PARSETAGEND.$contenu[1].self::PARSETAGSTART2.'div'.self::PARSETAGEND.
				self::PARSETAGSTART.'div class="code_code"'.self::PARSETAGEND.$parse.self::PARSETAGSTART2.'div'.self::PARSETAGEND.
				self::PARSETAGSTART2.'code'.self::PARSETAGEND;
		}
		
		private function _link($contenu){
			if(strlen($contenu[1])>65){
				return self::PARSETAGSTART.'a href="http://'.substr($contenu[1], 6,strlen($contenu[1])).'"'.self::PARSETAGEND.substr($contenu[1], 0,65).'...'.self::PARSETAGSTART2.'a'.self::PARSETAGEND; 
			}
			else{
				return self::PARSETAGSTART.'a href="http://'.substr($contenu[1], 6,strlen($contenu[1])).'"'.self::PARSETAGEND.$contenu[1].self::PARSETAGSTART2.'a'.self::PARSETAGEND;
			}
		}
		
		private function _video($contenu){
			$contenu[1] = html_entity_decode($contenu[1]);
			$contenu[1] =  preg_replace('#&feature=related#isU', '', $contenu[1]);	
			
			if(preg_match('#youtube#isU',$contenu[1])){
				if(preg_match('#www#isU',$contenu[1])){	
					if(!preg_match('#gl=#isU',$contenu[1])){
						$resultat =  preg_replace('#http://www.youtube.com/watch\?v=(.+)#isU', '$1', $contenu[1]);
					}
					else{
						$resultat =  preg_replace('#http://www.youtube.com/watch\?gl=(.+)&v=(.+)#isU', '$2', $contenu[1]);
					}	
				}
				else{
					if(!preg_match('#gl=#isU',$contenu[1])){
						$resultat =  preg_replace('#http://youtube.com/watch\?v=(.+)#isU', '$1', $contenu[1]);
					}
					else{
						$resultat =  preg_replace('#http://youtube.com/watch\?gl=(.+)v=(.+)#isU', '$2', $contenu[1]);
					}
				}
				return '<object width="500" height="375" type="application/x-shockwave-flash" data="http://www.youtube.com/v/'.$resultat.'">
							<param name="movie" value="http://www.youtube.com/v/'.$resultat.'"><param name="allowFullScreen" value="true"><param name="wmode" value="transparent">
						</object>';
			}
			elseif(preg_match('#dailymotion#isU',$contenu[1])){
				if(preg_match('#www#isU',$contenu[1])){
					$resultat =  preg_replace('#http://www.dailymotion.com/video/(.+)#isU', 'http://www.dailymotion.com/swf/video/$1', $contenu[1]);
				}
				else{
					$resultat =  preg_replace('#http://dailymotion.com/video/(.+)#isU', 'http://www.dailymotion.com/swf/video/$1', $contenu[1]);
				}
				return '<object width="500" height="375" type="application/x-shockwave-flash" data="'.$resultat.'"><param name="movie" value="'.$resultat.'">
							<param name="allowFullScreen" value="true"><param name="wmode" value="transparent">
						</object>';
			}
			elseif(preg_match('#vimeo#isU',$contenu[1])){
				if(preg_match('#www#isU',$contenu[1])){
					$resultat =  preg_replace('#http://www.vimeo.com/(.+)#', 'http://www.vimeo.com/moogaloop.swf?clip_id=$1&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1', $contenu[1]);
				}
				else{
					$resultat =  preg_replace('#http://vimeo.com/(.+)#', 'http://www.vimeo.com/moogaloop.swf?clip_id=$1&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1', $contenu[1]);
				}
				return '<object width="500" height="375" type="application/x-shockwave-flash" data="'.$resultat.'">
							<param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" />
							<param name="movie" value="'.$resultat.'" /><param name="wmode" value="transparent" />
						</object>';
			}
			else{
				return '<video controls="" width="500" src="'.$contenu[1].'" controls="controls"></video>';
			}
		}
		
		public function editor($contenu, $option = array()){
			$this->id = 'id_'.uniqid();
			$this->name = 'name_'.uniqid();
			
			foreach($option as $cle=>$info){
				switch($cle){
					case 'width':
						$this->bbcodeWidth = $info;
					break;
					
					case 'height':
						$this->bbcodeHeight = $info;
					break;
					
					case 'bgcolor':
						$this->bbcodeBgColor = $info;
					break;
					
					case 'id':
						$this->id = $info;
					break;
					
					case 'name':
						$this->name = $info;
					break;
					
					case 'theme':
						$this->bbcodeButton = $info;
					break;
					
					case 'preview':
						$this->preview = $info;
					break;
					
					case 'instantane':
						$this->previewInstantanee = $info;
					break;
					
					case 'color' :
						$this->bbcodeButtonColor = $info;
					break;
				}
			}
			
			$tpl = new templateGC('GCbbcodeEditor', 'GCbbcodeEditor', 0, $this->lang);
			$tpl->assign(array(
				'message' => $contenu,
				'preview' => $this->preview,
				'instantane' => $this->previewInstantanee,
				'id' => $this->id,
				'name' => $this->name,
				'width' => $this->bbcodeWidth,
				'Twidth' => $this->bbcodeWidth,
				'height' => $this->bbcodeHeight,
				'bgcolor' => $this->bbcodeBgColor,
				'theme' => $this->bbcodeButton,
				'smiley' => $this->bbCodeSmiley,
				'bbcode' => $this->bbCodeJs,
				'imgpath' => IMG_PATH,
				'bbCodeS' => $this->bbCodeSJs,
				'bbCodeEditor' => $this->bbCodeEditor,
				'color' => $this->bbcodeButtonColor
			));
			$tpl->show();
		}
		
		private function _addError($error){
			array_push($this->error, $error);
		}
		
		private function _showError(){
			foreach($this->error as $error){
				$erreur .=$error."<br />";
			}
			return $erreur;
		}
		
		public  function __desctuct(){
		
		}
	}
?>