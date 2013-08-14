<?php
class filemanager {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->javascript = array(3);
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {

		$this->core = $core;

		include "includes/classes/files.inc.php";

		$path = getcwd() . "/datastore/userhomes/" . $this->username;
		$filename = $this->core->cleanGet['edi'];
		$current = $this->core->cleanGet['op'];

		if ($this->core->cleanGet["action"] == "overview" || !isset($this->core->cleanGet["action"])) {

			$this->viewPersonalFiles($path);

		} elseif ($this->core->cleanGet["action"] == "edit") {

			$function = __FUNCTION__;
			echo breadcrumb::generate(get_class(), $function);

			echo '<div class="contentpadfull">
			<p class="title2">Editing ' . $filename . '</p> <p><b>Remember to click save</b>';

			editFile($filename);

		} elseif ($this->core->cleanGet["action"] == "saveFile") {

			saveFile($path);

		} elseif ($this->core->cleanGet["action"] == "rename") {

			$rename = renameFile($path);

			if ($rename) {
				viewPersonalFiles($path);
			}

		} elseif ($this->core->cleanGet["action"] == "delete") {

			deleteFile($path);

		} elseif ($this->core->cleanGet["action"] == "new") {

			newFileForm();

		} elseif ($this->core->cleanGet["action"] == "upload") {

			upload();


		} elseif ($this->core->cleanGet["action"] == "uploadfile") {

			uploadFile();

		} elseif ($this->core->cleanGet["action"] == "newFile") {

			newFile($path);

		}
	}

	function viewPersonalFiles($path) {
		$function = __FUNCTION__;
		echo breadcrumb::generate(get_class(), $function);

		echo '<div class="contentpadfull">
		<p class="title2">Overview of personal files</p> <p><b>This directory lists your personal files <img src="templates/edurole/images/up.png"/> <a href="?id=files&action=upload&op=' . $current . '">upload a file</a> or <img src="templates/edurole/images/dd.gif"/> <a href="?id=files&action=new&op=' . $current . '">create an empty file</a> or <img src="templates/edurole/images/new.gif"/> <a href="?id=files&action=newdir&op=' . $current . '">new directory</a></b>';

		overview($path);
	}

	function upload() {
		$function = __FUNCTION__;
		echo breadcrumb::generate(get_class(), $function);

		echo '<div class="contentpadfull">
		<p class="title2">Upload a file</p> <p><b>Please note that executables must be compressed and the maximum file size is 50MB</b>
	
		<div class="heading">File upload</div>

		<form id="login" name="login" method="get" action="?id=files&action=uploadfile" enctype="multipart/form-data">
		<input type="hidden" name="id" value="view-information">
		<div class="label">Select file to upload </div>
		<input type="file" name="file" id="file" class="submit"><br>
			<div class="label"> </div>
		<input type="submit" class="submit" value="Upload file" />
		</form>';

	}

	function downloadFile($filename) {

		$home = getcwd();
		$path = $home . "/datastore/userhomes/" . $this->username . "/";
		$path = str_replace("//", "/", $path);
		$file = $path . $filename;

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

	function newFileForm() {
		$function = __FUNCTION__;
		echo breadcrumb::generate(get_class(), $function);

		echo '<div class="contentpadfull">
        <p class="title2">New file</p> <p><b>Please enter a name for the new file to create it in the current working directory</b>
    
        <div class="heading">New file</div>
    
        <form id="login" name="login" method="get" action="?id=files&action=uploadfile" enctype="multipart/form-data">
        <input type="hidden" name="id" value="view-information">
        <div class="label">Select file to upload </div>
        <input type="file" name="file" id="file" class="submit"><br>
        <div class="label"> </div>
        <input type="submit" class="submit" value="Upload file" />
        </form>';

	}
}

?>