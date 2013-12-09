SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `access` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) NOT NULL,
  `RoleID` int(11) NOT NULL,
  `Password` varchar(128) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2010222144 ;

INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES
(100001, 'admin', 1000, '');

CREATE TABLE IF NOT EXISTS `assignments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CreatorID` int(11) NOT NULL,
  `AssignmentName` varchar(255) NOT NULL,
  `AssignmentDescription` text NOT NULL,
  `CourseID` int(11) NOT NULL,
  `ProgrammeID` int(11) NOT NULL,
  `StudyID` int(11) NOT NULL,
  `AssignmentWeight` int(11) NOT NULL,
  `UploadNeeded` int(11) NOT NULL,
  `AssignmentFiles` text NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `SubmissionDeadline` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `basic-information` (
  `FirstName` varchar(35) NOT NULL,
  `MiddleName` varchar(35) NOT NULL,
  `Surname` varchar(35) NOT NULL,
  `Sex` enum('Male','Female','Unknown') NOT NULL,
  `ID` int(9) NOT NULL AUTO_INCREMENT,
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
  `DissabilityType` varchar(40) NOT NULL,
  `PrivateEmail` varchar(100) NOT NULL,
  `MaritalStatus` enum('Single','Married','Divorced','Widowed') NOT NULL,
  `StudyType` enum('Fulltime','Partime','Distance') NOT NULL,
  `Status` enum('Enrolled','Rejected','Employed','Retired','Fired','Requesting','Graduated','Suspended','Dismissed','Deceased','Failed') NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `GovernmentID` (`GovernmentID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2010222144 ;

INSERT INTO `basic-information` (`FirstName`, `MiddleName`, `Surname`, `Sex`, `ID`, `GovernmentID`, `DateOfBirth`, `PlaceOfBirth`, `Nationality`, `StreetName`, `PostalCode`, `Town`, `Country`, `HomePhone`, `MobilePhone`, `Disability`, `DissabilityType`, `PrivateEmail`, `MaritalStatus`, `StudyType`, `Status`) VALUES
('John', '', 'Doe', 'Male', 100001, '123456789', '1985-04-10', 'Sarisota', 'American', 'Bayou drive', '10032', 'Sarisota, Florida', 'America', '555-245-123', '', 'No', '', 'j.doe@example.com', 'Married', '', 'Employed');

CREATE TABLE IF NOT EXISTS `calendar` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CoursecalID` int(11) NOT NULL,
  `StartTime` int(64) NOT NULL,
  `EndTime` int(64) NOT NULL,
  `Description` text NOT NULL,
  `Location` text NOT NULL,
  `Summary` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

CREATE TABLE IF NOT EXISTS `content` (
  `ContentID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Content` text NOT NULL,
  `ContentCat` enum('static','help','news','notification','update','article','image') NOT NULL,
  PRIMARY KEY (`ContentID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=106 ;

INSERT INTO `content` (`ContentID`, `Name`, `Content`, `ContentCat`) VALUES
(1, 'Welcome to EduRole', '<p>EduRole is the free academic support system that encompases:\n<li>Student information management</li>\n<li>Online student registration</li>\n<li>Student communication services (email)</li>\n<li>Centralized authentication</li>\n</p>', 'static'),
(2, 'Online student registration (left container login page)', '<h2><strong>ONLINE STUDENT REGISTRATION</strong></h2>\n<p>Trough the easy online registration form you can now complete your request for admission online. Click on the link below to view the programs for which intake is currently open.</p>\n<p><a href="admission.php"><strong> View current intake possibilities</strong></a></p>', 'static'),
(100, 'EduRole starts trails in Aruba', 'Aruba''s educational system is patterned after the Dutch system of education. There are 68 schools for primary education, 12 schools for secondary education, and 5 universities in Aruba. </br></br> As part of a development initiative starting early 2013 Edurole will be deployed in two secondary schools. More news to follow.', 'news'),
(101, 'EduRole helps universities in Africa', 'Edurole started out as a small functional service access platform at Nkrumah University in Kabwe Zambia. It has since grown out to a full product capable of easy centralization of web services for institutions like Nkrumah. <br /> <br /> With the success of Nkrumah our goal is to expand Eduroles reach in developing countries such as Zambia by keeping Edurole a free product for non-profit institutions.', 'news'),
(3, 'Information on using Edurole', '<p>Edurole works with user roles to assign privileges and permissions to students and staff. Each user account has a role assigned, the following roles are currently defined in the sytem:\n\n<li>Enrolling student</li>\n<li>Active Student</li>\n<li>Academic Staff</li>\n<li>Registry</li>\n<li>Library</li>\n<li>Financial manager</li>\n<li>Academic office</li>\n<li>Administrator</li>\n\n</p>', 'static'),
(102, 'Using the online admission system', 'Edurole started out as a small functional service access platform at Nkrumah University in Kabwe Zambia. It has since grown out to a full product capable of easy centralization of web services for institutions like Nkrumah. <br /> <br /> With the success of Nkrumah our goal is to expand Eduroles reach in developing countries such as Zambia by keeping Edurole a free product for non-profit institutions.', 'help'),
(103, 'Creating user accounts', 'Edurole started out as a small functional service access platform at Nkrumah University in Kabwe Zambia. It has since grown out to a full product capable of easy centralization of web services for institutions like Nkrumah. <br /> <br /> With the success of Nkrumah our goal is to expand Eduroles reach in developing countries such as Zambia by keeping Edurole a free product for non-profit institutions.', 'help'),
(104, 'Managing intake (enrollment)', 'Edurole started out as a small functional service access platform at Nkrumah University in Kabwe Zambia. It has since grown out to a full product capable of easy centralization of web services for institutions like Nkrumah. <br /> <br /> With the success of Nkrumah our goal is to expand Eduroles reach in developing countries such as Zambia by keeping Edurole a free product for non-profit institutions.', 'help'),
(105, 'Using your university email ', 'Edurole started out as a small functional service access platform at Nkrumah University in Kabwe Zambia. It has since grown out to a full product capable of easy centralization of web services for institutions like Nkrumah. <br /> <br /> With the success of Nkrumah our goal is to expand Eduroles reach in developing countries such as Zambia by keeping Edurole a free product for non-profit institutions.', 'help');

CREATE TABLE IF NOT EXISTS `coursecal` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CourseName` varchar(255) NOT NULL,
  `UploadDate` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

CREATE TABLE IF NOT EXISTS `courses` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ProgramType` varchar(50) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `CourseCoordinator` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=356 ;

CREATE TABLE IF NOT EXISTS `education-background` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `StudentID` int(9) NOT NULL,
  `CertificateName` varchar(50) NOT NULL,
  `TypeOfCertificate` varchar(50) NOT NULL,
  `InstitutionName` varchar(50) NOT NULL,
  `DocumentName` varchar(128) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8290 ;

CREATE TABLE IF NOT EXISTS `emergency-contact` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `StudentID` int(9) NOT NULL,
  `FullName` varchar(70) NOT NULL,
  `Relationship` varchar(50) NOT NULL,
  `PhoneNumber` int(30) NOT NULL,
  `Street` varchar(40) NOT NULL,
  `Town` varchar(40) NOT NULL,
  `Postalcode` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3743 ;

CREATE TABLE IF NOT EXISTS `gradebook` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `GlobalHash` varchar(129) NOT NULL,
  `Owner` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  `DateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Course` int(11) NOT NULL,
  `ValidatorID` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ValidatorID` (`ValidatorID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

CREATE TABLE IF NOT EXISTS `grades` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `StudentID` int(11) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `Grade` varchar(30) NOT NULL,
  `Datestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `GradeHash` varchar(32) NOT NULL,
  `CreatorID` int(11) NOT NULL,
  `MarkType` int(11) NOT NULL,
  `BatchID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=614 ;

CREATE TABLE IF NOT EXISTS `hostel` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `HostelName` varchar(255) NOT NULL,
  `TotalRooms` int(11) NOT NULL,
  `Type` int(11) NOT NULL,
  `Category` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

CREATE TABLE IF NOT EXISTS `library` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `StudentID` int(11) NOT NULL,
  `AuthorName` varchar(100) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `DueDate` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `messages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SenderID` int(11) NOT NULL,
  `RecipientID` int(11) NOT NULL,
  `BroadcastGroup` int(11) NOT NULL,
  `Subject` text NOT NULL,
  `Content` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `page-segment` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SegmentName` varchar(255) NOT NULL,
  `SegmentParent` int(11) NOT NULL,
  `SegmentRequiredPermission` varchar(100) NOT NULL,
  `SegmentPosition` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

INSERT INTO `page-segment` (`ID`, `SegmentName`, `SegmentParent`, `SegmentRequiredPermission`, `SegmentPosition`) VALUES
(0, 'Main', 0, '1', 0),
(3, 'Admission menu', 0, '5', 1),
(4, 'Registry', 0, '21', 4),
(5, 'Academic Office', 0, '17', 5),
(6, 'Library', 0, '19', 6),
(7, 'Academic staff', 0, '18', 3),
(8, 'Student', 0, '4', 2),
(9, 'Virtual Learning Environment', 0, '4', 10);

CREATE TABLE IF NOT EXISTS `pages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PageRoute` varchar(128) NOT NULL,
  `PageRequiredPermission` varchar(100) NOT NULL,
  `PageName` varchar(255) NOT NULL,
  `PageSegmentID` int(11) NOT NULL,
  `PagePosition` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1002 ;

INSERT INTO `pages` (`ID`, `PageRoute`, `PageRequiredPermission`, `PageName`, `PageSegmentID`, `PagePosition`) VALUES
(1, 'calendar', '2', 'Personalized Calendar', 0, 3),
(1000, 'logout', '2', 'Logout', 0, 100),
(100, 'home', '1', 'Home', 0, 0),
(4, 'password/change', '2', 'Change Password', 0, 90),
(5, 'admission', '3', 'Follow admission progress', 3, 0),
(6, 'information/view/personal', '2', 'View Personal Information', 0, 1),
(7, 'grades/view-grades', '4', 'View Grades', 8, 1),
(9, 'grades/selectcourse', '100', 'Submit results', 5, 1),
(10, 'library/registry', '101', 'Library holdings', 6, 1),
(11, 'library/registry', '101', 'Loan registry', 6, 1),
(12, 'library/overdue', '101', 'Overdue Books', 6, 1),
(13, 'library/loan-records', '101', 'Manage loan records', 6, 1),
(14, 'admission/managment', '103', 'Admission management', 4, 1),
(15, 'users/students/management', '103', 'Manage student information', 4, 1),
(16, 'view-information/student', '103', 'Search student Information', 4, 1),
(17, 'users/students/management/correct', '103', 'Correct student records', 4, 1),
(18, 'users/students/register', '103', 'Register student', 4, 1),
(19, 'information/students', '106', 'View student information', 5, 1),
(20, 'institution/management', '106', 'Institution settings', 5, 1),
(21, 'schools/management', '106', '> Manage schools', 5, 1),
(22, 'studies/management', '106', '> > Manage studies', 5, 1),
(23, 'programmes/management', '106', '> > > Manage programmes', 5, 1),
(24, 'courses/management', '106', '> > > Manage courses', 5, 1),
(25, 'users/management', '106', 'Manage users', 5, 1),
(26, 'grades/management', '106', 'Results management', 5, 1),
(28, 'assignments/view', '4', 'View all assignments', 9, 1),
(29, 'file/managment', '4', 'Personal file manager', 9, 1),
(1001, 'mail/view', '2', 'mail', 0, 5);

CREATE TABLE IF NOT EXISTS `payments` (
  `ReceiptID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `PaymentType` varchar(30) NOT NULL,
  `Payed` enum('Yes','No') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `permission-link` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PermissionsRangeID` int(11) NOT NULL,
  `PermissionID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

INSERT INTO `permission-link` (`ID`, `PermissionsRangeID`, `PermissionID`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 5),
(6, 6, 6),
(7, 7, 7),
(8, 8, 8),
(9, 8, 1),
(10, 8, 2),
(11, 8, 3),
(12, 8, 4),
(13, 8, 5),
(14, 8, 6),
(15, 8, 7),
(16, 8, 8),
(17, 106, 106),
(18, 100, 100),
(19, 101, 101),
(20, 102, 102),
(21, 103, 103),
(22, 104, 104),
(23, 105, 105);

CREATE TABLE IF NOT EXISTS `permissions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RequiredRoleMin` int(11) NOT NULL,
  `RequiredRoleMax` int(11) NOT NULL,
  `PermissionDescription` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=107 ;

INSERT INTO `permissions` (`ID`, `RequiredRoleMin`, `RequiredRoleMax`, `PermissionDescription`) VALUES
(1, 0, 1000, 'Everybody'),
(2, 1, 1000, 'Authorized users'),
(3, 1, 10, 'Students and admitting students'),
(4, 10, 10, 'Students'),
(5, 1, 9, 'Admitting students'),
(6, 100, 999, 'All staff'),
(7, 103, 106, 'Administrative staff'),
(8, 1000, 1000, 'Administrators'),
(100, 100, 100, 'Academic staff only'),
(101, 101, 101, 'Library Staff Only'),
(102, 102, 102, 'Financial administrator'),
(103, 103, 103, 'Registry'),
(104, 104, 104, 'Department head'),
(105, 105, 105, 'Dean of school'),
(106, 106, 106, 'Academic office');

CREATE TABLE IF NOT EXISTS `program-course-link` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ProgramID` int(11) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `Manditory` int(11) NOT NULL,
  `Year` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=810 ;

CREATE TABLE IF NOT EXISTS `programmes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ProgramType` int(11) NOT NULL,
  `ProgramName` varchar(255) NOT NULL,
  `ProgramCoordinator` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

CREATE TABLE IF NOT EXISTS `roles` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RoleName` varchar(255) NOT NULL,
  `RoleLevel` int(11) NOT NULL,
  `RoleGroup` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1011 ;

INSERT INTO `roles` (`ID`, `RoleName`, `RoleLevel`, `RoleGroup`) VALUES
(1, 'Admitting student (1)', 1, 0),
(2, 'Admitting student (2)', 2, 0),
(3, 'Admitting student (3)', 3, 0),
(4, 'Admitting student (4)', 4, 0),
(5, 'Admitting student (5)', 5, 0),
(6, 'Admitting student (6)', 6, 0),
(10, 'Basic student', 10, 1),
(101, 'Library', 100, 2),
(100, 'Academic staff ', 101, 2),
(102, 'Financial administrator', 102, 2),
(103, 'Registry', 103, 2),
(104, 'Department head', 104, 2),
(105, 'Dean of school', 105, 2),
(106, 'Academic office', 106, 2),
(1000, 'Administrator', 1000, 3);

CREATE TABLE IF NOT EXISTS `schools` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ParentID` int(11) NOT NULL,
  `Established` date NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Dean` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

CREATE TABLE IF NOT EXISTS `settings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Value` varchar(255) NOT NULL,
  `ModifierID` int(11) NOT NULL,
  `ModifierDate` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

INSERT INTO `settings` (`ID`, `Name`, `Value`, `ModifierID`, `ModifierDate`) VALUES
(2, 'InstitutionName', 'Edurole', 2010222117, '2013-03-03 00:00:00'),
(3, 'AdmissionLevel1', 'Complete online application form', 0, '0000-00-00 00:00:00'),
(4, 'AdmissionLevel2', 'Have personal information verified', 0, '0000-00-00 00:00:00'),
(5, 'AdmissionLevel3', 'Have education history verified', 0, '0000-00-00 00:00:00'),
(6, 'AdmissionLevel4', 'Complete payment', 0, '0000-00-00 00:00:00'),
(7, 'AdmissionLevel5', 'Receive admission approval', 0, '0000-00-00 00:00:00'),
(8, 'AdmissionLevel6', 'Complete course registration', 0, '0000-00-00 00:00:00'),
(9, 'InstitutionWebsite', 'www.edurole.com', 0, '2013-03-08 00:00:00'),
(10, 'PaymentType1', 'Boarding (GRZ)', 0, '0000-00-00 00:00:00'),
(11, 'PaymentType2', 'Boarding (PVT)', 0, '0000-00-00 00:00:00'),
(12, 'PaymentType3', 'Day Scholar (GRZ)', 0, '0000-00-00 00:00:00'),
(13, 'PaymentType4', 'Day Scholar (PVT)', 0, '0000-00-00 00:00:00');

CREATE TABLE IF NOT EXISTS `student-program-link` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `StudentID` int(9) NOT NULL,
  `Major` varchar(40) NOT NULL,
  `Minor` varchar(40) NOT NULL,
  `DateOfEnrollment` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4854 ;

CREATE TABLE IF NOT EXISTS `student-study-link` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `StudentID` int(11) NOT NULL,
  `StudyID` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;

CREATE TABLE IF NOT EXISTS `study` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=63 ;

CREATE TABLE IF NOT EXISTS `study-program-link` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `StudyID` int(11) NOT NULL,
  `ProgramID` int(11) NOT NULL,
  `Manditory` int(11) NOT NULL,
  `Year` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

CREATE TABLE IF NOT EXISTS `transactions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `TransactionType` enum('INSERTGRADES','MODIFY','DELETE','LOGIN','VIEW') NOT NULL,
  `Data` longtext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
