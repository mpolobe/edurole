<?php
class journals {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}
	private function insertJournal($jid,$aid,$doi,$title,$simplePara,$author,$copyright,$pii){
		
		$sql = "INSERT INTO `journals` (`jid`, `aid`, `doi`, `title`, `simplePara`, `author`, `copyright`,`pii`) 
		VALUES ('$jid','$aid','$doi','$title','$simplePara','$author','$copyright','$pii');";
		
		$run = $this->core->database->doInsertQuery($sql);
		
		return;
	}
	private function processJournals($file){
		
		$jid="";$aid="";$doi="";$title="";$affiliation="";$simplePara="";$author="";$copyright="";$pii="";
		
		
		
		$filename = $file;
		$file = file_get_contents($file);

		$file = preg_replace('~(</?|\s)([a-z0-9_]+):~is', '$1$2_', $file);
		
		$content = simplexml_load_string($file);
		
		
		$item="item-info";
		
		foreach ($content->$item->children() as $key3=>$value3){
			if($key3 == "ce_doi"){
				$doi = $value3;
				echo $doi .' doi </br>' ;
			
			}
			if($key3 == "aid"){
				$aid = $value3;
				echo $aid .'  aid</br>' ;
			
			}
			if($key3 == "ce_pii"){
				$pii = $value3;
				echo $pii .' pii </br>' ;
			
			}
			if($key3 == "ce_copyright"){
				$copyright = $value3;
				echo $copyright .' copyright </br>' ;
			
			}
			if($key3 == "jid"){
				$jid = $value3;
				echo $jid .' jid </br>' ;
			
			}
			
		}
		
		//var_dump($content);
		
		foreach ($content->head->children() as $key=>$value){
			if($key == "ce_title"){
				$title = $value;
				echo $title.' title </br>' ;
			
			}
			
			if($key == "ce_dochead"){
				
				foreach ($content->head->$key->children() as $key2=>$value2) {
					if($key2 == "ce_textfn"){
						$textfn = $value2;
						//echo $textfn .' ce_textfn </br>' ;
					}
				
				}
			
			}
			
			if($key == "ce_author-group"){
				$name ="";
				foreach ($content->head->$key->children() as $key2=>$value2) {
					
					if($key2 == "ce_author"){
						
						foreach ($content->head->$key->$key2->children() as $key3=>$value3) {
							
							if($key3 == "ce_given-name"){
								if (!empty($value3)||$value3!="")
								{
									$name =$value3;
									//echo $textfn .' ce_author </br></br>' ;
								}
							}
							if($key3 == "ce_surname"){
								
								if (!empty($value3)||$value3!="")
								{
									$name .= ' '.$value3;
								}
							}
							//var_dump($key3);
							//echo 'author ['.$name.']  </br></br>' ;
							
						}
						
					}/*
					if($key == "affiliation"){
				
						foreach ($content->head->$key->$key2->children() as $key3=>$value3) {
							if($key3 == "ce_textfn"){
								$affiliation = $value3;
								echo $affiliation .' affiliation </br>' ;
							}
						
						}
					}
					*/
				
				}
				$author=$name;
				echo 'author ['.$author.']  </br></br>' ;
				
				
			
			}
			
			if($key == "ce_abstract"){
				foreach ($content->head->$key->children() as $key2=>$value2) {
					
					if($key2 == "ce_abstract-sec"){
						
						foreach ($content->head->$key->$key2->children() as $key3=>$value3) {
							
							if($key3 == "ce_simple-para"){
								if (!empty($value3)||$value3!="")
								{
									$simplePara =$value3;
									//echo $textfn .' ce_author </br></br>' ;
								}
							}
														
						}
						
					}
				
				}
				echo 'abstract ['.$simplePara.']  </br></br>' ;
			
			}
			
		}
		
		$this->insertJournal($jid,$aid,$doi,$title,$simplePara,$author,$copyright,$pii);
		
		
		
		return;
	}

	public function importJournals(){
		$dir = $this->core->conf['conf']['dataStorePath'] . 'journals/';


		$di = new RecursiveDirectoryIterator($dir);
		foreach (new RecursiveIteratorIterator($di) as $filename => $file) {

			if(is_dir($file)){
				continue;
			} else if (basename($file) == "main.xml"){
				//echo $di;
				$this->processJournals($file);
			}
		}
		//echo $di;
	}
}
?>
