<?php
class files {

	public $core;
	public $view;
	public $filename;

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

		include $this->core->conf['conf']['classPath'] . "files.inc.php";

		$this->filename = $this->core->cleanGet['filename'];
	}

	function personalFiles($path) {

		$path = $this->core->conf['conf']['dataStorePath'] . $this->username;

		echo	'<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/files/upload/?path=' . urlencode($path) . '"><img src="' . $this->core->fullTemplatePath . '/images/up.png"/>  upload a file</a>
			<a href="' . $this->core->conf['conf']['path'] . '/files/new/?path=' . urlencode($path) . '"><img src="' . $this->core->fullTemplatePath . '/images/list.gif"/> create an empty file</a>
			<a href="' . $this->core->conf['conf']['path'] . '/files/makedir/?path=' . urlencode($path) . '"><img src="' . $this->core->fullTemplatePath . '/images/new.gif"/> new directory</a>
			</div>';
		
		$files = new filescontroller($this->core);
		$files->overview($path);
	}

	function uploadFiles() {
		echo '<div class="heading">File upload</div>
		<form id="login" name="login" method="get" action="/files/uploadfile" enctype="multipart/form-data">
		<input type="hidden" name="id" value="view-information">
		<div class="label">Select file to upload </div>
		<input type="file" name="file" id="file" class="submit"><br>
		<div class="label"> </div>
		<input type="submit" class="submit" value="Upload file" />
		</form>';

	}

	function downloadFiles($item) {

		$home = getcwd();
		$path = $home . "/datastore/userhomes/" . $this->core->username . "/";
		$path = str_replace("//", "/", $path);
		$file = $path . $this->filename;

		if (file_exists($file)) {

			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);
			exit;
		}
	}

	function newFiles() {
		echo '<div class="heading">New file</div>
	        <form id="login" name="login" method="get" action="/files/uploadfile" enctype="multipart/form-data">
	        <input type="hidden" name="id" value="view-information">
	        <div class="label">Select file to upload </div>
	        <input type="file" name="file" id="file" class="submit"><br>
	        <div class="label"> </div>
	        <input type="submit" class="submit" value="Upload file" />
	        </form>';
	}
}

?>
