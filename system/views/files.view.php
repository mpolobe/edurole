<?php
class files {

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

		include $this->core->classPath . "files.inc.php";

		$userHome = $this->core->dataStorePath . $this->username;
		$filename = $this->core->cleanGet['filename'];

		if ($this->core->action == "overview" || !isset($this->core->action)) {
			$this->viewPersonalFiles($userHome);
		} elseif ($this->core->action == "edit") {
			$this->editFile($filename);
		} elseif ($this->core->action == "saveFile") {
			$this->saveFile($userHome);
		} elseif ($this->core->action == "rename") {
			$rename = $this->renameFile($userHome);
			if($rename) {
				$this->viewPersonalFiles($userHome);
			}
		} elseif ($this->core->action == "delete") {
			$this->deleteFile($userHome);
		} elseif ($this->core->action == "new") {
			$this->newFileForm();
		} elseif ($this->core->action == "upload") {
			$this->upload();
		} elseif ($this->core->action == "uploadfile") {
			$this->uploadFile();
		} elseif ($this->core->action == "newFile") {
			$this->newFile($userHome);
		}
	}

	function viewPersonalFiles($path) {
		$function = __FUNCTION__;
		$title = 'Overview of personal files';
		$description = 'This directory lists your personal files';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		echo '<p><img src="templates/edurole/images/up.png"/> <a href="?id=files&action=upload&op=' . $path . '">upload a file</a> or <img src="templates/edurole/images/dd.gif"/> <a href="?id=files&action=new&op=' . $path . '">create an empty file</a> or <img src="templates/edurole/images/new.gif"/> <a href="?id=files&action=newdir&op=' . $path . '">new directory</a></b>';

		overview($path);
	}

	function upload() {
		$function = __FUNCTION__;
		$title = 'Upload a file';
		$description = 'Please note that executables must be compressed and the maximum file size is 50MB';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		echo '<div class="heading">File upload</div>

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
		$title = 'New file';
		$description = 'Please enter a name for the new file to create it in the current working directory';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		echo '<div class="heading">New file</div>
    
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