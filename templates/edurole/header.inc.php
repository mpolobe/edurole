<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<meta name="apple-mobile-web-app-capable" content="yes" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo $this->core->getTitle(); ?></title>

<link rel="icon" type="image/png" href="<? echo $this->core->conf['conf']['path']; ?>/templates/edurole/images/apple-touch-icon-144x144-precomposed.png">
<link rel="apple-touch-startup-image" href="<? echo $this->core->conf['conf']['path']; ?>/templates/edurole/images/splash-screen-320x460.png" media="screen and (max-device-width: 320px)" />
<link rel="apple-touch-startup-image" media="(max-device-width: 480px) and (-webkit-min-device-pixel-ratio: 2)" href="<? echo $this->core->conf['conf']['path']; ?>/templates/edurole/images/splash-screen-640x920.png" />
<link rel="apple-touch-icon" href="<? echo $this->core->conf['conf']['path']; ?>/templates/edurole/images/apple-touch-icon-57x57-precomposed.png" />
<link rel="apple-touch-icon" sizes="72x72" href="<? echo $this->core->conf['conf']['path']; ?>/templates/edurole/images/apple-touch-icon-72x72-precomposed.png" />
<link rel="apple-touch-icon" sizes="114x114" href="<? echo $this->core->conf['conf']['path']; ?>/templates/edurole/images/apple-touch-icon-114x114-precomposed.png" />
<link rel="apple-touch-icon" sizes="144x144" href="<? echo $this->core->conf['conf']['path']; ?>/templates/edurole/images/apple-touch-icon-144x144-precomposed.png" />

<? 
echo $this->cssFiles;
echo $this->jsFiles; 

if(isset($this->jsConflict)){
	echo'<script type="text/javascript">
		jQuery.noConflict();
	</script>';
}
?>

</head>
<body>
 <div class="headercenter"><a href="<? echo $this->core->conf['conf']['path']; ?>"><img src="<? echo $this->core->fullTemplatePath; ?>/images/header.png" /></a></div>
		<div class="bodycontainer">