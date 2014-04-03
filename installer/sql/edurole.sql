/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `access`
--

DROP TABLE IF EXISTS `access`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `access` (
  `ID` int(11) NOT NULL auto_increment,
  `Username` varchar(50) NOT NULL,
  `RoleID` int(11) NOT NULL,
  `Password` varchar(128) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2010226819 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `access`
--

LOCK TABLES `access` WRITE;
/*!40000 ALTER TABLE `access` DISABLE KEYS */;
INSERT INTO `access` VALUES (2010222117,'rvos',1000,'017fe2c68f2a319f0fcf78db41ecda004b4d847a22156e65ff00abbb5adf51dafdf6a5265c0475f628c90f901d2974d5f9892d17217cdb13e7628d8c645f8d4f'),(2010226818,'administrator',1000,'2b4259a65acb9cf357ee81cb559909b69ad6624bd45d7371891a7c3d1e5a4f8071d05f7d88c5a9450e4a56b6503fd3ffce6454945d000cb60670a524604eba9f');
/*!40000 ALTER TABLE `access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accommodation`
--

DROP TABLE IF EXISTS `accommodation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `accommodation` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL,
  `Location` varchar(64) NOT NULL,
  `Rooms` int(11) NOT NULL,
  `Capacity` int(11) NOT NULL,
  `LocationDescription` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `accommodation`
--

LOCK TABLES `accommodation` WRITE;
/*!40000 ALTER TABLE `accommodation` DISABLE KEYS */;
/*!40000 ALTER TABLE `accommodation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `assignments` (
  `ID` int(11) NOT NULL auto_increment,
  `CreatorID` int(11) NOT NULL,
  `AssignmentName` varchar(255) NOT NULL,
  `AssignmentDescription` text NOT NULL,
  `CourseID` int(11) NOT NULL,
  `ProgrammeID` int(11) NOT NULL,
  `StudyID` int(11) NOT NULL,
  `AssignmentWeight` int(11) NOT NULL,
  `UploadNeeded` int(11) NOT NULL,
  `AssignmentFiles` text NOT NULL,
  `DateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `SubmissionDeadline` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `assignments`
--

LOCK TABLES `assignments` WRITE;
/*!40000 ALTER TABLE `assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `basic-information`
--

DROP TABLE IF EXISTS `basic-information`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `basic-information` (
  `FirstName` varchar(35) NOT NULL,
  `MiddleName` varchar(35) NOT NULL,
  `Surname` varchar(35) NOT NULL,
  `Sex` enum('Male','Female','Unknown') NOT NULL,
  `ID` int(9) NOT NULL auto_increment,
  `GovernmentID` varchar(25) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `PlaceOfBirth` varchar(40) NOT NULL,
  `Nationality` enum('Afghan','Albanian','Algerian','American','Andorran','Angolan','Antiguans','Argentinean','Armenian','Australian','Austrian','Azerbaijani','Bahamian','Bahraini','Bangladeshi','Barbadian','Barbudans','Batswana','Belarusian','Belgian','Belizean','Beninese','Bhutanese','Bolivian','Bosnian','Brazilian','British','Bruneian','Bulgarian','Burkinabe','Burmese','Burundian','Cambodian','Cameroonian','Canadian','Cape Verdean','Central African','Chadian','Chilean','Chinese','Colombian','Comoran','Congolese','Costa Rican','Croatian','Cuban','Cypriot','Czech','Danish','Djibouti','Dominican','Dutch','East Timorese','Ecuadorean','Egyptian','Emirian','Equatorial Guinean','Eritrean','Estonian','Ethiopian','Fijian','Filipino','Finnish','French','Gabonese','Gambian','Georgian','German','Ghanaian','Greek','Grenadian','Guatemalan','Guinea-Bissauan','Guinean','Guyanese','Haitian','Herzegovinian','Honduran','Hungarian','I-Kiribati','Icelander','Indian','Indonesian','Iranian','Iraqi','Irish','Israeli','Italian','Ivorian','Jamaican','Japanese','Jordanian','Kazakhstani','Kenyan','Kittian and Nevisian','Kuwaiti','Kyrgyz','Laotian','Latvian','Lebanese','Liberian','Libyan','Liechtensteiner','Lithuanian','Luxembourger','Macedonian','Malagasy','Malawian','Malaysian','Maldivan','Malian','Maltese','Marshallese','Mauritanian','Mauritian','Mexican','Micronesian','Moldovan','Monacan','Mongolian','Moroccan','Mosotho','Motswana','Mozambican','Namibian','Nauruan','Nepalese','New Zealander','Nicaraguan','Nigerian','Nigerien','North Korean','Northern Irish','Norwegian','Omani','Pakistani','Palauan','Panamanian','Papua New Guinean','Paraguayan','Peruvian','Polish','Portuguese','Qatari','Romanian','Russian','Rwandan','Saint Lucian','Salvadoran','Samoan','San Marinese','Sao Tomean','Saudi','Scottish','Senegalese','Serbian','Seychellois','Sierra Leonean','Singaporean','Slovakian','Slovenian','Solomon Islander','Somali','South African','South Korean','Spanish','Sri Lankan','Sudanese','Surinamer','Swazi','Swedish','Swiss','Syrian','Taiwanese','Tajik','Tanzanian','Thai','Togolese','Tongan','Trinidadian or Tobagonian','Tunisian','Turkish','Tuvaluan','Ugandan','Ukrainian','Uruguayan','Uzbekistani','Venezuelan','Vietnamese','Welsh','Yemenite','Zambian','Zimbabwean') NOT NULL,
  `StreetName` varchar(40) NOT NULL,
  `PostalCode` varchar(10) NOT NULL,
  `Town` varchar(40) NOT NULL,
  `Country` varchar(255) NOT NULL,
  `HomePhone` varchar(25) NOT NULL,
  `MobilePhone` varchar(25) NOT NULL,
  `Disability` enum('No','Yes') NOT NULL,
  `DissabilityType` varchar(40) default NULL,
  `PrivateEmail` varchar(100) NOT NULL,
  `MaritalStatus` enum('Single','Married','Divorced','Widowed') NOT NULL,
  `StudyType` enum('Fulltime','Partime','Distance') NOT NULL,
  `Status` enum('Enrolled','Rejected','Employed','Retired','Fired','Requesting','Graduated','Suspended','Dismissed','Deceased','Failed') NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `basic-information`
--

LOCK TABLES `basic-information` WRITE;
/*!40000 ALTER TABLE `basic-information` DISABLE KEYS */;
/*!40000 ALTER TABLE `basic-information` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `calendar` (
  `ID` int(11) NOT NULL auto_increment,
  `CoursecalID` int(11) NOT NULL,
  `StartTime` int(64) NOT NULL,
  `EndTime` int(64) NOT NULL,
  `Description` text NOT NULL,
  `Location` text NOT NULL,
  `Summarye` text NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `calendar`
--

LOCK TABLES `calendar` WRITE;
/*!40000 ALTER TABLE `calendar` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `content` (
  `ContentID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL,
  `Content` text NOT NULL,
  `ContentCat` enum('static','help','news','notification','update','article','image') NOT NULL,
  PRIMARY KEY  (`ContentID`)
) ENGINE=MyISAM AUTO_INCREMENT=117 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `content`
--

LOCK TABLES `content` WRITE;
/*!40000 ALTER TABLE `content` DISABLE KEYS */;
INSERT INTO `content` VALUES (1,'Welcome to EduRole','<p>EduRole is the free academic support system that encompases:\n<li>Student information management</li>\n<li>Online student registration</li>\n<li>Student communication services (email)</li>\n<li>Centralized authentication</li>\n</p>','static'),(2,'Online student registration (left container login page)','<h2><strong>ONLINE STUDENT REGISTRATION</strong></h2>\n<p>Trough the easy online registration form you can now complete your request for admission online. Click on the link below to view the programs for which intake is currently open.</p>\n<p><a href=\"admission.php\"><strong> View current intake possibilities</strong></a></p>','static'),(100,'EduRole starts trails in Aruba','Aruba\'s educational system is patterned after the Dutch system of education. There are 68 schools for primary education, 12 schools for secondary education, and 5 universities in Aruba. </br></br> As part of a development initiative starting early 2013 Edurole will be deployed in two secondary schools. More news to follow.','news'),(101,'EduRole helps universities in Africa','Edurole started out as a small functional service access platform at Nkrumah University in Kabwe Zambia. It has since grown out to a full product capable of easy centralization of web services for institutions like Nkrumah. <br /> <br /> With the success of Nkrumah our goal is to expand Eduroles reach in developing countries such as Zambia by keeping Edurole a free product for non-profit institutions.','news'),(3,'Information on using Edurole','<p>Edurole works with user roles to assign privileges and permissions to students and staff. Each user account has a role assigned, the following roles are currently defined in the sytem:\n\n<li>Enrolling student</li>\n<li>Active Student</li>\n<li>Academic Staff</li>\n<li>Registry</li>\n<li>Library</li>\n<li>Financial manager</li>\n<li>Academic office</li>\n<li>Administrator</li>\n\n</p>','static'),(102,'Using the online admission system','Edurole started out as a small functional service access platform at Nkrumah University in Kabwe Zambia. It has since grown out to a full product capable of easy centralization of web services for institutions like Nkrumah. <br /> <br /> With the success of Nkrumah our goal is to expand Eduroles reach in developing countries such as Zambia by keeping Edurole a free product for non-profit institutions.','help'),(103,'Creating user accounts','Edurole started out as a small functional service access platform at Nkrumah University in Kabwe Zambia. It has since grown out to a full product capable of easy centralization of web services for institutions like Nkrumah. <br /> <br /> With the success of Nkrumah our goal is to expand Eduroles reach in developing countries such as Zambia by keeping Edurole a free product for non-profit institutions.','help'),(104,'Managing intake (enrollment)','Edurole started out as a small functional service access platform at Nkrumah University in Kabwe Zambia. It has since grown out to a full product capable of easy centralization of web services for institutions like Nkrumah. <br /> <br /> With the success of Nkrumah our goal is to expand Eduroles reach in developing countries such as Zambia by keeping Edurole a free product for non-profit institutions. ','help'),(105,'Using your university email ','Edurole started out as a small functional service access platform at Nkrumah University in Kabwe Zambia. It has since grown out to a full product capable of easy centralization of web services for institutions like Nkrumah. <br /> <br /> With the success of Nkrumah our goal is to expand Eduroles reach in developing countries such as Zambia by keeping Edurole a free product for non-profit institutions.','help'),(107,'Q3 2013 visit to Nkrumah and Mukuba','Q3 2013 visit to Nkrumah and Mukuba','news'),(108,'eee','EduRole System ManagementEduRole the easy Student Information Management System.Access Management-----------------------------EduRole features a full access control system to manage access to each of the systems available functions. To manage access it is important to first define the roles and groups we&nbsp;have in our organisation.Setting up User Roles----------------------------To set up user roles we enter the Permission Management function under the Administrators menu. User roles are the various levels within your organisation throughout which&nbsp;priveliges are separated.&gt; For example &#34;Academic Office&#34; would define a role for all usersemployed in the academic office, however some priveliges might need to be separated from the basic academic office employees so we also define a role &#34;Registrars Office&#34; within&nbsp;this role we can separate functiontional priveliges from the basic academic office.We can manage roles under the submenu &#34;Role Management&#34;. To add a new role press the &#34;Add New Role&#34; item and enter the name for the new role that you wish to define.After we have defined the role we are able to go to user management and assign a user this role.To be able to share access to certain system functions we need to define groups,&nbsp;these groups consist of multiple roles. A user is connected to a role but is shown&nbsp;menu&#39;s for the group the role is a part of. If you create a role you automatically also create a group with the same name. If you wish to give access to a certain function to multiple roles you can define a group that contains these roles.For example some of the system default groups:&nbsp;- The group &#34;staff&#34; contains all roles with a user access level (an access level higher than 100)&nbsp;&nbsp;- The group &#34;Students and Admitting Students&#34; contains the role &#34;Students&#34; as well as&nbsp;&#34;Admitting Students.&nbsp;- The group &#34;Everybody&#34; contains ALL roles even none authorized users. (role 0)Under the system settings menu the administrator is able to setup each of the systems&nbsp;available functions. The system mangement is divided in three segments:&nbsp;- Access Management&nbsp;- Block Managment&nbsp;- Translation ManagementUnder the Access Management panel the administrator can define the basic title and&nbsp;description for each of the available views, these are visible by default. Each view&nbsp;has a required permission dropdown, using this setting you can define the group who&nbsp;has access to the view.Menu Management------------------------------EduRole features a dynamic menu structure, you are able to define a&nbsp;menu position for each view the system has. This means that any function that the&nbsp;system has can be added to the menu for easy access. Menu blocks are named after the&nbsp;group that the user is in. Only the members who&#39;s role is part of the group will see&nbsp;the menu specific to their role.To show all students the &#34;Show Accommodation Information&#34; view we can select the&nbsp;group &#34;Students&#34; to be the required permission for this function. After which you can&nbsp;enter a number to the menu field at the end of the function. The numer of the&nbsp;function represents the relative placement in the menu, meaning that if you want&nbsp;function x to be first in the menu and function y second, you would define x as 1 in&nbsp;the menu field and y as 2 in the menu field.Extending EduRole=======================================To extend EduRole developers can build their own function modules to replace, extend or build functionality in the system.EduRole uses a model view contoler system to implement functionality.The main system is located in the &#34;system&#34; directory, inside of this directory a number ofdirectories are present, the &#34;views&#34;, &#34;forms&#34;, &#34;services&#34; and &#34;classes&#34;The system core classes are located inside of the system directory itself,EduRole utilizes its own framework to be able to be as light as possible and allow foreasy development by new PHP developers.Lets make an example module for attendence monitoring.First we will need to create a new view in the views directory, the file will need to&nbsp;have the same name as the class. In this case call the filename: &#34;attendance.view.php&#34;.We can make this a new file or we can copy the &#34;example.view.php&#34; skeleton view class.The class will need to have a configuration function to set component dependencies&nbsp;and if required javascript or CSS required by your module. The class configuration is&nbsp;not manditory from Edurole v2 onwards but it is still recomended because it makes&nbsp;development easier.Edurole knows automated view and service routing. Meaning that by default ALL your&nbsp;public functions in a view are accessable.The routing principle is quite simlpe and utilizes the following url key:www.site.com/edurole/modulename/functionname/item/subitemIf you have created the attendance.view.php and created a public function called &#34;manageAttendance&#34; you will be able to access this function like:www.site.com/edurole/attendance/manage/','help');
/*!40000 ALTER TABLE `content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coursecal`
--

DROP TABLE IF EXISTS `coursecal`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `coursecal` (
  `ID` int(11) NOT NULL auto_increment,
  `CourseName` varchar(255) NOT NULL,
  `UploadDate` datetime NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `coursecal`
--

LOCK TABLES `coursecal` WRITE;
/*!40000 ALTER TABLE `coursecal` DISABLE KEYS */;
/*!40000 ALTER TABLE `coursecal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `courses` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(50) NOT NULL,
  `CourseCoordinatorInternal` int(11) NOT NULL,
  `CourseCoordinatorDistance` int(11) NOT NULL,
  `CourseDescription` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=828 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (606,'EDU 110',2010226818,2010226818,'Introduction to Special Needs Education '),(618,'MAT 110',2010226818,2010226818,'Foundation Mathematics'),(624,'BIO 110',2010226818,2010226818,'Molecular and Cell Biology'),(625,'CHE 110',2010226818,2010226818,'Introductory Chemistry '),(626,'PHY 100',2010226818,2010226818,'Introductory Physics');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `education-background`
--

DROP TABLE IF EXISTS `education-background`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `education-background` (
  `ID` int(10) NOT NULL auto_increment,
  `StudentID` int(9) NOT NULL,
  `CertificateName` varchar(50) NOT NULL,
  `TypeOfCertificate` varchar(50) NOT NULL,
  `InstitutionName` varchar(50) NOT NULL,
  `DocumentName` varchar(128) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `education-background`
--

LOCK TABLES `education-background` WRITE;
/*!40000 ALTER TABLE `education-background` DISABLE KEYS */;
/*!40000 ALTER TABLE `education-background` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emergency-contact`
--

DROP TABLE IF EXISTS `emergency-contact`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `emergency-contact` (
  `ID` int(10) NOT NULL auto_increment,
  `StudentID` int(9) NOT NULL,
  `FullName` varchar(70) NOT NULL,
  `Relationship` varchar(50) NOT NULL,
  `PhoneNumber` int(30) NOT NULL,
  `Street` varchar(40) NOT NULL,
  `Town` varchar(40) NOT NULL,
  `Postalcode` varchar(10) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `emergency-contact`
--

LOCK TABLES `emergency-contact` WRITE;
/*!40000 ALTER TABLE `emergency-contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `emergency-contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee-package-program-link`
--

DROP TABLE IF EXISTS `fee-package-program-link`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `fee-package-program-link` (
  `ID` int(11) NOT NULL auto_increment,
  `ProgramID` int(11) NOT NULL,
  `FeePackageID` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `fee-package-program-link`
--

LOCK TABLES `fee-package-program-link` WRITE;
/*!40000 ALTER TABLE `fee-package-program-link` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee-package-program-link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee-package-study-link`
--

DROP TABLE IF EXISTS `fee-package-study-link`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `fee-package-study-link` (
  `ID` int(11) NOT NULL auto_increment,
  `StudyID` int(11) NOT NULL,
  `FeePackageID` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `fee-package-study-link`
--

LOCK TABLES `fee-package-study-link` WRITE;
/*!40000 ALTER TABLE `fee-package-study-link` DISABLE KEYS */;
/*!40000 ALTER TABLE `fee-package-study-link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fee-package`
--

DROP TABLE IF EXISTS `fee-package`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `fee-package` (
  `ID` int(16) NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Date` datetime NOT NULL,
  `Owner` int(16) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `fee-package`
--

LOCK TABLES `fee-package` WRITE;
/*!40000 ALTER TABLE `fee-package` DISABLE KEYS */;
INSERT INTO `fee-package` VALUES (1,'Standard full-time bachelor fees 2013','Standard full-time bachelor fees 2013','2013-11-13 17:24:04',2010222117);
/*!40000 ALTER TABLE `fee-package` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fees`
--

DROP TABLE IF EXISTS `fees`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `fees` (
  `ID` int(16) NOT NULL auto_increment,
  `PackageID` int(16) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Amount` int(32) NOT NULL,
  `Date` datetime NOT NULL,
  `Owner` int(16) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `fees`
--

LOCK TABLES `fees` WRITE;
/*!40000 ALTER TABLE `fees` DISABLE KEYS */;
INSERT INTO `fees` VALUES (1,1,'Examination fees','Examination fees',1200,'2013-11-13 17:25:11',0),(2,1,'Computer fees','Computer fees',400,'2013-11-13 17:25:11',0);
/*!40000 ALTER TABLE `fees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `functions`
--

DROP TABLE IF EXISTS `functions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `functions` (
  `ID` int(11) NOT NULL auto_increment,
  `Class` varchar(128) NOT NULL,
  `Function` varchar(255) NOT NULL,
  `FunctionRoutes` text NOT NULL,
  `FunctionRequiredPermissions` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  `FunctionRequiredElements` text NOT NULL,
  `FunctionTitle` text NOT NULL,
  `FunctionDescription` text NOT NULL,
  `FunctionMenuVisible` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=56552 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `functions`
--

LOCK TABLES `functions` WRITE;
/*!40000 ALTER TABLE `functions` DISABLE KEYS */;
INSERT INTO `functions` VALUES (7087,'information','edit','/edit/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.form-repeater\"],\"css\":[]}','Edit personal information','To save changes to the profile please remember to click the save button.',0),(7069,'home','show','/show/',2,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":false,\"description\":false,\"footer\":true,\"javascript\":[],\"css\":[]}','Home','Home Page',1),(7085,'mail','show','/show/',2,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Personal Mailbox','View your personal mailbox.',3),(7072,'calendar','show','/show/',2,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"fullcalendar\"],\"css\":[\"fullcalendar\",\"fullcalendar.print\"]}','Personal Calendar','The calendar displays all events added by the institution.',3),(7074,'users','add','/add/',8,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Add New User','To add a new user to the system please fill the entire form.',0),(7086,'information','show','/show/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.form-repeater\"],\"css\":[]}','Personal Information','Overview off all information associated with the student.',0),(7076,'grades','show','/show/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Students Results','View the results submitted for this student.',0),(7077,'grades','search','/search/',7,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Search for Results','Search through all results.',0),(7078,'grades','selectcourse','/selectcourse/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Upload Results','To submit grades select the course they are part of.',1),(7098,'settings','manage','/manage/',8,1,'{\"open\":false,\"header\":true,\"footer\":true,\"menu\":true,\"javascript\":[],\"css\":[]}','Manage system settings','These settings are required for the functioning of your student information system.',1),(7081,'feepackages','manage','/manage/',102,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Fee Packages','Overview of all Fee Packages',1),(7082,'payments','manage','/manage/',102,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Payment Management','A complete overview of payments, the payments marked red need to be verified manually.',2),(7083,'payments','unknown','/unknown/',102,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Unlinked Payments','List of payments currently not connected to a student.',0),(7084,'grades','manage','/manage/',107,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Results','Manage the submitted grades.',1),(7094,'intake','show','/show/',1,1,'{\"header\":true,\"menu\":false,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Studies Open For Intake','Overview of all studies with an open intake.',0),(7093,'login','show','/show/',1,1,'{\"header\":true,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":true,\"javascript\":[],\"css\":[\"login\"]}','Login page','Front page to site needs to remain accessible to everybody',0),(7096,'intake','register','/register/',1,1,'{\"header\":true,\"menu\":false,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Register to Selected Study','To complete your registration please enter all forms and submit.',0),(7097,'information','personal','/personal/',2,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.form-repeater\"],\"css\":[]}','Personal Information','Overview of all personal information.',2),(7099,'settings','permissions','/permissions/',8,1,'{\"open\":false,\"header\":true,\"footer\":true,\"menu\":true,\"javascript\":[],\"css\":[]}','Permission Management','Manage permissions to individual functions.',2),(7100,'fees','show','/show/',2,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.ui.datepicker\"],\"css\":[]}','Fee Information','List of all fees in the fee package.',0),(7101,'fees','add','/add/',102,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.ui.datepicker\"],\"css\":[]}','Add New Fee','To add a new Fee to the Fee Package please fill in the fields and press save.',0),(7102,'feepackages','edit','/edit/',102,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.ui.datepicker\"],\"css\":[]}','Edit Fee Package','Edit the currently selected Fee Package',0),(7103,'fees','save','/save/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Save Fee','Internal function',0),(7104,'feepackages','delete','/delete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Delete Package','Internal function',0),(7105,'information','save','/save/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Save Information','Internal function',0),(7106,'feepackages','add','/add/',102,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.ui.datepicker\"],\"css\":[]}','Add Fee Package','Add a new Fee Package',0),(7107,'admission','manage','/manage/',103,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Admission','Overview of admission requests and their statuses.',1),(7108,'feepackages','save','/save/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Save Package','Internal function',0),(7109,'fees','edit','/edit/',102,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.ui.datepicker\"],\"css\":[]}','Edit Existing Fee','Edit the currently selected Fees',0),(7110,'fees','delete','/delete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Delete Fee','Internal function',0),(7111,'housing','edit','/edit/',7,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Edit Housing Location Record','Edit the selected housing location record.',0),(7112,'schools','manage','/manage/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Schools','Manage all Schools.',3),(7113,'schools','show','/show/',2,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Show School Information','Showing details of the selected school.',0),(7114,'schools','edit','/edit/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Edit School','To save changes to the School please press the save button.',0),(7115,'schools','save','/save/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Save School','Internal function',0),(7116,'schools','add','/add/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Add a new School','A school serves as a top level organisational unit under which studies are placed.',0),(7117,'schools','delete','/delete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Delete School','Internal function',0),(7118,'courses','manage','/manage/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Courses','Current courses in the system',6),(7119,'courses','show','/show/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Course Information','Information about the selected course',0),(7120,'studies','manage','/manage/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.ui.datepicker\"],\"css\":[]}','Manage Studies','Manage the studies offered by the institution',4),(7121,'studies','show','/show/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.ui.datepicker\"],\"css\":[]}','Study Information','Information of the currently selected study',0),(7122,'studies','edit','/edit/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.ui.datepicker\"],\"css\":[]}','Edit Study','Edit the currently selected study',0),(7123,'studies','add','/add/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.ui.datepicker\"],\"css\":[]}','Add Study','To create a new study enter the following fields.',0),(7124,'studies','save','/save/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Delete Study','Internal function',0),(7125,'studies','delete','/delete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Delete Study','Internal function',0),(7126,'courses','add','/add/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Add New Course','To add a new course please enter the required information.',0),(7127,'courses','save','/save/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Save Course','Internal function',0),(7128,'courses','delete','/delete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Delete Course','Internal function',0),(7129,'courses','edit','/edit/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Edit Existing Course','To save changes to the course remember to press the save button.',0),(7130,'programmes','manage','/manage/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Programmes','Each Programme consists of multiple courses.',5),(7131,'programmes','show','/show/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Show ProgrammeInformation','Details of the selected Programme.',0),(7132,'programmes','edit','/edit/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Edit Programme','To add courses to the programme use the form below.',0),(7133,'programmes','add','/add/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Add new Programme','Add a new Programme by entering the form below.',0),(7134,'programmes','save','/save/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Save Programme','Internal function',0),(7136,'statement','results','/results/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Results Statement','Print a transcript of the selected results',0),(7137,'transcript','results','/results/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Results Transcript','Transcript of results',0),(7138,'programmes','delete','/delete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Delete Programme','Internal function',0),(7139,'grades','statement','/statement/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Get Statement of Results','Enter the student ID of which the statement of results need to be printed.',0),(7140,'grades','transcript','/transcript/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Get Statement of Results','Enter the student ID of which the transcript of results need to be printed.',0),(7176,'register','submit','/submit/',1,1,'{\"header\":true,\"menu\":false,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"register\",\"jquery.form-repeater\"],\"css\":[]}','Registration Submitted','Registration received by the system.',0),(7142,'grades','student','/student/',4,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Students Results','View the results submitted for this student.',1),(7144,'information','search','/search/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.form-repeater\"],\"css\":[]}','Search Student Information','You can search for a single record or a group by utilizing the various search categories.',1),(56545,'information','students','/students/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','','',0),(7146,'library','show','/show/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Show Library Section','Showing all books in the selected library section.',0),(7147,'library','manage','/manage/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Library','Here you can manage your library assets.',0),(7148,'accommodation','edit','/edit/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Edit Accommodation','Edit the existing accommodation information for this student.',0),(7149,'accommodation','show','/show/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Accommodation Information','Showing accommodation records for the selected user.',0),(7150,'accommodation','manage','/manage/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Accommodation','Manage all accommodation records.',1),(7151,'accommodation','add','/add/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Add new Accomodation','You are adding a new accommodation record to the student.',0),(7152,'accommodation','delete','/delete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Delete Accommodation','Internal function',0),(7153,'accommodation','save','/save/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Save Accommodation','Internal function',0),(7155,'payments','approve','/approve/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":true,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Approve Payment Link','Internal function',0),(7156,'payments','show','/show/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Show Payment Information','Payment details for the selected transaction.',0),(7157,'payments','balance','/balance/',3,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Check Payment Balance','Overview of outstanding and payed balances.',1),(7158,'payments','reject','/reject/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Reject Payment Link','Internal function',0),(7159,'rooms','manage','/manage/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Rooms','Manage rooms part of the selected hostel.',0),(7160,'rooms','add','/add/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Add New Room','Add a new room to the selected hostel.',0),(7161,'rooms','show','/show/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Show Room Information','The following room details have been saved.',0),(7162,'fees','assigned','/assigned/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"jquery.ui.datepicker\"],\"css\":[]}','Assigned Fees','Fees that have been assigned',0),(7163,'help','show','/show/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Help and information for system use','Click on one of the help items to learn more',0),(7165,'password','change','/change/',2,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Change Password','Change your password by entering the following data.',99),(7166,'users','students','/students/',103,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Students','Overview of all students in the system',1),(7167,'assignments','show','/show/',4,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Current Assignments','The following assignments have been assigned to courses you are currently participating in.',5),(7168,'item','show','/show/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":false,\"description\":false,\"footer\":true,\"javascript\":[\"require\",\"aloha\"],\"css\":[\"aloha\"]}','Show Item','Watch content block.',0),(7169,'item','edit','/edit/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"require\",\"aloha\"],\"css\":[\"aloha\"]}','Edit Item','To save changes to the item you are modifying press the save button.',0),(7170,'item','add','/add/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"require\",\"aloha\"],\"css\":[\"aloha\"]}','Add New Item','To add a new item to a content section please enter the content here.',0),(7171,'users','manage','/manage/',106,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Users','Overview of all users in the system',2),(7172,'register','study','/study/',1,1,'{\"header\":true,\"menu\":false,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[\"register\",\"jquery.form-repeater\"],\"css\":[]}','Request for Enrollment','You bare requesting enrollment by completing this form.',0),(7173,'intake','studies','/studies/',1,1,'{\"header\":true,\"menu\":false,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','All Studies','Overview of all studies offered by the institution.',0),(7178,'logout','','/logout/',2,1,'{\"open\":false,\"header\":false,\"footer\":false,\"menu\":false,\"breadcrumb\":false,\"javascript\":[],\"css\":[]} ','Logout','Logout the current user',100),(7179,'assignments','manage','/manage/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage Assignments','Overview of all assignments in the institution',1),(56547,'payments','list','/list/',2,1,'{\"title\":true,\"description\":true,\"open\":false,\"header\":true,\"breadcrumb\":true,\"footer\":true,\"menu\":true,\"javascript\":[],\"css\":[]}','Received Payments','Overview of payments by student.',5),(56548,'settings','update','/update/',100,1,'{\"title\":true,\"description\":true,\"open\":false,\"header\":true,\"breadcrumb\":true,\"footer\":true,\"menu\":true,\"javascript\":[],\"css\":[]}','','',0),(56544,'password','recover','/recover/',1,1,'{\"header\":true,\"menu\":false,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Recover a Forgotten Password','If you forgot your password please enter your information here.',0),(56519,'admission','promote','/promote/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Promote Student In Admission Flow','Promote Student In Admission Flow',0),(56520,'admission','complete','/complete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Admit Student','Complete Student Admission',0),(56521,'admission','reject','/reject/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Reject Students Admission','Reject Step in Admission flow',0),(56522,'admission','delete','/delete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Reject Admission','Reject Student from Institution',0),(56523,'admission','profile','/profile/',5,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Admission Status Overview','Student admission overview',1),(56524,'admission','continue','/continue/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Continue Rejected Admission','Continue admission for student who has a rejected admission step',0),(56525,'admission','active','/active/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Active Admission','Students currently with an active admission status.',0),(56526,'admission','rejected','/rejected/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Rejected Admission','Students who currently did not meet admission requirements.',0),(56527,'files','personal','/personal/',2,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Show Personal Files','An overview of your personal home directory',0),(56551,'item','save','/save/',100,1,'{\"title\":true,\"description\":true,\"open\":false,\"header\":true,\"breadcrumb\":true,\"footer\":true,\"menu\":true,\"javascript\":[],\"css\":[]}','','',0),(56550,'settings','translate','/translate/',100,1,'{\"title\":true,\"description\":true,\"open\":false,\"header\":true,\"breadcrumb\":true,\"footer\":true,\"menu\":true,\"javascript\":[],\"css\":[]}','','',0),(56549,'settings','translation','/translation/',100,1,'{\"title\":true,\"description\":true,\"open\":false,\"header\":true,\"breadcrumb\":true,\"footer\":true,\"menu\":true,\"javascript\":[],\"css\":[]}','','',0),(56546,'password','recovered','/recovered/',1,1,'{\"header\":true,\"menu\":false,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Recover a Forgotten Password','If you forgot your password please enter your information here.',0),(56543,'settings','delete','/delete/',100,1,'{\"title\":true,\"description\":true,\"open\":false,\"header\":true,\"breadcrumb\":true,\"footer\":true,\"menu\":true,\"javascript\":[],\"css\":[]}','Delete Orphan','Delete orphaned settings from database.',0),(56542,'users','save','/save/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Account Created','Please remember to save your credentials to log in to EduRole.',0),(56540,'housing','save','/save/',100,1,'{\"header\":true,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Save Housing Container','Save changes to the housing location.',0),(56539,'housing','delete','/delete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Delete Housing Location','Delete housing location.',0),(56538,'housing','manage','/manage/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Manage','Manage all housing records.',0),(56537,'housing','add','/add/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Add New Housing Location','Add new housing location.',0),(56536,'example','actionthree','/actionthree/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Example View','To learn to develop in EduRole.',0),(56535,'example','actiontwo','/actiontwo/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','For Developers','Try building your own views.',0),(56534,'example','show','/show/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Very Easy','It\'s very easy to do.',0),(56533,'error','show','/show/',1,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":false,\"description\":false,\"footer\":true,\"javascript\":[],\"css\":[]}','An Error Occurred','An error occurred in the system',0),(56532,'correct','show','/show/',103,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Information Correction','Correct student records that have incorrect numbers.',1),(56531,'help','view','/view/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','View Help Item','Show an individual help item.',0),(56530,'files','new','/new/',2,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Create a New File','Enter the name of the new file.',0),(56529,'files','download','/download/',2,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Download Selected File','Download Selected File',0),(56541,'users','delete','/delete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Deleted Account','This action cannot be undone',0),(56528,'files','upload','/upload/',2,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Upload a New File','Upload new files to the system',0),(56518,'settings','save','/save/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Save Settings','Internal function',0),(56517,'rooms','save','/save/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":false,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Save Room','Internal function',0),(56516,'rooms','delete','/delete/',100,1,'{\"header\":false,\"menu\":false,\"breadcrumb\":true,\"title\":false,\"description\":false,\"footer\":false,\"javascript\":[],\"css\":[]}','Delete Room','Internal function',0),(56515,'rooms','edit','/edit/',100,1,'{\"header\":true,\"menu\":true,\"breadcrumb\":true,\"title\":true,\"description\":true,\"footer\":true,\"javascript\":[],\"css\":[]}','Edit Room','Edit room details and press the save button.',0),(56514,'settings','functions','/functions/',100,1,'{\"open\":false,\"header\":true,\"breadcrumb\":true,\"footer\":true,\"menu\":true,\"javascript\":[],\"css\":[]}','Function Management','Manage the settings of each view function',0);
/*!40000 ALTER TABLE `functions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gradebatches`
--

DROP TABLE IF EXISTS `gradebatches`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `gradebatches` (
  `ID` int(11) NOT NULL auto_increment,
  `GlobalHash` varchar(129) NOT NULL,
  `Owner` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  `DateTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `Course` int(11) NOT NULL,
  `ValidatorID` int(11) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `ValidatorID` (`ValidatorID`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `gradebatches`
--

LOCK TABLES `gradebatches` WRITE;
/*!40000 ALTER TABLE `gradebatches` DISABLE KEYS */;
/*!40000 ALTER TABLE `gradebatches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hostel`
--

DROP TABLE IF EXISTS `hostel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `hostel` (
  `ID` int(11) NOT NULL auto_increment,
  `HostelName` varchar(255) NOT NULL,
  `TotalRooms` int(11) NOT NULL,
  `Type` int(11) NOT NULL,
  `Category` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `hostel`
--

LOCK TABLES `hostel` WRITE;
/*!40000 ALTER TABLE `hostel` DISABLE KEYS */;
INSERT INTO `hostel` VALUES (1,'Chimwemwe',0,0,1),(2,'Kafue',0,0,1),(3,'Liseli',0,0,2),(4,'Luangwa',0,0,0),(5,'Luapula',0,0,0),(6,'Mulungushi A',0,0,0),(7,'Mulungushi B',0,0,0),(8,'Mulungushi C',0,0,0),(9,'Zambezi',0,0,0),(10,'Kabompo',0,0,0),(11,'Chambishi',0,0,0),(12,'Parirenyatwa',0,0,0);
/*!40000 ALTER TABLE `hostel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `housing`
--

DROP TABLE IF EXISTS `housing`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `housing` (
  `ID` int(11) NOT NULL auto_increment,
  `StudentID` int(11) NOT NULL,
  `RoomID` int(11) NOT NULL,
  `HousingStatus` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `housing`
--

LOCK TABLES `housing` WRITE;
/*!40000 ALTER TABLE `housing` DISABLE KEYS */;
/*!40000 ALTER TABLE `housing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `languages` (
  `ID` int(11) NOT NULL auto_increment,
  `Language` varchar(255) NOT NULL,
  `Countries` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (1,'English','UK, US, ZM, GM, MW'),(2,'Spanish','ES, GQ');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library`
--

DROP TABLE IF EXISTS `library`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `library` (
  `ID` int(11) NOT NULL auto_increment,
  `StudentID` int(11) NOT NULL,
  `AuthorName` varchar(100) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `DueDate` date NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `library`
--

LOCK TABLES `library` WRITE;
/*!40000 ALTER TABLE `library` DISABLE KEYS */;
/*!40000 ALTER TABLE `library` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `messages` (
  `ID` int(11) NOT NULL auto_increment,
  `SenderID` int(11) NOT NULL,
  `RecipientID` int(11) NOT NULL,
  `BroadcastGroup` int(11) NOT NULL,
  `Subject` text NOT NULL,
  `Content` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nkrumah-grades`
--

DROP TABLE IF EXISTS `nkrumah-grades`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `nkrumah-grades` (
  `ID` int(11) NOT NULL auto_increment,
  `user` varchar(15) NOT NULL,
  `userdate` date NOT NULL,
  `usertime` time NOT NULL,
  `StudentNo` int(11) NOT NULL,
  `AcademicYear` varchar(10) NOT NULL,
  `Semester` varchar(15) NOT NULL,
  `ProgramNo` varchar(12) NOT NULL,
  `CourseNo` varchar(12) NOT NULL,
  `CAMarks` double NOT NULL,
  `ExamMarks` double NOT NULL,
  `TotalMarks` double NOT NULL,
  `Grade` text NOT NULL,
  `Points` double NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `nkrumah-grades`
--

LOCK TABLES `nkrumah-grades` WRITE;
/*!40000 ALTER TABLE `nkrumah-grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `nkrumah-grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nkrumah-student-program-link`
--

DROP TABLE IF EXISTS `nkrumah-student-program-link`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `nkrumah-student-program-link` (
  `ID` int(11) NOT NULL auto_increment,
  `StudentID` int(11) NOT NULL,
  `ProgrammeID` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `nkrumah-student-program-link`
--

LOCK TABLES `nkrumah-student-program-link` WRITE;
/*!40000 ALTER TABLE `nkrumah-student-program-link` DISABLE KEYS */;
/*!40000 ALTER TABLE `nkrumah-student-program-link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `permissions` (
  `ID` int(11) NOT NULL auto_increment,
  `RequiredRoleMin` int(11) NOT NULL,
  `RequiredRoleMax` int(11) NOT NULL,
  `PermissionDescription` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,0,1000,'Everybody'),(2,1,1000,'Authorized users'),(3,1,10,'Students and admitting students'),(4,10,10,'Students'),(5,1,9,'Admitting students'),(6,100,999,'All staff'),(7,103,106,'Administrative staff'),(8,1000,1000,'Administrators'),(100,100,100,'Academic staff'),(101,101,101,'Library Staff'),(102,102,102,'Financial administrator'),(103,103,103,'Registry'),(104,104,104,'Department head'),(105,105,105,'Dean of school'),(106,106,106,'Academic office'),(107,107,107,'Board of Studies');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `program-course-link`
--

DROP TABLE IF EXISTS `program-course-link`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `program-course-link` (
  `ID` int(11) NOT NULL auto_increment,
  `ProgramID` int(11) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `Manditory` int(11) NOT NULL,
  `Year` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `program-course-link`
--

LOCK TABLES `program-course-link` WRITE;
/*!40000 ALTER TABLE `program-course-link` DISABLE KEYS */;
/*!40000 ALTER TABLE `program-course-link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `programmes-link`
--

DROP TABLE IF EXISTS `programmes-link`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `programmes-link` (
  `ID` int(11) NOT NULL auto_increment,
  `ProgramNo` varchar(11) NOT NULL,
  `Major` int(11) NOT NULL,
  `Minor` int(11) NOT NULL,
  `School` text NOT NULL,
  `YearOfStudy` varchar(15) NOT NULL,
  `modeofstudy` varchar(15) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `ProgramNo` (`ProgramNo`)
) ENGINE=MyISAM AUTO_INCREMENT=296 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `programmes-link`
--

LOCK TABLES `programmes-link` WRITE;
/*!40000 ALTER TABLE `programmes-link` DISABLE KEYS */;
INSERT INTO `programmes-link` VALUES (243,'ENG/RES',12,6,'20','','Full time'),(242,'CVE/RSE',1,6,'14','','Full Time'),(228,'MAT/PHY',3,2,'16','','Full time'),(227,'MAT/PES',3,7,'16','','Full time'),(226,'MAT/HIS',3,4,'16','','Full time'),(225,'MAT/GEO',3,18,'16','','Full time'),(224,'MAT SNE',3,5,'16','','Full time'),(223,'LAL/RSE',11,6,'20','','Full Time'),(222,'HIS/RSE',4,6,'14','','Full time'),(221,'HIS/CVE',4,1,'14','','Full time'),(220,'GEO/RSE',18,6,'14','','Full time'),(218,'GEO/HIS',18,4,'14','','Full time'),(219,'GEO/PES',18,7,'14','','Full time'),(217,'ENG/RSE',12,6,'20','','Full time'),(216,'ENG/PES',12,7,'20','','Full time'),(215,'ENG/LAL',12,11,'20','','Full time'),(213,'ENG/GEO',12,18,'20','','Full time'),(214,'ENG/HIS',12,4,'20','','Full time'),(212,'ENG/FRE',12,8,'20','','Full time'),(211,'ENG/CVE',12,1,'20','','Full time'),(209,'CHE/PHY',10,2,'17','','Full time'),(210,'CVE/PES',1,7,'14','','Full time'),(207,'BIO/CHE',9,10,'17','','Full time'),(208,'CHE/MAT',10,3,'17','','Full time'),(229,'MAT/SNE',3,5,'16','','Full time'),(230,'PES/ENG',7,12,'17','','Full time'),(231,'PHY/CHE',2,10,'17','','Full time'),(232,'PHY/MAT',2,3,'17','','Full time'),(233,'RSE/ENG',6,12,'14','','Full time'),(234,'RSE/HIS',6,4,'14','','Full time'),(235,'RSE/PES',6,7,'14','','Full time'),(236,'RSE/SNE',6,5,'14','','Full time'),(282,'SEN/CVE',5,1,'18','',''),(280,'GEO/ENG',18,12,'14','','Full Time'),(281,'CVE/FRE',1,8,'14','','Full Time'),(244,'SNE/RSE',5,6,'18','','Full time'),(245,'SNE/MAT',5,3,'18','','Full time'),(246,'SNE/ENG',5,12,'18','','Full time'),(247,'HIS/LAL',4,11,'14','','Full time'),(248,'SNE/LAL',5,11,'18','0','Full time'),(249,'SNE/FRE',5,8,'18','0','Full time'),(251,'RSE/CVE',6,1,'14','0','Full time'),(252,'PES/MAT',7,3,'17','','Full time'),(253,'PES/CVE',7,1,'17','','Full time'),(254,'MAT/CHE',3,10,'16','','Full time'),(255,'LAL/PES',11,7,'20','','Full time'),(256,'HIS/PES',4,7,'14','','Full time'),(257,'GEO/SNE',18,5,'14','','Full time'),(258,'GEO/MAT',18,3,'14','','Full time'),(259,'GEO/LAL',18,11,'14','','Full time'),(260,'GEO/CVE',18,1,'14','','Full time'),(261,'FRE/PES',8,7,'20','','Full time'),(262,'FRE/CVE',8,1,'20','','Full time'),(263,'CVE/LAL',1,11,'14','','Full Time'),(264,'CVE/HIS',1,4,'14','','Full Time'),(265,'BUSS',25,0,'19','','Full Time'),(266,'PES/RSE',7,6,'17','','Full Time'),(283,'SNE/CVE',5,1,'18','','Full Time'),(268,'HIS/GEO',4,18,'14','','Full Time'),(269,'LAL/HIS',11,4,'20','','Full Time'),(270,'CVE/GEO',1,18,'14','','Full Time'),(271,'SNE/GEO',5,18,'18','','Full Time'),(272,'CVE/ENG',1,12,'14','','Full Time'),(273,'HIS/MAT',4,3,'14','','FULL TIME'),(274,'CVE/SNE',1,5,'14','2010','Full Time'),(275,'HIS/SNE',4,5,'14','','Full Time'),(276,'ENG/SNE',12,5,'20','','Full Time'),(277,'SNE/HIS',5,4,'14','','Full Time'),(278,'HIS/ENG',4,12,'14','','Full Time'),(284,'LAL/GEO',11,18,'20','','Full Time'),(285,'LAL/CVE',11,1,'20','','Full Time'),(286,'SEN/HIS',5,4,'18','','Full Time'),(287,'SEN/GEO',5,18,'18','','Full Time'),(288,'SEN/RSE',5,6,'18','2010','Full Time'),(289,'SEN/ENG',5,12,'18','','Full Time'),(290,'PES/HIS',7,4,'17','',''),(291,'RSE/LAL',6,11,'14','',''),(292,'PES/LAL',7,11,'14','',''),(293,'FRE/RSE',8,6,'20','2010','DISTANCE EDUCAT'),(294,'PES/GEO',7,18,'17','2010','DISTANCE EDUCAT'),(295,'RSE/GEO',6,18,'14','','Distance');
/*!40000 ALTER TABLE `programmes-link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `programmes`
--

DROP TABLE IF EXISTS `programmes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `programmes` (
  `ID` int(11) NOT NULL auto_increment,
  `ProgramType` int(11) NOT NULL,
  `ProgramName` varchar(255) NOT NULL,
  `ProgramCoordinator` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `programmes`
--

LOCK TABLES `programmes` WRITE;
/*!40000 ALTER TABLE `programmes` DISABLE KEYS */;
INSERT INTO `programmes` VALUES (1,3,'Civic Education',2010226818),(2,3,'Physics',2010226818),(3,3,'Mathematics',2010226818),(4,3,'History',2010226818),(5,3,'Special Education',2010226818),(6,3,'Religious Studies',2010226818),(7,1,'Physical Education and Sports',2010226818),(8,1,'French',2010226818),(9,1,'Biology',2010226818),(10,1,'Chemistry',2010226818),(11,2,'Linguistics and African Languages',2010226818),(12,1,'English',2010226818),(18,2,'Geography',2010226818),(25,2,'Business Studies',2010226818),(27,4,'Education',2010226818);
/*!40000 ALTER TABLE `programmes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `roles` (
  `ID` int(11) NOT NULL auto_increment,
  `RoleName` varchar(255) NOT NULL,
  `RoleLevel` int(11) NOT NULL,
  `RoleGroup` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1012 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admitting student (1)',1,0),(2,'Admitting student (2)',2,0),(3,'Admitting student (3)',3,0),(4,'Admitting student (4)',4,0),(5,'Admitting student (5)',5,0),(6,'Admitting student (6)',6,0),(10,'Basic student',10,1),(101,'Library',100,2),(100,'Academic staff ',101,2),(102,'Financial administrator',102,2),(103,'Registry',103,2),(104,'Department head',104,2),(105,'Dean of school',105,2),(106,'Academic office',106,2),(1000,'Administrator',1000,3),(1011,'Board of Studies',107,2);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `rooms` (
  `ID` int(11) NOT NULL auto_increment,
  `AccommodationID` int(11) NOT NULL,
  `RoomType` varchar(255) NOT NULL,
  `RoomNumber` int(11) NOT NULL,
  `RoomCapacity` int(11) NOT NULL,
  `RoomPrice` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,1,'Twin room',10,2,1200);
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `schools` (
  `ID` int(11) NOT NULL auto_increment,
  `ParentID` int(11) NOT NULL,
  `Established` date NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Dean` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `schools`
--

LOCK TABLES `schools` WRITE;
/*!40000 ALTER TABLE `schools` DISABLE KEYS */;
INSERT INTO `schools` VALUES (14,0,'2013-10-21','Social Sciences','',2010222126),(16,0,'0000-00-00','Mathematics and Statistics','',2010222126),(17,0,'0000-00-00','Natural Sciences','',2010222126),(18,0,'0000-00-00','Education','',2010222126),(19,0,'0000-00-00','School of Business Studies','',2010222126),(20,0,'0000-00-00','Linguistics and Languages','',2010222126),(21,0,'2013-10-21','Business Studiesd','',2010222164);
/*!40000 ALTER TABLE `schools` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `settings` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL,
  `Value` varchar(255) NOT NULL,
  `ModifierID` int(11) NOT NULL,
  `ModifierDate` datetime NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (2,'InstitutionName','Edurole',2010222117,'2013-03-03 00:00:00'),(3,'AdmissionLevel1','Complete online application form',0,'0000-00-00 00:00:00'),(4,'AdmissionLevel2','Have personal information verified',0,'0000-00-00 00:00:00'),(5,'AdmissionLevel3','Have education history verified',0,'0000-00-00 00:00:00'),(6,'AdmissionLevel4','Complete payment',0,'0000-00-00 00:00:00'),(7,'AdmissionLevel5','Receive admission approval',0,'0000-00-00 00:00:00'),(8,'AdmissionLevel6','Complete course registration',0,'0000-00-00 00:00:00'),(9,'InstitutionWebsite','www.edurole.com',0,'2013-03-08 00:00:00'),(10,'PaymentType1','Boarding (GRZ)',0,'0000-00-00 00:00:00'),(11,'PaymentType2','Boarding (PVT)',0,'0000-00-00 00:00:00'),(12,'PaymentType3','Day Scholar (GRZ)',0,'0000-00-00 00:00:00'),(13,'PaymentType4','Day Scholar (PVT)',0,'0000-00-00 00:00:00');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student-program-link`
--

DROP TABLE IF EXISTS `student-program-link`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `student-program-link` (
  `ID` int(10) NOT NULL auto_increment,
  `StudentID` int(20) NOT NULL,
  `Major` varchar(40) NOT NULL,
  `Minor` varchar(40) NOT NULL,
  `DateOfEnrollment` date NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `student-program-link`
--

LOCK TABLES `student-program-link` WRITE;
/*!40000 ALTER TABLE `student-program-link` DISABLE KEYS */;
/*!40000 ALTER TABLE `student-program-link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student-study-link`
--

DROP TABLE IF EXISTS `student-study-link`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `student-study-link` (
  `ID` int(11) NOT NULL auto_increment,
  `StudentID` int(11) NOT NULL,
  `StudyID` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `student-study-link`
--

LOCK TABLES `student-study-link` WRITE;
/*!40000 ALTER TABLE `student-study-link` DISABLE KEYS */;
/*!40000 ALTER TABLE `student-study-link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study-program-link`
--

DROP TABLE IF EXISTS `study-program-link`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `study-program-link` (
  `ID` int(11) NOT NULL auto_increment,
  `StudyID` int(11) NOT NULL,
  `ProgramID` int(11) NOT NULL,
  `Manditory` int(11) NOT NULL,
  `Year` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `study-program-link`
--

LOCK TABLES `study-program-link` WRITE;
/*!40000 ALTER TABLE `study-program-link` DISABLE KEYS */;
INSERT INTO `study-program-link` VALUES (1,1,4,0,1),(2,1,11,1,3),(3,1,7,0,2),(4,61,10,0,1),(5,2,10,0,1),(6,2,4,1,4),(7,2,5,1,3),(8,70,25,0,0),(9,64,1,0,0),(10,64,4,0,0),(11,64,5,0,0),(12,64,6,0,0),(13,64,7,0,0),(14,64,8,0,0),(15,64,11,0,0),(16,64,12,0,0),(17,64,18,0,0),(18,64,27,0,0),(19,66,1,0,0),(20,66,3,0,0),(21,66,4,0,0),(22,66,5,0,0),(23,66,6,0,0),(24,66,7,0,0),(25,66,8,0,0),(26,66,11,0,0),(27,66,12,0,0),(28,66,18,0,0),(29,66,27,0,0),(30,70,27,0,0),(31,67,1,0,0),(32,67,3,0,0),(33,67,4,0,0),(34,67,5,0,0),(35,67,6,0,0),(36,67,7,0,0),(37,67,8,0,0),(38,67,11,0,0),(39,67,12,0,0),(40,67,18,0,0),(41,67,27,0,0),(42,68,2,0,0),(43,68,3,0,0),(44,68,10,0,0),(45,68,27,0,0),(46,69,1,0,0),(47,69,4,0,0),(48,69,5,0,0),(49,69,6,0,0),(50,69,7,0,0),(51,69,8,0,0),(52,69,11,0,0),(53,69,12,0,0),(54,69,18,0,0),(55,69,27,0,0);
/*!40000 ALTER TABLE `study-program-link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study`
--

DROP TABLE IF EXISTS `study`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `study` (
  `ID` int(11) NOT NULL auto_increment,
  `ParentID` int(11) NOT NULL,
  `IntakeStart` date NOT NULL,
  `IntakeEnd` date NOT NULL,
  `Delivery` int(11) NOT NULL,
  `IntakeMax` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `ShortName` varchar(255) NOT NULL,
  `Active` int(11) NOT NULL,
  `StudyType` int(11) NOT NULL,
  `TimeBlocks` int(11) NOT NULL,
  `StudyIntensity` int(11) NOT NULL,
  `ProgrammesAvailable` text NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `study`
--

LOCK TABLES `study` WRITE;
/*!40000 ALTER TABLE `study` DISABLE KEYS */;
INSERT INTO `study` VALUES (1,5,'2012-07-22','2014-02-01',1,0,'Bachelor of Education in Civic Education','B.Ed Civic Education',0,11,48,0,''),(2,5,'2012-07-22','2014-02-01',1,200,'Bachelor of Education in Mathematics','B.Ed Mathematics',1,11,48,1,''),(61,7,'2013-01-15','2014-02-01',1,0,'Master of Science in Information Technology ','Msc. Inf. Tech.',0,2,24,0,''),(62,5,'2013-01-02','2014-02-01',3,0,'Bachelor of Social Science','Bsc. Social Sc.',1,3,48,0,''),(63,0,'2013-09-11','2013-02-11',3,0,'Bachelor of Education in Civic Education','B.Ed Civ. Ed.',0,1,48,0,''),(64,14,'2014-01-11','2014-10-29',0,0,'Bachelor of Education in Civic Education','B.Ed Civ. Ed.',0,0,12,0,''),(66,14,'2013-09-11','2013-10-11',4,0,'Bachelor of Education in Geography','B.Ed GEO',1,0,48,0,''),(67,14,'2013-09-15','2013-10-25',0,0,'Bachelor of Education in History','B.Ed History',0,0,48,0,''),(68,14,'2013-09-11','2014-12-11',0,0,'Bachelor of Education in Mathematics','B.Ed Maths.',0,0,12,0,''),(69,14,'2014-01-15','2014-10-25',0,0,'Bachelor of Education in Religious Studies','B.Ed R.E',0,0,12,0,''),(70,14,'2013-09-15','2013-10-25',0,0,'Bachelor of Bussines Studies with Education','B. BS',0,0,12,0,'');
/*!40000 ALTER TABLE `study` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `transactions` (
  `ID` int(11) NOT NULL auto_increment,
  `UID` int(11) NOT NULL,
  `RequestID` varchar(64) NOT NULL,
  `TransactionID` varchar(64) NOT NULL,
  `StudentID` int(16) NOT NULL,
  `NRC` varchar(32) NOT NULL,
  `TransactionDate` varchar(32) NOT NULL,
  `Amount` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Type` varchar(4) NOT NULL,
  `Hash` varchar(128) NOT NULL,
  `Timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Phone` varchar(16) NOT NULL,
  `Status` varchar(64) NOT NULL,
  `Error` varchar(64) NOT NULL,
  `Data` longtext NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translation`
--

DROP TABLE IF EXISTS `translation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `translation` (
  `ID` int(11) NOT NULL auto_increment,
  `LanguageID` int(11) NOT NULL,
  `Phrase` text NOT NULL,
  `TranslatedPhrase` text NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `translation`
--

LOCK TABLES `translation` WRITE;
/*!40000 ALTER TABLE `translation` DISABLE KEYS */;
INSERT INTO `translation` VALUES (33,0,'Welcome',''),(8,0,'Employee number',''),(9,0,'Current role',''),(10,0,'Selected Template',''),(11,0,'News and updates',''),(12,0,'Please take the time to enter your profile information first, you can do this <a href=\'/dev/edurole/information/edit/personal\'>here</a>.',''),(13,0,'Total students',''),(14,0,'Fulltime students',''),(15,0,'Distance students',''),(16,0,'Part-time students',''),(17,0,'Currently in admission',''),(18,0,'Search by student number',''),(19,0,'Enter student number',''),(20,0,'Open Record',''),(21,0,'Search by Name',''),(22,0,'Enter students first name',''),(23,0,'and/or surname',''),(24,0,'Show as',''),(25,0,'List of Students',''),(26,0,'Profile View',''),(27,0,'Search Records',''),(28,0,'View students by study',''),(29,0,'Show all students from',''),(30,0,'View Records',''),(31,0,'View students by programme',''),(32,0,'View students by course',''),(34,0,'The user account has been updated','');
/*!40000 ALTER TABLE `translation` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-04-02  9:52:50
