<?php
/*
 * General system setup
 * You are required to change these values for the setup of the system.
 */
$conf['conf']['debugging'] = FALSE;											// Set TRUE to enable debugging
$conf['conf']['institutionName'] = "EduRole University"; 					// Institution name
$conf['conf']['domain'] = "edurole.com";									// Domain on which the pages will be served
$conf['conf']['mailEnabled'] = TRUE;										// Whether to enable in-system mailclient
$conf['conf']['hash'] = "2#FCLWJEFO2j3@K#LKF";								// Change this to a unique random set of characters
$conf['conf']['titleName'] = "EduRole Student Information System"; 			// Default Page Title
$conf['conf']['systemMail'] = "info@edurole.com"; 							// System email

/*
 * System paths
 */
$conf['conf']['path'] = "/edurole-git"; 									// Change to path to path to Edurole on webserver (example: www.example.com/pathtosystem) would be /pathtosystem.
$conf['conf']['classPath'] = "system/classes/"; 							// Location for classes
$conf['conf']['viewPath'] = "system/views/"; 								// Location for views
$conf['conf']['formPath'] = "system/forms/"; 								// Location for forms
$conf['conf']['templatePath'] = "templates/"; 								// Location for templates
$conf['conf']['dataStorePath'] = getcwd() . "/datastore/home/"; 			// Datastore location (userhomes, identities, etc.)

/*
 * MYSQL server information
 */
$conf['mysql']['server'] = "localhost"; 									// MySQL server host
$conf['mysql']['user'] = "root"; 											// MySQL user
$conf['mysql']['password'] = "PASSWORD"; 									// MySQL password
$conf['mysql']['db'] = "edurole"; 											// MySQL database

/*
 * LDAP server information
 */
$conf['conf']['ldapEnabled'] = FALSE; 										// Enable LDAP integration or not
$conf['ldap']['server'] = "localhost"; 										// LDAP host
$conf['ldap']['port'] = "389"; 												// LDAP server port
$conf['ldap']['studentou'] = "ou=students,dc=mulungushi,dc=ac,dc=zm"; 		// Dedicated OU is needed for students
$conf['ldap']['staffou'] = "ou=staff,dc=mulungushi,dc=ac,dc=zm"; 			// Dedicated OU is needed for staff
$conf['ldap']['adminou'] = "ou=administrators,dc=mulungushi,dc=ac,dc=zm"; 	// Dedicated OU is needed for administrators

/*
 * Mail server information
 */
$conf['mail']['server'] = "localhost"; 										// IMAP server address
$conf['mail']['port'] = "389"; 												// IMAP port

/*
 * Enabled templates, default is first template listed
 */
$conf['conf']['templates'] = array("edurole"); 								// Template names in array form

/*
 * CSS available to the system, 0 is included on every page
 */
$conf['css'][0] = '<link href="%BASE%/templates/%TEMPLATE%/css/style.css" rel="stylesheet" type="text/css" />';
$conf['css'][3] = '<link href="%BASE%/templates/%TEMPLATE%/css/jq.css" rel="stylesheet" type="text/css" />';
$conf['css'][4] = '<link href="%BASE%/templates/%TEMPLATE%/css/ddSlick.css" rel="stylesheet" type="text/css" />';
$conf['css'][6] = '<link href="%BASE%/templates/%TEMPLATE%/css/login.css" rel="stylesheet" type="text/css" />';
$conf['css'][1] = '<link href="%BASE%/lib/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />';
$conf['css'][2] = '<link href="%BASE%/lib/fullcalendar/fullcalendar.print.css" rel="stylesheet" type="text/css" media="print" />';
$conf['css'][5] = '<link href="%BASE%/lib/aloha/css/aloha.css" rel="stylesheet" type="text/css" />';

/*
 * Javascript available to the system, 0 is included on every page
 */
$conf['javascript'][0] = '<script src="%BASE%/lib/jquery/jquery.js"></script>';
$conf['javascript'][2] = '<script src="%BASE%/lib/jquery/jquery.ui.core.js"></script>';
$conf['javascript'][3] = '<script src="%BASE%/lib/jquery/jquery.dropdown.js"></script>';
$conf['javascript'][4] = '<script src="%BASE%/lib/jquery/jquery.ui.widget.js"></script>';
$conf['javascript'][5] = '<script src="%BASE%/lib/jquery/jquery.ui.datepicker.js"></script>';
$conf['javascript'][6] = '<script src="%BASE%/lib/requirejs/require.js"></script>';
$conf['javascript'][7] = '<script src="%BASE%/lib/aloha/lib/aloha.js" data-aloha-plugins="common/ui,  common/format, common/list, common/link, common/highlighteditables"></script>';
$conf['javascript'][9] = '<script src="%BASE%/lib/fullcalendar/fullcalendar.min.js"></script>';
?>