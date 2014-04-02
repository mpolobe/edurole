<?php
class filescontroller{

	public $core;

	function __construct($core){
		$this->core = $core;
	}

	function overview($path) {

		$start = TRUE;
		$selected = $_GET['op'];
		$path = $path . $selected;
		$path = str_replace("//", "/", $path);

		if (file_exists($path)) {
	
			$handle = opendir($path);
			$o = 0;

			while (false !== ($file = readdir($handle))) {

				$type = filetype("$path/$file");
	
				if ($start == TRUE) {

					$current = dirname($selected . '../');

					echo ' <br />
					<table  width="768px" border=0 cellpadding="3" cellspacing="0" bordercolor="#cccccc" align="left">
					<tr><td>';
					if ($selected != "/") {
						echo '&nbsp;&nbsp;&nbsp; <a href="?&id=files&action=overview&op=' . $current . '"><img src="' . $this->core->fullTemplatePath . '/images/up.png" border="0"> up one directory</a>';
					}
					echo '</td>
					</tr>';

					$start = FALSE;

				} else if ($file == ".") {

				} elseif ($file == "..") {

				} elseif ($file == "") {

				} else {

					if ($o == "0") {
						$o++;
						echo '<tr class="zebra"><td width="460">';
					} else {
						$o--;
						echo '<tr><td width="460" >';
	
					}

				if ($kleur == "bgcolor=#FFFFFF") {
					$bgl = "";
				} else {
					$bgl = "background=img/bgl.gif";
				}

				$fileurl = urlencode($file);
				$selectedurl = urlencode($selected);

				$fi = $file;

				if (strlen($file) > 70) $file = substr($file, 0, 65) . " ...";

				if ($type == "dir") {

					$type = str_replace("dir", "<img src=" . $this->core->fullTemplatePath . "/images/new.gif>", $type);

					echo ' &nbsp;&nbsp;&nbsp;' . $type . '&nbsp;&nbsp;  <a href="' . $this->core->conf['conf']['path'] . '?&id=files&action=overview&op=' . $selected . '/' . $fileurl . '"><b>' . $file . '</b></a></td>
					<td width="100"  ' . $kleur . ' style="color: #999;">&nbsp; dir</td>
					<td width="60" style="color: #999;" align="center" ' . $kleur . '><img src="' . $this->core->fullTemplatePath . '/images/edit.gif" border="0"> edit</td>
					<td width="60" align="center" ' . $kleur . '><a href=' . $this->core->conf['conf']['path'] . '/files&action=delete&del=' . $selected . '/' . $fileurl . '&atat=mo.php?op=' . $path . '><img alt="Delete Directory" src="' . $this->core->fullTemplatePath . '/images/delete.gif" border="0"> delete</a></TD>
					<td width="60" align="center" ' . $bgl . ' ' . $kleur . '><a href="' . $this->core->conf['conf']['path'] . '/files&action=rename&ren=' . $selected . '/' . $fileurl . '&op=' . $path . '">
					<center><img alt="Rename Directory" src="' . $this->core->fullTemplatePath . '/images/ren.gif" border="0"> rename</a></TD>';

				} else {

					$qqt = $path . '/' . $fi . '';
					$sd = filesize($qqt);

					if ($sd < "1024") {
						$ty = "b";
					} elseif ($sd < "1024000") {
						$ty = "kb";
						$sd / 102400;
					} elseif ($sd > "1024000") {
						$ty = "mb";
						$sd = $sd / 1024000;
					}

					$sd = substr($sd, 0, 4) . "";
					$type = str_replace("file", "<img src=" . $this->core->fullTemplatePath . "/images/dd.gif>", $type);
					echo ' &nbsp;&nbsp;&nbsp;' . $type . '&nbsp;&nbsp;   <a href="' . $this->core->conf['conf']['path'] . '/download&file=' . $selected . '' . $fileurl . '">' . $file . '</a></TD>
					<td width="100" ' . $kleur . ' style="color: #999;">&nbsp; ' . $sd . '' . $ty . '</td>
					<td width="60"  align="center" ' . $kleur . '><a href="' . $this->core->conf['conf']['path'] . '/files&action=edit&show=0&edi=' . $selected . '/' . $fileurl . '&op=' . $selected . '"><img alt="Edit File" src="' . $this->core->fullTemplatePath . '/images/edit.gif" border="0"> edit</a></TD>
					<td width="60"   align="center" ' . $kleur . '><a href=' . $this->core->conf['conf']['path'] . '/files&action=delete&del=' . $selected . '/' . $fileurl . '&op=' . $selected . '><img alt="Delete File" src="' . $this->core->fullTemplatePath . '/images/delete.gif" border="0"> delete</a></TD>
					<td width="60"  valign="middle" align="center" ' . $bgl . ' ' . $kleur . '><a  href="' . $this->core->conf['conf']['path'] . '/files&action=rename&show=0&ren=' . $selected . '/' . $fileurl . '&op=' . $selected . '"><center><img alt="Rename File" src="' . $this->core->fullTemplatePath . '/images/ren.gif" border="0"> rename</a></TD>';

					}
	
				}
	
			}
	
			closedir($handle);
			echo "</table>";
		}
	}

function newFile() {

	$bestand = $this->core->cleanPost['file'];
	$filename = $this->core->cleanPost['dir'];
	$path = $this->core->cleanPost['op'];
	$at = $_GET["atat"];

	if ($filename != "") {

		if (file_exists("$path/$filename")) {

			echo '<meta http-equiv="refresh" content="2;URL=' . $at . '">File aready exists';

		} else {

			mkdir("$path/$filename", 0700);
			echo '<meta http-equiv="refresh" content="0;URL=' . $at . '">';

		}

	}


	if ($bestand != "") {

		if (file_exists("$path/$bestand")) {

			echo '<meta http-equiv="refresh" content="2;URL=' . $at . '">File aready exists';

		} else {

			$open = fopen("$path/$bestand", "w");
			$tekst = @fread($open, filesize("$path/$bestand"));
			fclose($open);
			echo '<meta http-equiv="refresh" content="80;URL=' . $at . '">';

		}

	}
}

function deleteFile() {

	$at = $_GET["atat"];
	$new = $this->core->cleanPost["new"];
	$filename = $_GET["ren"];

	$filename = $this->core->cleanPost["filend"];
	$everything = "$path$filename";
	$everything = str_replace("//", "/", $everything);

	if (file_exists($everything)) {

		$var = fileperms($everything);

		if ($var == "16832") {

			rmdir($everything);
			echo '<meta http-equiv="refresh" content="0;URL=' . $at . '">';
			return true;

		} else {

			echo '<meta http-equiv="refresh" content="0;URL=' . $at . '">';
			unlink($everything);
			return true;

		}

	} else {

		return false;


	}

}

function renameFile($path) {
	$at = $_GET["atat"];
	$new = $this->core->cleanPost["new"];
	$filename = $_GET["ren"];

	if (isset($new)) {

		$filename = $this->core->cleanPost["filend"];
		rename("$path$filename", "$path/$new");
		return true;

	} else {
		echo '<div class="easymencontainer"><form name="rename" method="post" action="/files&action=rename">
		<input name="op" type="hidden" value=' . $path . '>
		<input name="filend" type=hidden value="' . $filename . '">
		<div class="padding"><div class="label">New Name</div>
		<input type="text" name="new" class="submit"></div>
		<div class="padding"><div class="label"></div><input type="submit" name="Submit" value="Rename" class="submit"></div>
		</form></div>';

		return false;
	}

}

function saveFile() {

	$filename = $this->core->cleanPost["filename"];
	$at = $_GET["atat"];
	$tekst = $this->core->cleanPost["tekst"];
	$tekst = stripslashes($tekst);

	$filename2 = str_replace("%20", " ", $filename);
	$data = fopen("$home/$this->username/$filename2", "w");
	fwrite($data, $tekst);
	fclose($data);

	echo '<meta http-equiv="refresh" content="30;URL=' . $at . '">';

}

function downloadFile($filename) {

	$home = getcwd();

	$path = $home . "/datastore/userhomes/" . eduroleCore::getUsername() . "/";
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

function editFile($filename) {
	$start = TRUE;
	$home = getcwd();
	$selected = $_GET['op'];
	$username = $_SESSION['username'];
	$path = $home . "/datastore/userhomes/" . $username;
	$path = str_replace("//", "/", $path);
	$file = $path . $filename;

	$type = filetype($file);

	if ($type == dir) {

		echo "een map kan niet bewerkt worden";

	} else {

		$extension = end(explode(".", $file));

		if ($extension != "sh") {
			echo "<script type=\"text/javascript\">
				Aloha.ready( function() {
					var $ = Aloha.jQuery;
					$('.editable').aloha();
				});
			</script>";
		}

		echo '<form name="form1" method="post" action="mo.php/save&atat=' . $at . '">';
		$open = fopen("$file", "r");
		$tekst = htmlentities(@fread($open, filesize($file)), ENT_QUOTES);

		echo '<input type=hidden name=filename value=' . $bestand . '>
		<textarea name="tekst" rows="30" cols="105" class="editable">' . $tekst . '</textarea>
		<br><input type="submit" name="Submit" value="Save">';

		fclose($open);
		echo '</form>';

	}
}

function uploadFile($file) {

	$notallowedExts = array("exe", "EXE", "cmd", "CMD", "sh", "SH", "vb", "VB", "app", "APP", "com", "COM", "bat", "BAT", "php", "PHP", "html", "HTML", "cgi", "CGI", "htm", "HTM", "htaccess");
	$extension = end(explode(".", $file["upload"]));

	if (($file["size"] < 50000000) && !in_array($extension, $notallowedExts)) {

		if ($file["error"] > 0) {
			echo "Error: " . $file["error"] . "<br>";
		} else {

			$name = randomName(10);
			while (file_exists("datastore/userdata/$name." . $extension)) {
				$name = randomName(10);
			}

			move_uploaded_file($file["tmp_name"], "datastore/userdata/$name." . $extension);

			return ("$name." . $extension);
		}

	} else {
		echo "Invalid file";
	}
}


function randomName($length) {
	$id = ($id == NULL) ? uniqid(hash("sha512", mt_rand()), TRUE) : $id;
	$code = hash("sha512", $id . $salt);
	return $length == NULL ? $code : substr($code, 0, $length);
}
}
?>
