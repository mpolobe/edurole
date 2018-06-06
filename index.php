<?php
session_start();

/*
	If you have no clue what all these files do please use the EduRole installer.
	-> wwww.yoursite.com/edurole/installer/
	Or manually open the system configuration:
	-> system/config.inc.php

	The application system are separated in the following directories:
	system/				Core components, authentication, template-constructor, database connectors and the like.
	system/forms		Input forms
	system/views		View handlers
	system/services		Service handlers
	system/classes		Functional classes
*/

require_once "system/config.inc.php";

/*
	 Required core classes
*/
require_once "system/core.inc.php";
require_once "system/database.inc.php";
require_once "system/authentication.inc.php";
require_once "system/breadcrumb.inc.php";
require_once "system/menu.inc.php";
require_once "system/components.inc.php";
require_once "system/viewBuilder.inc.php";
require_once "system/serviceBuilder.inc.php";

/*
	Initialize core which holds core functions and variables
	Start view builder/page loader
*/
$core = new eduroleCore($conf);
?>