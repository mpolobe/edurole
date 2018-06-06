<?php
class update {

	public $core;

	function __construct($core){
		$this->core = $core;
	}

	public function updateClient(){

		$server = $this->core->conf['conf']['updates']['server'];

		echo date(" H:i:s ") . " - Getting List of updates<br>";

		$update = file_get_contents($server . "?file=version");
		$files = explode(";",$update);
		$count = count($files);

		echo date(" H:i:s ") . " - A total of <b> $count files </b> need to be updated<br>";

		foreach($files as $filename){
			$url = $server . "?file=". urlencode($filename);
			$content = file_get_contents($url);

			$lfile = ltrim($filename, ".");
			$lfile = ltrim($lfile, "/");

			$localfile = "test/" . $lfile;
			$localdir = dirname($localfile);

			if (!file_exists($localdir)){
				mkdir($localdir, 0777, true);
			}

			$localfile = fopen($localfile, "w");
			fwrite($localfile, $content);
			fclose($localfile);

			echo date(" H:i:s ") . " - Downloading : $filename <br>";
		}
	}
}
?>
