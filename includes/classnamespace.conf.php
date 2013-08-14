<?php

/* TODO: CREATE NAMESPACE FOR ALL VIEWS
Namespace variables for use in breadcrumbs and pagetitles 
Migrated from local in-class variables
*/

$namespace['home'] = array("functionalname" => "Welcome to EduRole", "executionpath" => array("home"));
$namespace['change-password'] = array("functionalname" => "Change password", "executionpath" => array("home"));
$namespace['view-information'] = array("functionalname" => "Personal Information", "executionpath" => array("home"));
$namespace['search-results'] = array("functionalname" => "Search results", "executionpath" => array("home", "view-information"));
$namespace['intake'] = array("functionalname" => "Studies open for intake Intake", "executionpath" => array("home"));
$namespace['info'] = array("functionalname" => "Studies offered at institution", "executionpath" => array("home"));
$namespace['register'] = array("functionalname" => "Online registration", "executionpath" => array("home", "intake"));
$namespace['calendar'] = array("functionalname" => "Personal calendar", "executionpath" => array("home"));
$namespace['mail'] = array("functionalname" => "Personal email", "executionpath" => array("home"));
$namespace['filemanager'] = array("functionalname" => "File management", "executionpath" => array("home"));
$namespace['password'] = array("functionalname" => "Recover your password", "executionpath" => array("home"));
$namespace['changepass'] = array("functionalname" => "Change your password", "executionpath" => array("home"));
?>