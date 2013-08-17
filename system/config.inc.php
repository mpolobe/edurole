<?php
$conf['conf']['debugging'] = FALSE; //SET TRUE FOR DEBUG LOG

$conf['conf']['titlename'] = "EduRole Student Information System";
$conf['conf']['domain'] = "edurole.com";
$conf['conf']['mailenabled'] = TRUE;
$conf['conf']['hash'] = "2#FCLWJEFO2j3@K#LKF"; //CHANGE THIS TO SOMETHING UNIQUE

//MYSQL server information
$conf['mysql']['server'] = "localhost";
$conf['mysql']['user'] = "root";
$conf['mysql']['password'] = "PASSWORD";
$conf['mysql']['db'] = "edurole";

//LDAP server information (EXAMPLE UNIVERSITY TEST DATA)
$conf['conf']['ldapenabled'] = FALSE;
$conf['ldap']['server'] = "localhost";
$conf['ldap']['port'] = "389";
$conf['ldap']['studentou'] = "ou=students,dc=mulungushi,dc=ac,dc=zm";
$conf['ldap']['staffou'] = "ou=staff,dc=mulungushi,dc=ac,dc=zm";
$conf['ldap']['adminou'] = "ou=administrators,dc=mulungushi,dc=ac,dc=zm";

//MAIL server information
$conf['mail']['server'] = "localhost";
$conf['mail']['port'] = "389";

//Enabled templates, default is first template listed
$conf['conf']['templates'] = array("edurole", "silver", "opus");

//CSS available to the system, 0 is included on every page
$conf['css'][0] = '<link href="templates/%TEMPLATE%/css/style.css" rel="stylesheet" type="text/css" />';
$conf['css'][3] = '<link href="templates/%TEMPLATE%/css/jq.css" rel="stylesheet" type="text/css" />';
$conf['css'][4] = '<link href="templates/%TEMPLATE%/css/ddSlick.css" rel="stylesheet" type="text/css" />';
$conf['css'][6] = '<link href="templates/%TEMPLATE%/css/login.css" rel="stylesheet" type="text/css" />';
$conf['css'][1] = '<link href="lib/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />';
$conf['css'][2] = '<link href="lib/fullcalendar/fullcalendar.print.css" rel="stylesheet" type="text/css" media="print" />';
$conf['css'][5] = '<link href="lib/aloha/css/aloha.css" rel="stylesheet" type="text/css" />';

//Javascript available to the system, 0 is included on every page
$conf['javascript'][0] = '<script src="lib/jquery/jquery.js"></script>';
$conf['javascript'][2] = '<script src="lib/jquery/jquery.ui.core.js"></script>';
$conf['javascript'][3] = '<script src="lib/jquery/jquery.dropdown.js"></script>';
$conf['javascript'][4] = '<script src="lib/jquery/jquery.ui.widget.js"></script>';
$conf['javascript'][5] = '<script src="lib/jquery/jquery.ui.datepicker.js"></script>';
$conf['javascript'][6] = '<script src="lib/requirejs/require.js"></script>';
$conf['javascript'][7] = '<script src="lib/aloha/lib/aloha.js" data-aloha-plugins="common/ui,  common/format, common/list, common/link, common/highlighteditables"></script>';
$conf['javascript'][9] = '<script src="lib/fullcalendar/fullcalendar.min.js"></script>';
?>
