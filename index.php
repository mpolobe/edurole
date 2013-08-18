<?php
session_start();

/*
	The application system are separated in the following directories:
	system/				(core components, authentication, template-constructor, database connectors)
	system/forms		(input forms)
	system/views		(generated output)
	system/classes		(functions)

	General administrator configurable system options
	EDIT THE system/CONFIG.INC.PHP FILE FOR GENERAL SYSTEM SETTINGS
*/
require_once "system/config.inc.php";

/*
	 Required core classes
*/
require_once "system/core.inc.php";
require_once "system/database.inc.php";
require_once "system/authentication.inc.php";
require_once "system/namespace.inc.php";
require_once "system/breadcrumb.inc.php";
require_once "system/viewbuilder.inc.php";
require_once "system/menu.inc.php";
require_once "system/components.inc.php";

/*
	Initialize core which holds core functions and variables
	Start view builder/page loader
*/
$core = new eduroleCore($conf);

new viewBuilder($core);
?>
