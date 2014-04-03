
// DEFAULT CONFIG SECTION NOT MODIFIED BY INSTALLER

$conf['conf']['installed'] = TRUE;                                              // Set to true to complete installation
$conf['conf']['debugging'] = FALSE;                                             // Set TRUE to enable debugging

$conf['conf']['updates']['server'] = "http://edurole.com/update/";              // Update server to receive updates from

error_reporting(0);								// Disable PHP error messages in production

/*
 * System paths
 */
$conf['conf']['classPath'] = "system/classes/"; 				// Location for classes
$conf['conf']['viewPath'] = "system/views/"; 					// Location for views
$conf['conf']['libPath'] = "lib/"; 						// Location for forms
$conf['conf']['servicePath'] = "system/services/"; 				// Location for services
$conf['conf']['formPath'] = "system/forms/"; 					// Location for forms
$conf['conf']['templatePath'] = "templates/"; 					// Location for templates
$conf['conf']['dataStorePath'] = getcwd() . "/datastore/"; 			// Datastore location (userhomes, identities, etc.)


/*
 * Enabled templates, default is first template listed
 */
$conf['conf']['templates'] = array("edurole", "nkrumah", "bootstrap");		// Template names in array form

/*
 * CSS available to the system, required is included everywhere header is active
 */
$conf['css']['required'] = array("style", "ddslick");

$conf['css']['style'] = '<link href="%BASE%/templates/%TEMPLATE%/css/style.css" rel="stylesheet" type="text/css" />';
$conf['css']['ddslick'] = '<link href="%BASE%/templates/%TEMPLATE%/css/ddSlick.css" rel="stylesheet" type="text/css" />';
$conf['css']['jq'] = '<link href="%BASE%/templates/%TEMPLATE%/css/jq.css" rel="stylesheet" type="text/css" />';
$conf['css']['login'] = '<link href="%BASE%/templates/%TEMPLATE%/css/login.css" rel="stylesheet" type="text/css" />';
$conf['css']['fullcalendar'] = '<link href="%BASE%/lib/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />';
$conf['css']['fullcalendar.print'] = '<link href="%BASE%/lib/fullcalendar/fullcalendar.print.css" rel="stylesheet" type="text/css" media="print" />';
$conf['css']['aloha'] = '<link href="%BASE%/lib/aloha/css/aloha.css" rel="stylesheet" type="text/css" />';

/*
 * Javascript available to the system, required is included everywhere header is active
 */
$conf['javascript']['required'] = array("mail", "jquery.dropdown", "jquery");

$conf['javascript']['jquery'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.js"></script>';
$conf['javascript']['jquery.dropdown'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.dropdown.js"></script>';
$conf['javascript']['mail'] = '<script type="text/javascript" src="%BASE%/lib/edurole/javascript/mail.js"></script>';
$conf['javascript']['jquery.ui'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.ui.core.js"></script>';
$conf['javascript']['jquery.ui.widget'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.ui.widget.js"></script>';
$conf['javascript']['jquery.ui.datepicker'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.ui.datepicker.js"></script>';
$conf['javascript']['require'] = '<script type="text/javascript" src="%BASE%/lib/requirejs/require.js"></script>';
$conf['javascript']['aloha'] = '<script src="%BASE%/lib/aloha/lib/aloha.js" data-aloha-plugins="common/ui,  common/format, common/list, common/link, common/highlighteditables"></script>';
$conf['javascript']['fullcalendar'] = '<script type="text/javascript" src="%BASE%/lib/fullcalendar/fullcalendar.min.js"></script>';
$conf['javascript']['register'] = '<script type="text/javascript" src="%BASE%/lib/edurole/javascript/register.js"></script>';
$conf['javascript']['jquery.form-repeater'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.form-repeater.js"></script>';
$conf['javascript']['jquery.ui.dialog'] = '<script type="text/javascript" src="%BASE%/lib/jquery/jquery.ui.dialog.js"></script>';
?>
