<?php
class update {

	public $core;

	function __construct($core){
		$this->core = $core;
	}

	public function updateClient(){

		$this->core->conf['updates']['server'] = "http://edurole.com/update/";

		$server = $this->core->conf['updates']['server'];

		echo date(" H:i:s ") . " - Getting List of updates<br>";

		$update = file_get_contents($server . "?file=version");
		$files = explode(";",$update);
		$count = count($files);

		echo date(" H:i:s ") . " - A total of $count files need to be updated<br>";

		foreach($files as $filename){

			$url = urlencode($server . "?file=$filename");
			$content = file_get_contents($url);

			$localfile = fopen("$filename", "w");

			fwrite($localfile, $content);
			fclose($localfile);
	
			echo date(" H:i:s ") . " - Downloading : $filename<br>";

		}
	}
}
?>
