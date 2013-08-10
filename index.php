<?php 
session_start();

/*
	The application includes are separated in the following directories:
	includes/			(core components, authentication, template-constructor, database connectors)
	includes/forms		(input forms)
	includes/views		(generated output)
	includes/classes	(functions) 

	General administrator configurable system options
	EDIT THE INCLUDES/CONFIG.INC.PHP FILE FOR GENERAL SYSTEM SETTINGS
*/
require_once "includes/config.inc.php";

/*
	 Required core classes
*/
require_once "includes/core.inc.php";
require_once "includes/database.inc.php";
require_once "includes/authentication.inc.php";
require_once "includes/namespace.inc.php";
require_once "includes/breadcrumb.inc.php";
require_once "includes/template.inc.php";
require_once "includes/viewbuilder.inc.php";
require_once "includes/menu.inc.php";
		
/*
	Initialize core which holds core functions and variables
	Start view builder/page loader 
*/
$core = new eduroleCore($conf);

new viewBuilder($core);
?>