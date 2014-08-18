#
# carmina.sql Database Schema
#
# Use this schema for creating your database for
# a new installation of dotProject.
#
# Changed: Add in a dbprefix string to be replaced with the actual db table prefix
# Changed: Remove explicit mySQL table type which then allows for being able to use other
#			database engines

CREATE TABLE `%dbprefix%login_header` (
  `login_header_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) COLLATE latin1_general_ci NOT NULL,
  `added_date` datetime DEFAULT NULL,
  `counter` int(2) DEFAULT NULL,
  `ip_address` varchar(15) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`login_header_id`),
  KEY `sessionid` (`session_id`)
) ;

CREATE TABLE `%dbprefix%phpbrowscap` (
  `browser_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) COLLATE latin1_general_ci DEFAULT NULL,
  `browser_name` mediumtext COLLATE latin1_general_ci,
  `browser_name_regex` mediumtext COLLATE latin1_general_ci,
  `browser_name_pattern` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `parent` varchar(128) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `comment` varchar(14) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `browser` varchar(96) COLLATE latin1_general_ci DEFAULT NULL,
  `version` varchar(96) COLLATE latin1_general_ci DEFAULT NULL,
  `majorver` varchar(128) COLLATE latin1_general_ci DEFAULT NULL,
  `platform` varchar(15) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `frames` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `iframes` varchar(128) COLLATE latin1_general_ci DEFAULT '',
  `tables` varchar(15) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `cookies` varchar(14) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `javascript` varchar(14) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `javaapplets` varchar(96) COLLATE latin1_general_ci DEFAULT NULL,
  `cssversion` varchar(96) COLLATE latin1_general_ci DEFAULT NULL,
  `device_name` varchar(128) COLLATE latin1_general_ci DEFAULT NULL,
  `device_maker` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `device_type` varchar(64) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `device_pointing_method` varchar(20) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `renderingengine_name` varchar(128) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `renderingengine_version` varchar(15) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `browser_type` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `minorver` int(11) DEFAULT NULL,
  `platform_version` varchar(14) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `platform_description` varchar(96) COLLATE latin1_general_ci DEFAULT NULL,
  `alpha` varchar(96) COLLATE latin1_general_ci DEFAULT NULL,
  `beta` varchar(128) COLLATE latin1_general_ci DEFAULT NULL,
  `win16` varchar(128) COLLATE latin1_general_ci DEFAULT NULL,
  `win32` varchar(14) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `win64` varchar(64) COLLATE latin1_general_ci DEFAULT '',
  `backgroundsounds` varchar(128) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `vbscript` varchar(15) COLLATE latin1_general_ci DEFAULT '',
  `activexcontrols` varchar(14) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `ismobiledevice` varchar(14) COLLATE latin1_general_ci DEFAULT '',
  `issyndicationreader` varchar(96) COLLATE latin1_general_ci DEFAULT NULL,
  `crawler` varchar(96) COLLATE latin1_general_ci DEFAULT NULL,
  `aolversion` varchar(128) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`browser_id`),
  KEY `sessionid` (`session_id`)
) ;

ALTER TABLE `%dbprefix%user_access_log` (

) ;

CREATE TABLE `%dbprefix%user_access_log` (
`user_access_log_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`user_id` INT( 10 ) UNSIGNED NOT NULL ,
`user_ip` VARCHAR( 15 ) NOT NULL ,
`date_time_in` DATETIME DEFAULT '0000-00-00 00:00:00',
`date_time_out` DATETIME DEFAULT '0000-00-00 00:00:00',
`date_time_last_action` DATETIME DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY ( `user_access_log_id` )
);
--
-- Table structure for table `dotp_user_access_log`
--

DROP TABLE IF EXISTS `dotp_user_access_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dotp_user_access_log` (
  `user_access_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
 * `session_id` varchar(40) NOT NULL,
  `user_ip` varchar(15) NOT NULL,
 * `last_page_url` varchar(128) NOT NULL,
 * `browser_language` varchar(128) DEFAULT NULL,
  `date_time_in` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_out` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_last_action` datetime DEFAULT '0000-00-00 00:00:00',
 * `ismobiledevice` tinyint(1) DEFAULT NULL,
  *`referer` varchar(128) DEFAULT NULL,
  *`crawler` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`user_access_log_id`),
  KEY `session_id` (`session_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22093 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

