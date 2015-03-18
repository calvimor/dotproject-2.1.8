CREATE TABLE `%dbprefix%billingcode` (
  `billingcode_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `billingcode_name` varchar(25) NOT NULL DEFAULT '',
  `billingcode_value` float NOT NULL DEFAULT '0',
  `billingcode_desc` varchar(255) NOT NULL DEFAULT '',
  `billingcode_status` int(1) NOT NULL DEFAULT '0',
  `company_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`billingcode_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%common_notes` (
  `note_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `note_author` int(10) unsigned NOT NULL DEFAULT '0',
  `note_module` int(10) unsigned NOT NULL DEFAULT '0',
  `note_record_id` int(10) unsigned NOT NULL DEFAULT '0',
  `note_category` int(3) unsigned NOT NULL DEFAULT '0',
  `note_title` varchar(100) NOT NULL DEFAULT '',
  `note_body` text NOT NULL,
  `note_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `note_hours` float NOT NULL DEFAULT '0',
  `note_code` varchar(8) NOT NULL DEFAULT '',
  `note_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `note_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note_modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`note_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%companies` (
  `company_id` int(10) NOT NULL AUTO_INCREMENT,
  `company_module` int(10) NOT NULL DEFAULT '0',
  `company_name` varchar(100) DEFAULT '',
  `company_phone1` varchar(30) DEFAULT '',
  `company_phone2` varchar(30) DEFAULT '',
  `company_fax` varchar(30) DEFAULT '',
  `company_address1` varchar(50) DEFAULT '',
  `company_address2` varchar(50) DEFAULT '',
  `company_city` varchar(30) DEFAULT '',
  `company_state` varchar(30) DEFAULT '',
  `company_zip` varchar(11) DEFAULT '',
  `company_primary_url` varchar(255) DEFAULT '',
  `company_owner` int(11) NOT NULL DEFAULT '0',
  `company_description` text,
  `company_type` int(3) NOT NULL DEFAULT '0',
  `company_email` varchar(255) DEFAULT NULL,
  `company_custom` longtext,
  PRIMARY KEY (`company_id`),
  KEY `idx_cpy1` (`company_owner`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) NOT NULL DEFAULT '',
  `config_value` varchar(255) NOT NULL DEFAULT '',
  `config_group` varchar(255) NOT NULL DEFAULT '',
  `config_type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `config_name` (`config_name`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%config` VALUES (1,'host_locale','en','','text'),(2,'check_overallocation','false','','checkbox'),(3,'currency_symbol','$','','text'),(4,'host_style','default','','text'),(5,'company_name','','','text'),(6,'page_title','PPK Lab PM','','text'),(7,'site_domain','192.168.0.90:8010/dproject','','text'),(8,'email_prefix','[randd]','','text'),(9,'admin_username','admin','','text'),(10,'username_min_len','4','','text'),(11,'password_min_len','4','','text'),(12,'enable_gantt_charts','true','','checkbox'),(13,'log_changes','false','','checkbox'),(14,'check_task_dates','true','','checkbox'),(15,'check_task_empty_dynamic','false','','checkbox'),(16,'locale_warn','false','','checkbox'),(17,'locale_alert','^','','text'),(18,'daily_working_hours','8','','text'),(19,'display_debug','false','','checkbox'),(20,'link_tickets_kludge','false','','checkbox'),(21,'show_all_task_assignees','false','','checkbox'),(22,'direct_edit_assignment','false','','checkbox'),(23,'restrict_color_selection','false','','checkbox'),(24,'cal_day_view_show_minical','true','','checkbox'),(25,'cal_day_start','5','','text'),(26,'cal_day_end','22','','text'),(27,'cal_day_increment','30','','text'),(28,'cal_working_days','1,2,3,4,5,6','','text'),(29,'restrict_task_time_editing','false','','checkbox'),(30,'default_view_m','calendar','','text'),(31,'default_view_a','day_view','','text'),(32,'default_view_tab','1','','text'),(33,'index_max_file_size','-1','','text'),(34,'session_handling','php','session','select'),(35,'session_idle_time','2h','session','text'),(36,'session_max_lifetime','7d','session','text'),(37,'debug','1','','text'),(38,'parser_default','/usr/bin/strings','','text'),(39,'parser_application/msword','/usr/bin/strings','','text'),(40,'parser_text/html','/usr/bin/strings','','text'),(41,'parser_application/pdf','/usr/bin/pdftotext','','text'),(42,'files_ci_preserve_attr','true','','checkbox'),(43,'files_show_versions_edit','false','','checkbox'),(44,'auth_method','sql','auth','select'),(45,'ldap_host','localhost','ldap','text'),(46,'ldap_port','389','ldap','text'),(47,'ldap_version','3','ldap','text'),(48,'ldap_base_dn','dc=saki,dc=com,dc=au','ldap','text'),(49,'ldap_user_filter','(uid=%USERNAME%)','ldap','text'),(50,'postnuke_allow_login','true','auth','checkbox'),(51,'reset_memory_limit','32M','','text'),(52,'mail_transport','php','mail','select'),(53,'mail_host','smtp.randd','mail','text'),(54,'mail_port','25','mail','text'),(55,'mail_auth','false','mail','checkbox'),(56,'mail_user','admin@ppkl.randd.net','mail','text'),(57,'mail_pass','CdSNGC5866*','mail','password'),(58,'mail_defer','false','mail','checkbox'),(59,'mail_timeout','30','mail','text'),(60,'session_gc_scan_queue','false','session','checkbox'),(61,'task_reminder_control','false','task_reminder','checkbox'),(62,'task_reminder_days_before','1','task_reminder','text'),(63,'task_reminder_repeat','100','task_reminder','text'),(64,'gacl_cache','true','gacl','checkbox'),(65,'gacl_expire','true','gacl','checkbox'),(66,'gacl_cache_dir','/tmp','gacl','text'),(67,'gacl_timeout','600','gacl','text'),(68,'mail_smtp_tls','true','mail','checkbox'),(69,'ldap_search_user','Manager','ldap','text'),(70,'ldap_search_pass','secret','ldap','password'),(71,'ldap_allow_login','true','ldap','checkbox'),(72,'user_contact_inactivate','true','auth','checkbox'),(73,'user_contact_activate','false','auth','checkbox');


CREATE TABLE `%dbprefix%config_list` (
  `config_list_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_id` int(11) NOT NULL DEFAULT '0',
  `config_list_name` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`config_list_id`),
  KEY `config_id` (`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%config_list` VALUES (1,44,'sql'),(2,44,'ldap'),(3,44,'pn'),(4,34,'app'),(5,34,'php'),(6,52,'php'),(7,52,'smtp');


CREATE TABLE `%dbprefix%contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_first_name` varchar(30) DEFAULT NULL,
  `contact_last_name` varchar(30) DEFAULT NULL,
  `contact_order_by` varchar(30) NOT NULL DEFAULT '',
  `contact_title` varchar(50) DEFAULT NULL,
  `contact_birthday` date DEFAULT NULL,
  `contact_job` varchar(255) DEFAULT NULL,
  `contact_company` varchar(100) NOT NULL DEFAULT '',
  `contact_department` tinytext,
  `contact_type` varchar(20) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_email2` varchar(255) DEFAULT NULL,
  `contact_url` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `contact_phone2` varchar(30) DEFAULT NULL,
  `contact_fax` varchar(30) DEFAULT NULL,
  `contact_mobile` varchar(30) DEFAULT NULL,
  `contact_address1` varchar(60) DEFAULT NULL,
  `contact_address2` varchar(60) DEFAULT NULL,
  `contact_city` varchar(30) DEFAULT NULL,
  `contact_state` varchar(30) DEFAULT NULL,
  `contact_zip` varchar(11) DEFAULT NULL,
  `contact_country` varchar(30) DEFAULT NULL,
  `contact_jabber` varchar(255) DEFAULT NULL,
  `contact_icq` varchar(20) DEFAULT NULL,
  `contact_msn` varchar(255) DEFAULT NULL,
  `contact_yahoo` varchar(255) DEFAULT NULL,
  `contact_aol` varchar(30) DEFAULT NULL,
  `contact_notes` text,
  `contact_project` int(11) NOT NULL DEFAULT '0',
  `contact_icon` varchar(20) DEFAULT 'obj/contact',
  `contact_owner` int(10) unsigned DEFAULT '0',
  `contact_private` tinyint(3) unsigned DEFAULT '0',
  PRIMARY KEY (`contact_id`),
  KEY `idx_oby` (`contact_order_by`),
  KEY `idx_co` (`contact_company`),
  KEY `idx_prp` (`contact_project`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%contacts` VALUES (61,'Admin','Administrator','',NULL,NULL,NULL,'0',NULL,NULL,'admin@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'obj/contact',0,0),(62,'guest','guest','',NULL,NULL,NULL,'0',NULL,NULL,'guest@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'obj/contact',0,0),(63,'testlouis','louistest','',NULL,NULL,NULL,'0',NULL,NULL,'louis@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'obj/contact',1,0);


CREATE TABLE `%dbprefix%custom_fields_lists` (
  `field_id` int(11) DEFAULT NULL,
  `list_option_id` int(11) DEFAULT NULL,
  `list_value` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%custom_fields_struct` (
  `field_id` int(11) NOT NULL,
  `field_module` varchar(30) DEFAULT NULL,
  `field_page` varchar(30) DEFAULT NULL,
  `field_htmltype` varchar(20) DEFAULT NULL,
  `field_datatype` varchar(20) DEFAULT NULL,
  `field_order` int(11) DEFAULT NULL,
  `field_name` varchar(100) DEFAULT NULL,
  `field_extratags` varchar(250) DEFAULT NULL,
  `field_description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%custom_fields_values` (
  `value_id` int(11) DEFAULT NULL,
  `value_module` varchar(30) DEFAULT NULL,
  `value_object_id` int(11) DEFAULT NULL,
  `value_field_id` int(11) DEFAULT NULL,
  `value_charvalue` varchar(250) DEFAULT NULL,
  `value_intvalue` int(11) DEFAULT NULL,
  KEY `idx_cfv_id` (`value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%departments` (
  `dept_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dept_parent` int(10) unsigned NOT NULL DEFAULT '0',
  `dept_company` int(10) unsigned NOT NULL DEFAULT '0',
  `dept_name` tinytext NOT NULL,
  `dept_phone` varchar(30) DEFAULT NULL,
  `dept_fax` varchar(30) DEFAULT NULL,
  `dept_address1` varchar(30) DEFAULT NULL,
  `dept_address2` varchar(30) DEFAULT NULL,
  `dept_city` varchar(30) DEFAULT NULL,
  `dept_state` varchar(30) DEFAULT NULL,
  `dept_zip` varchar(11) DEFAULT NULL,
  `dept_url` varchar(25) DEFAULT NULL,
  `dept_desc` text,
  `dept_owner` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`dept_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COMMENT='Department heirarchy under a company';



CREATE TABLE `%dbprefix%dotpermissions` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(80) NOT NULL DEFAULT '',
  `section` varchar(80) NOT NULL DEFAULT '',
  `axo` varchar(80) NOT NULL DEFAULT '',
  `permission` varchar(80) NOT NULL DEFAULT '',
  `allow` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `enabled` int(11) NOT NULL DEFAULT '0',
  KEY `user_id` (`user_id`,`section`,`permission`,`axo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%dotpermissions` VALUES (33,'1','app','helpdesk','access',1,1,1),(33,'1','app','helpdesk','add',1,1,1),(33,'1','app','helpdesk','delete',1,1,1),(33,'1','app','helpdesk','edit',1,1,1),(33,'1','app','helpdesk','view',1,1,1),(12,'1','sys','acl','access',1,3,1),(16,'6','app','users','access',1,3,1),(16,'6','app','users','view',1,3,1),(11,'1','app','admin','access',1,4,1),(11,'1','app','calendar','access',1,4,1),(11,'1','app','events','access',1,4,1),(11,'1','app','companies','access',1,4,1),(11,'1','app','contacts','access',1,4,1),(11,'1','app','departments','access',1,4,1),(11,'1','app','files','access',1,4,1),(11,'1','app','file_folders','access',1,4,1),(11,'1','app','forums','access',1,4,1),(11,'1','app','help','access',1,4,1),(11,'1','app','projects','access',1,4,1),(11,'1','app','system','access',1,4,1),(11,'1','app','tasks','access',1,4,1),(11,'1','app','task_log','access',1,4,1),(11,'1','app','helpdesk','access',1,4,1),(11,'1','app','public','access',1,4,1),(11,'1','app','roles','access',1,4,1),(11,'1','app','users','access',1,4,1),(11,'1','app','history','access',1,4,1),(11,'1','app','resources','access',1,4,1),(11,'1','app','smartsearch','access',1,4,1),(11,'1','app','projectdesigner','access',1,4,1),(11,'1','app','carmina','access',1,4,1),(11,'1','','','access',1,4,1),(11,'1','app','admin','add',1,4,1),(11,'1','app','calendar','add',1,4,1),(11,'1','app','events','add',1,4,1),(11,'1','app','companies','add',1,4,1),(11,'1','app','contacts','add',1,4,1),(11,'1','app','departments','add',1,4,1),(11,'1','app','files','add',1,4,1),(11,'1','app','file_folders','add',1,4,1),(11,'1','app','forums','add',1,4,1),(11,'1','app','help','add',1,4,1),(11,'1','app','projects','add',1,4,1),(11,'1','app','system','add',1,4,1),(11,'1','app','tasks','add',1,4,1),(11,'1','app','task_log','add',1,4,1),(11,'1','app','helpdesk','add',1,4,1),(11,'1','app','public','add',1,4,1),(11,'1','app','roles','add',1,4,1),(11,'1','app','users','add',1,4,1),(11,'1','app','history','add',1,4,1),(11,'1','app','resources','add',1,4,1),(11,'1','app','smartsearch','add',1,4,1),(11,'1','app','projectdesigner','add',1,4,1),(11,'1','app','carmina','add',1,4,1),(11,'1','','','add',1,4,1),(11,'1','app','admin','delete',1,4,1),(11,'1','app','calendar','delete',1,4,1),(11,'1','app','events','delete',1,4,1),(11,'1','app','companies','delete',1,4,1),(11,'1','app','contacts','delete',1,4,1),(11,'1','app','departments','delete',1,4,1),(11,'1','app','files','delete',1,4,1),(11,'1','app','file_folders','delete',1,4,1),(11,'1','app','forums','delete',1,4,1),(11,'1','app','help','delete',1,4,1),(11,'1','app','projects','delete',1,4,1),(11,'1','app','system','delete',1,4,1),(11,'1','app','tasks','delete',1,4,1),(11,'1','app','task_log','delete',1,4,1),(11,'1','app','helpdesk','delete',1,4,1),(11,'1','app','public','delete',1,4,1),(11,'1','app','roles','delete',1,4,1),(11,'1','app','users','delete',1,4,1),(11,'1','app','history','delete',1,4,1),(11,'1','app','resources','delete',1,4,1),(11,'1','app','smartsearch','delete',1,4,1),(11,'1','app','projectdesigner','delete',1,4,1),(11,'1','app','carmina','delete',1,4,1),(11,'1','','','delete',1,4,1),(11,'1','app','admin','edit',1,4,1),(11,'1','app','calendar','edit',1,4,1),(11,'1','app','events','edit',1,4,1),(11,'1','app','companies','edit',1,4,1),(11,'1','app','contacts','edit',1,4,1),(11,'1','app','departments','edit',1,4,1),(11,'1','app','files','edit',1,4,1),(11,'1','app','file_folders','edit',1,4,1),(11,'1','app','forums','edit',1,4,1),(11,'1','app','help','edit',1,4,1),(11,'1','app','projects','edit',1,4,1),(11,'1','app','system','edit',1,4,1),(11,'1','app','tasks','edit',1,4,1),(11,'1','app','task_log','edit',1,4,1),(11,'1','app','helpdesk','edit',1,4,1),(11,'1','app','public','edit',1,4,1),(11,'1','app','roles','edit',1,4,1),(11,'1','app','users','edit',1,4,1),(11,'1','app','history','edit',1,4,1),(11,'1','app','resources','edit',1,4,1),(11,'1','app','smartsearch','edit',1,4,1),(11,'1','app','projectdesigner','edit',1,4,1),(11,'1','app','carmina','edit',1,4,1),(11,'1','','','edit',1,4,1),(11,'1','app','admin','view',1,4,1),(11,'1','app','calendar','view',1,4,1),(11,'1','app','events','view',1,4,1),(11,'1','app','companies','view',1,4,1),(11,'1','app','contacts','view',1,4,1),(11,'1','app','departments','view',1,4,1),(11,'1','app','files','view',1,4,1),(11,'1','app','file_folders','view',1,4,1),(11,'1','app','forums','view',1,4,1),(11,'1','app','help','view',1,4,1),(11,'1','app','projects','view',1,4,1),(11,'1','app','system','view',1,4,1),(11,'1','app','tasks','view',1,4,1),(11,'1','app','task_log','view',1,4,1),(11,'1','app','helpdesk','view',1,4,1),(11,'1','app','public','view',1,4,1),(11,'1','app','roles','view',1,4,1),(11,'1','app','users','view',1,4,1),(11,'1','app','history','view',1,4,1),(11,'1','app','resources','view',1,4,1),(11,'1','app','smartsearch','view',1,4,1),(11,'1','app','projectdesigner','view',1,4,1),(11,'1','app','carmina','view',1,4,1),(11,'1','','','view',1,4,1),(13,'6','app','calendar','access',1,4,1),(13,'6','app','events','access',1,4,1),(13,'6','app','companies','access',1,4,1),(13,'6','app','contacts','access',1,4,1),(13,'6','app','departments','access',1,4,1),(13,'6','app','files','access',1,4,1),(13,'6','app','file_folders','access',1,4,1),(13,'6','app','forums','access',1,4,1),(13,'6','app','help','access',1,4,1),(13,'6','app','projects','access',1,4,1),(13,'6','app','tasks','access',1,4,1),(13,'6','app','task_log','access',1,4,1),(13,'6','app','helpdesk','access',1,4,1),(13,'6','app','public','access',1,4,1),(13,'6','app','history','access',1,4,1),(13,'6','app','resources','access',1,4,1),(13,'6','app','smartsearch','access',1,4,1),(13,'6','app','projectdesigner','access',1,4,1),(13,'6','app','carmina','access',1,4,1),(13,'6','','','access',1,4,1),(13,'6','app','calendar','view',1,4,1),(13,'6','app','events','view',1,4,1),(13,'6','app','companies','view',1,4,1),(13,'6','app','contacts','view',1,4,1),(13,'6','app','departments','view',1,4,1),(13,'6','app','files','view',1,4,1),(13,'6','app','file_folders','view',1,4,1),(13,'6','app','forums','view',1,4,1),(13,'6','app','help','view',1,4,1),(13,'6','app','projects','view',1,4,1),(13,'6','app','tasks','view',1,4,1),(13,'6','app','task_log','view',1,4,1),(13,'6','app','helpdesk','view',1,4,1),(13,'6','app','public','view',1,4,1),(13,'6','app','history','view',1,4,1),(13,'6','app','resources','view',1,4,1),(13,'6','app','smartsearch','view',1,4,1),(13,'6','app','projectdesigner','view',1,4,1),(13,'6','app','carmina','view',1,4,1),(13,'6','','','view',1,4,1);


CREATE TABLE `%dbprefix%dpversion` (
  `code_version` varchar(10) NOT NULL DEFAULT '',
  `db_version` int(11) NOT NULL DEFAULT '0',
  `last_db_update` date NOT NULL DEFAULT '0000-00-00',
  `last_code_update` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%dpversion` VALUES ('2.1.8',2,'2010-12-16','2011-01-06');


CREATE TABLE `%dbprefix%event_queue` (
  `queue_id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_start` int(11) NOT NULL DEFAULT '0',
  `queue_type` varchar(40) NOT NULL DEFAULT '',
  `queue_repeat_interval` int(11) NOT NULL DEFAULT '0',
  `queue_repeat_count` int(11) NOT NULL DEFAULT '0',
  `queue_data` longblob NOT NULL,
  `queue_callback` varchar(127) NOT NULL DEFAULT '',
  `queue_owner` int(11) NOT NULL DEFAULT '0',
  `queue_origin_id` int(11) NOT NULL DEFAULT '0',
  `queue_module` varchar(40) NOT NULL DEFAULT '',
  `queue_module_type` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`queue_id`),
  KEY `queue_start` (`queue_start`),
  KEY `queue_module` (`queue_module`),
  KEY `queue_type` (`queue_type`),
  KEY `queue_origin_id` (`queue_origin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_title` varchar(255) NOT NULL DEFAULT '',
  `event_start_date` datetime DEFAULT NULL,
  `event_end_date` datetime DEFAULT NULL,
  `event_parent` int(11) unsigned NOT NULL DEFAULT '0',
  `event_description` text,
  `event_times_recuring` int(11) unsigned NOT NULL DEFAULT '0',
  `event_recurs` int(11) unsigned NOT NULL DEFAULT '0',
  `event_remind` int(10) unsigned NOT NULL DEFAULT '0',
  `event_icon` varchar(20) DEFAULT 'obj/event',
  `event_owner` int(11) DEFAULT '0',
  `event_project` int(11) DEFAULT '0',
  `event_private` tinyint(3) DEFAULT '0',
  `event_type` tinyint(3) DEFAULT '0',
  `event_cwd` tinyint(3) DEFAULT '0',
  `event_notify` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`),
  KEY `id_esd` (`event_start_date`),
  KEY `id_eed` (`event_end_date`),
  KEY `id_evp` (`event_parent`),
  KEY `idx_ev1` (`event_owner`),
  KEY `idx_ev2` (`event_project`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%file_folders` (
  `file_folder_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_folder_parent` int(11) NOT NULL DEFAULT '0',
  `file_folder_name` varchar(255) NOT NULL DEFAULT '',
  `file_folder_description` text,
  PRIMARY KEY (`file_folder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_real_filename` varchar(255) NOT NULL DEFAULT '',
  `file_folder` int(11) NOT NULL DEFAULT '0',
  `file_project` int(11) NOT NULL DEFAULT '0',
  `file_task` int(11) NOT NULL DEFAULT '0',
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `file_parent` int(11) DEFAULT '0',
  `file_description` text,
  `file_type` varchar(100) DEFAULT NULL,
  `file_owner` int(11) DEFAULT '0',
  `file_date` datetime DEFAULT NULL,
  `file_size` int(11) DEFAULT '0',
  `file_version` float NOT NULL DEFAULT '0',
  `file_icon` varchar(20) DEFAULT 'obj/',
  `file_category` int(11) DEFAULT '0',
  `file_checkout` varchar(255) NOT NULL DEFAULT '',
  `file_co_reason` text,
  `file_version_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`),
  KEY `idx_file_task` (`file_task`),
  KEY `idx_file_project` (`file_project`),
  KEY `idx_file_parent` (`file_parent`),
  KEY `idx_file_vid` (`file_version_id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%files_index` (
  `file_id` int(11) NOT NULL DEFAULT '0',
  `word` varchar(50) NOT NULL DEFAULT '',
  `word_placement` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`,`word`,`word_placement`),
  KEY `idx_fwrd` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%forum_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_forum` int(11) NOT NULL DEFAULT '0',
  `message_parent` int(11) NOT NULL DEFAULT '0',
  `message_author` int(11) NOT NULL DEFAULT '0',
  `message_editor` int(11) NOT NULL DEFAULT '0',
  `message_title` varchar(255) NOT NULL DEFAULT '',
  `message_date` datetime DEFAULT '0000-00-00 00:00:00',
  `message_body` text,
  `message_published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`message_id`),
  KEY `idx_mparent` (`message_parent`),
  KEY `idx_mdate` (`message_date`),
  KEY `idx_mforum` (`message_forum`)
) ENGINE=MyISAM AUTO_INCREMENT=151 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%forum_visits` (
  `visit_user` int(10) NOT NULL DEFAULT '0',
  `visit_forum` int(10) NOT NULL DEFAULT '0',
  `visit_message` int(10) NOT NULL DEFAULT '0',
  `visit_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_id` varchar(40) DEFAULT NULL,
  KEY `idx_fv` (`visit_user`,`visit_forum`,`visit_message`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%forum_watch` (
  `watch_user` int(10) unsigned NOT NULL DEFAULT '0',
  `watch_forum` int(10) unsigned DEFAULT NULL,
  `watch_topic` int(10) unsigned DEFAULT NULL,
  KEY `idx_fw1` (`watch_user`,`watch_forum`),
  KEY `idx_fw2` (`watch_user`,`watch_topic`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links users to the forums/messages they are watching';



CREATE TABLE `%dbprefix%forums` (
  `forum_id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_project` int(11) NOT NULL DEFAULT '0',
  `forum_status` tinyint(4) NOT NULL DEFAULT '-1',
  `forum_owner` int(11) NOT NULL DEFAULT '0',
  `forum_name` varchar(50) NOT NULL DEFAULT '',
  `forum_create_date` datetime DEFAULT '0000-00-00 00:00:00',
  `forum_last_date` datetime DEFAULT '0000-00-00 00:00:00',
  `forum_last_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_message_count` int(11) NOT NULL DEFAULT '0',
  `forum_description` text,
  `forum_moderated` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`forum_id`),
  KEY `idx_fproject` (`forum_project`),
  KEY `idx_fowner` (`forum_owner`),
  KEY `forum_status` (`forum_status`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%gacl_acl` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(230) NOT NULL DEFAULT 'system',
  `allow` int(11) NOT NULL DEFAULT '0',
  `enabled` int(11) NOT NULL DEFAULT '0',
  `return_value` text,
  `note` text,
  `updated_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `gacl_enabled_acl` (`enabled`),
  KEY `gacl_section_value_acl` (`section_value`),
  KEY `gacl_updated_date_acl` (`updated_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_acl` VALUES (10,'user',1,1,NULL,NULL,947369906),(11,'user',1,1,NULL,NULL,947369906),(12,'user',1,1,NULL,NULL,947369906),(13,'user',1,1,NULL,NULL,947369906),(14,'user',1,1,NULL,NULL,947369906),(15,'user',1,1,NULL,NULL,947369906),(16,'user',1,1,NULL,NULL,947369906),(19,'user',1,1,NULL,NULL,1389255470),(21,'user',1,1,NULL,NULL,1389658760),(32,'user',0,0,'','',1415314927),(33,'user',1,1,NULL,NULL,1421946577);


CREATE TABLE `%dbprefix%gacl_acl_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(230) NOT NULL,
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_acl_sections` (`value`),
  KEY `gacl_hidden_acl_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_acl_sections` VALUES (1,'system',1,'System',0),(2,'user',2,'User',0);


CREATE TABLE `%dbprefix%gacl_acl_seq` (
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_acl_seq` VALUES (33);


CREATE TABLE `%dbprefix%gacl_aco` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(240) NOT NULL DEFAULT '0',
  `value` varchar(240) NOT NULL,
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_section_value_value_aco` (`section_value`,`value`),
  KEY `gacl_hidden_aco` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aco` VALUES (10,'system','login',1,'Login',0),(11,'application','access',1,'Access',0),(12,'application','view',2,'View',0),(13,'application','add',3,'Add',0),(14,'application','edit',4,'Edit',0),(15,'application','delete',5,'Delete',0);


CREATE TABLE `%dbprefix%gacl_aco_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(230) NOT NULL DEFAULT '0',
  `value` varchar(230) NOT NULL,
  PRIMARY KEY (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aco_map` VALUES (10,'system','login'),(11,'application','access'),(11,'application','add'),(11,'application','delete'),(11,'application','edit'),(11,'application','view'),(12,'application','access'),(13,'application','access'),(13,'application','view'),(14,'application','access'),(15,'application','access'),(15,'application','add'),(15,'application','delete'),(15,'application','edit'),(15,'application','view'),(16,'application','access'),(16,'application','view'),(19,'application','access'),(21,'application','access'),(21,'application','view'),(32,'application','access'),(33,'application','access'),(33,'application','add'),(33,'application','delete'),(33,'application','edit'),(33,'application','view');


CREATE TABLE `%dbprefix%gacl_aco_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(230) NOT NULL,
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_aco_sections` (`value`),
  KEY `gacl_hidden_aco_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aco_sections` VALUES (10,'system',1,'System',0),(11,'application',2,'Application',0);


CREATE TABLE `%dbprefix%gacl_aco_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aco_sections_seq` VALUES (11);


CREATE TABLE `%dbprefix%gacl_aco_seq` (
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aco_seq` VALUES (15);


CREATE TABLE `%dbprefix%gacl_aro` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(240) NOT NULL DEFAULT '0',
  `value` varchar(240) NOT NULL,
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_section_value_value_aro` (`section_value`,`value`),
  KEY `gacl_hidden_aro` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aro` VALUES (10,'user','1',1,'admin',0),(15,'user','6',1,'guess',0);


CREATE TABLE `%dbprefix%gacl_aro_groups` (
  `id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`value`),
  UNIQUE KEY `gacl_value_aro_groups` (`value`),
  KEY `gacl_parent_id_aro_groups` (`parent_id`),
  KEY `gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aro_groups` VALUES (10,0,1,12,'Roles','role'),(11,10,2,3,'Administrator','admin'),(12,10,4,5,'Anonymous','anon'),(13,10,6,7,'Guest','guest'),(14,10,8,9,'Project worker','techbot'),(15,10,10,11,'Carmina Worker','carmina-worker');


CREATE TABLE `%dbprefix%gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aro_groups_id_seq` VALUES (15);


CREATE TABLE `%dbprefix%gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`acl_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aro_groups_map` VALUES (10,10),(11,11),(12,11),(13,13),(14,12),(15,14),(16,13),(16,14),(32,10);


CREATE TABLE `%dbprefix%gacl_aro_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL,
  `value` varchar(230) NOT NULL DEFAULT '',
  PRIMARY KEY (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aro_map` VALUES (33,'user','1');


CREATE TABLE `%dbprefix%gacl_aro_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(230) NOT NULL,
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_aro_sections` (`value`),
  KEY `gacl_hidden_aro_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aro_sections` VALUES (10,'user',1,'Users',0);


CREATE TABLE `%dbprefix%gacl_aro_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aro_sections_seq` VALUES (10);


CREATE TABLE `%dbprefix%gacl_aro_seq` (
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_aro_seq` VALUES (18);


CREATE TABLE `%dbprefix%gacl_axo` (
  `id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(240) NOT NULL DEFAULT '0',
  `value` varchar(240) NOT NULL,
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_section_value_value_axo` (`section_value`,`value`),
  KEY `gacl_hidden_axo` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_axo` VALUES (10,'sys','acl',1,'ACL Administration',0),(11,'app','admin',1,'User Administration',0),(12,'app','calendar',2,'Calendar',0),(13,'app','events',2,'Events',0),(14,'app','companies',3,'Companies',0),(15,'app','contacts',4,'Contacts',0),(16,'app','departments',5,'Departments',0),(17,'app','files',6,'Files',0),(18,'app','file_folders',6,'File Folders',0),(19,'app','forums',7,'Forums',0),(20,'app','help',8,'Help',0),(21,'app','projects',9,'Projects',0),(22,'app','system',10,'System Administration',0),(23,'app','tasks',11,'Tasks',0),(24,'app','task_log',11,'Task Logs',0),(25,'app','helpdesk',12,'Helpdesk',0),(26,'app','public',13,'Public',0),(27,'app','roles',14,'Roles Administration',0),(28,'app','users',15,'User Table',0),(29,'app','history',1,'History',0),(30,'app','resources',1,'Resources',0),(31,'app','smartsearch',1,'SmartSearch',0),(32,'tasks','157',0,'Link Bot',0),(33,'projects','26',0,'Carmina',0),(34,'app','projectdesigner',1,'ProjectDesigner',0),(35,'projects','41',0,'Dotproject',0),(36,'app','carmina',1,'Carmina-Flex',0);


CREATE TABLE `%dbprefix%gacl_axo_groups` (
  `id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`,`value`),
  UNIQUE KEY `gacl_value_axo_groups` (`value`),
  KEY `gacl_parent_id_axo_groups` (`parent_id`),
  KEY `gacl_lft_rgt_axo_groups` (`lft`,`rgt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_axo_groups` VALUES (10,0,1,8,'Modules','mod'),(11,10,2,3,'All Modules','all'),(12,10,4,5,'Admin Modules','admin'),(13,10,6,7,'Non-Admin Modules','non_admin');


CREATE TABLE `%dbprefix%gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_axo_groups_id_seq` VALUES (13);


CREATE TABLE `%dbprefix%gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`acl_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_axo_groups_map` VALUES (11,11),(13,13),(14,13),(15,13);


CREATE TABLE `%dbprefix%gacl_axo_map` (
  `acl_id` int(11) NOT NULL DEFAULT '0',
  `section_value` varchar(80) NOT NULL DEFAULT '0',
  `value` varchar(230) NOT NULL,
  PRIMARY KEY (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_axo_map` VALUES (12,'sys','acl'),(16,'app','users'),(19,'app','projects'),(21,'tasks','157'),(33,'app','helpdesk');


CREATE TABLE `%dbprefix%gacl_axo_sections` (
  `id` int(11) NOT NULL DEFAULT '0',
  `value` varchar(230) NOT NULL,
  `order_value` int(11) NOT NULL DEFAULT '0',
  `name` varchar(230) NOT NULL DEFAULT '',
  `hidden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gacl_value_axo_sections` (`value`),
  KEY `gacl_hidden_axo_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_axo_sections` VALUES (10,'sys',1,'System',0),(11,'app',2,'Application',0),(12,'resources',0,'Resources Record',0),(13,'tasks',0,'Tasks Record',0),(14,'projects',0,'Projects Record',0),(16,'companies',0,'Companies Record',0);


CREATE TABLE `%dbprefix%gacl_axo_sections_seq` (
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_axo_sections_seq` VALUES (18);


CREATE TABLE `%dbprefix%gacl_axo_seq` (
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_axo_seq` VALUES (39);


CREATE TABLE `%dbprefix%gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `aro_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`aro_id`),
  KEY `aro_id` (`aro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_groups_aro_map` VALUES (11,10),(13,15);


CREATE TABLE `%dbprefix%gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `axo_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`axo_id`),
  KEY `axo_id` (`axo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_groups_axo_map` VALUES (11,11),(12,11),(11,12),(13,12),(11,13),(13,13),(11,14),(13,14),(11,15),(13,15),(11,16),(13,16),(11,17),(13,17),(11,18),(13,18),(11,19),(13,19),(11,20),(13,20),(11,21),(13,21),(11,22),(12,22),(11,23),(13,23),(11,24),(13,24),(11,25),(13,25),(11,26),(13,26),(11,27),(12,27),(11,28),(12,28),(11,29),(13,29),(11,30),(13,30),(11,31),(13,31),(11,34),(13,34),(11,36),(13,36),(11,38),(13,38);


CREATE TABLE `%dbprefix%gacl_phpgacl` (
  `name` varchar(230) NOT NULL,
  `value` varchar(230) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%gacl_phpgacl` VALUES ('schema_version','2.1'),('version','3.3.2');


CREATE TABLE `%dbprefix%helpdesk_item_status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_item_id` int(11) NOT NULL,
  `status_code` tinyint(4) NOT NULL,
  `status_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status_modified_by` int(11) NOT NULL,
  `status_comment` text,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%helpdesk_item_watchers` (
  `item_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `notify` char(1) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%helpdesk_items` (
  `item_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_title` varchar(64) NOT NULL DEFAULT '',
  `item_summary` text,
  `item_calltype` int(3) unsigned NOT NULL DEFAULT '0',
  `item_source` int(3) unsigned NOT NULL DEFAULT '0',
  `item_os` varchar(48) NOT NULL DEFAULT '',
  `item_application` varchar(48) NOT NULL DEFAULT '',
  `item_priority` int(3) unsigned NOT NULL DEFAULT '0',
  `item_severity` int(3) unsigned NOT NULL DEFAULT '0',
  `item_status` int(3) unsigned NOT NULL DEFAULT '0',
  `item_assigned_to` int(11) NOT NULL DEFAULT '0',
  `item_created_by` int(11) NOT NULL DEFAULT '0',
  `item_notify` int(1) NOT NULL DEFAULT '1',
  `item_requestor` varchar(48) NOT NULL DEFAULT '',
  `item_requestor_id` int(11) NOT NULL DEFAULT '0',
  `item_requestor_email` varchar(128) NOT NULL DEFAULT '',
  `item_requestor_phone` varchar(30) NOT NULL DEFAULT '',
  `item_requestor_type` tinyint(4) NOT NULL DEFAULT '0',
  `item_created` datetime DEFAULT NULL,
  `item_modified` datetime DEFAULT NULL,
  `item_parent` int(10) unsigned NOT NULL DEFAULT '0',
  `item_project_id` int(11) NOT NULL DEFAULT '0',
  `item_company_id` int(11) NOT NULL DEFAULT '0',
  `item_updated` datetime DEFAULT NULL,
  `item_deadline` datetime DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%history` (
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `history_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `history_user` int(10) NOT NULL DEFAULT '0',
  `history_action` varchar(20) NOT NULL DEFAULT 'modify',
  `history_item` int(10) NOT NULL,
  `history_table` varchar(20) NOT NULL DEFAULT '',
  `history_project` int(10) NOT NULL DEFAULT '0',
  `history_name` varchar(255) DEFAULT NULL,
  `history_changes` text,
  `history_description` text,
  PRIMARY KEY (`history_id`),
  KEY `index_history_module` (`history_table`,`history_item`),
  KEY `index_history_item` (`history_item`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%login_header` (
  `login_header_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `added_date` datetime DEFAULT NULL,
  `counter` int(2) DEFAULT NULL,
  `ip_address` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`login_header_id`),
  KEY `sessionid` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%modules` (
  `mod_id` int(11) NOT NULL AUTO_INCREMENT,
  `mod_name` varchar(64) NOT NULL DEFAULT '',
  `mod_directory` varchar(64) NOT NULL DEFAULT '',
  `mod_version` varchar(10) NOT NULL DEFAULT '',
  `mod_setup_class` varchar(64) NOT NULL DEFAULT '',
  `mod_type` varchar(64) NOT NULL DEFAULT '',
  `mod_active` int(1) unsigned NOT NULL DEFAULT '0',
  `mod_ui_name` varchar(20) NOT NULL DEFAULT '',
  `mod_ui_icon` varchar(64) NOT NULL DEFAULT '',
  `mod_ui_order` tinyint(3) NOT NULL DEFAULT '0',
  `mod_ui_active` int(1) unsigned NOT NULL DEFAULT '0',
  `mod_description` varchar(255) NOT NULL DEFAULT '',
  `permissions_item_table` char(100) DEFAULT NULL,
  `permissions_item_field` char(100) DEFAULT NULL,
  `permissions_item_label` char(100) DEFAULT NULL,
  PRIMARY KEY (`mod_id`,`mod_directory`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%modules` VALUES (1,'Companies','companies','1.0.0','','core',1,'Companies','handshake.png',1,1,'','companies','company_id','company_name'),(2,'Projects','projects','1.0.0','','core',1,'Projects','applet3-48.png',2,1,'','projects','project_id','project_name'),(3,'Tasks','tasks','1.0.0','','core',1,'Tasks','applet-48.png',3,1,'','tasks','task_id','task_name'),(4,'Calendar','calendar','1.0.0','','core',1,'Calendar','myevo-appointments.png',4,1,'','events','event_id','event_title'),(5,'Files','files','1.0.0','','core',1,'Files','folder5.png',5,1,'','files','file_id','file_name'),(6,'Contacts','contacts','1.0.0','','core',1,'Contacts','monkeychat-48.png',6,1,'','contacts','contact_id','contact_title'),(7,'Forums','forums','1.0.0','','core',1,'Forums','support.png',7,1,'','forums','forum_id','forum_name'),(9,'User Administration','admin','1.0.0','','core',1,'User Admin','helix-setup-users.png',9,1,'','users','user_id','user_username'),(10,'System Administration','system','1.0.0','','core',1,'System Admin','48_my_computer.png',10,1,'','','',''),(11,'Departments','departments','1.0.0','','core',1,'Departments','users.gif',11,1,'','departments','dept_id','dept_name'),(12,'Help','help','1.0.0','','core',1,'Help','dp.gif',12,1,'','','',''),(13,'Public','public','1.0.0','','core',1,'Public','users.gif',13,0,'','','',''),(14,'History','history','0.32','CSetupHistory','user',1,'History','',13,1,'A module for tracking changes',NULL,NULL,NULL),(15,'Resources','resources','1.0.1','SResource','user',1,'Resources','helpdesk.png',14,1,'','resources','resource_id','resource_name'),(16,'SmartSearch','smartsearch','2.0','SSearchNS','user',1,'SmartSearch','kfind.png',16,1,'A module to search keywords and find the needle in the haystack',NULL,NULL,NULL),(17,'ProjectDesigner','projectdesigner','1.0','projectDesigner','user',1,'ProjectDesigner','projectdesigner.jpg',17,1,'A module to design projects',NULL,NULL,NULL),(22,'HelpDesk','helpdesk','0.6','CSetupHelpDesk','user',1,'Help Desk','helpdesk.png',20,1,'Help Desk is a bug, feature request, complaint and suggestion tracking centre','companies','company_id','company_name');


CREATE TABLE `%dbprefix%permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_user` int(11) NOT NULL DEFAULT '0',
  `permission_grant_on` varchar(12) NOT NULL DEFAULT '',
  `permission_item` int(11) NOT NULL DEFAULT '0',
  `permission_value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `idx_pgrant_on` (`permission_grant_on`,`permission_item`,`permission_user`),
  KEY `idx_puser` (`permission_user`),
  KEY `idx_pvalue` (`permission_value`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%permissions` VALUES (1,1,'all',-1,-1);


CREATE TABLE `%dbprefix%phpbrowscap` (
  `browser_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `browser_name` mediumtext CHARACTER SET latin1 COLLATE latin1_general_ci,
  `browser_name_regex` mediumtext CHARACTER SET latin1 COLLATE latin1_general_ci,
  `browser_name_pattern` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `parent` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `comment` varchar(14) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `browser` varchar(96) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `version` varchar(96) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `majorver` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `platform` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `frames` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `iframes` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '',
  `tables` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `cookies` varchar(14) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `javascript` varchar(14) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `javaapplets` varchar(96) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `cssversion` varchar(96) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `device_name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `device_maker` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `device_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `device_pointing_method` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `renderingengine_name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `renderingengine_version` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `browser_type` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `minorver` int(11) DEFAULT NULL,
  `platform_version` varchar(14) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `platform_description` varchar(96) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `alpha` varchar(96) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `beta` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `win16` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `win32` varchar(14) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `win64` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '',
  `backgroundsounds` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `vbscript` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '',
  `activexcontrols` varchar(14) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `ismobiledevice` varchar(14) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT '',
  `issyndicationreader` varchar(96) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `crawler` varchar(96) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `aolversion` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`browser_id`),
  KEY `sessionid` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%project_contacts` (
  `project_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%project_departments` (
  `project_id` int(10) NOT NULL,
  `department_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%project_designer_options` (
  `pd_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `pd_option_user` int(11) NOT NULL DEFAULT '0',
  `pd_option_view_project` int(1) NOT NULL DEFAULT '1',
  `pd_option_view_gantt` int(1) NOT NULL DEFAULT '1',
  `pd_option_view_tasks` int(1) NOT NULL DEFAULT '1',
  `pd_option_view_actions` int(1) NOT NULL DEFAULT '1',
  `pd_option_view_addtasks` int(1) NOT NULL DEFAULT '1',
  `pd_option_view_files` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pd_option_id`),
  UNIQUE KEY `pd_option_user` (`pd_option_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%projects` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_company` int(11) NOT NULL DEFAULT '0',
  `project_company_internal` int(11) NOT NULL DEFAULT '0',
  `project_department` int(11) NOT NULL DEFAULT '0',
  `project_name` varchar(255) DEFAULT NULL,
  `project_short_name` varchar(10) DEFAULT NULL,
  `project_owner` int(11) DEFAULT '0',
  `project_url` varchar(255) DEFAULT NULL,
  `project_demo_url` varchar(255) DEFAULT NULL,
  `project_start_date` datetime DEFAULT NULL,
  `project_end_date` datetime DEFAULT NULL,
  `project_status` int(11) DEFAULT '0',
  `project_percent_complete` tinyint(4) DEFAULT '0',
  `project_color_identifier` varchar(6) DEFAULT 'eeeeee',
  `project_description` text,
  `project_target_budget` decimal(10,2) DEFAULT '0.00',
  `project_actual_budget` decimal(10,2) DEFAULT '0.00',
  `project_creator` int(11) DEFAULT '0',
  `project_private` tinyint(3) unsigned DEFAULT '0',
  `project_departments` char(100) DEFAULT NULL,
  `project_contacts` char(100) DEFAULT NULL,
  `project_priority` tinyint(4) DEFAULT '0',
  `project_type` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`project_id`),
  KEY `idx_project_owner` (`project_owner`),
  KEY `idx_sdate` (`project_start_date`),
  KEY `idx_edate` (`project_end_date`),
  KEY `project_short_name` (`project_short_name`),
  KEY `idx_proj1` (`project_company`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%resource_tasks` (
  `resource_id` int(11) NOT NULL DEFAULT '0',
  `task_id` int(11) NOT NULL DEFAULT '0',
  `percent_allocated` int(11) NOT NULL DEFAULT '100',
  KEY `resource_id` (`resource_id`),
  KEY `task_id` (`task_id`,`resource_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%resource_types` (
  `resource_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_type_name` varchar(255) NOT NULL DEFAULT '',
  `resource_type_note` text,
  PRIMARY KEY (`resource_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%resource_types` VALUES (1,'Equipment',NULL),(2,'Tool',NULL),(3,'Venue',NULL);


CREATE TABLE `%dbprefix%resources` (
  `resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(255) NOT NULL DEFAULT '',
  `resource_key` varchar(64) NOT NULL DEFAULT '',
  `resource_type` int(11) NOT NULL DEFAULT '0',
  `resource_note` text NOT NULL,
  `resource_max_allocation` int(11) NOT NULL DEFAULT '100',
  PRIMARY KEY (`resource_id`),
  KEY `resource_name` (`resource_name`),
  KEY `resource_type` (`resource_type`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(24) NOT NULL DEFAULT '',
  `role_description` varchar(255) NOT NULL DEFAULT '',
  `role_type` int(3) unsigned NOT NULL DEFAULT '0',
  `role_module` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '',
  `session_user` int(11) NOT NULL DEFAULT '0',
  `session_data` longblob,
  `session_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `session_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`session_id`),
  KEY `session_updated` (`session_updated`),
  KEY `session_created` (`session_created`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%syskeys` (
  `syskey_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `syskey_name` varchar(48) NOT NULL DEFAULT '',
  `syskey_label` varchar(255) NOT NULL DEFAULT '',
  `syskey_type` int(1) unsigned NOT NULL DEFAULT '0',
  `syskey_sep1` char(2) DEFAULT '\n',
  `syskey_sep2` char(2) NOT NULL DEFAULT '|',
  PRIMARY KEY (`syskey_id`),
  UNIQUE KEY `syskey_name` (`syskey_name`),
  UNIQUE KEY `idx_syskey_name` (`syskey_name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%syskeys` VALUES (1,'SelectList','Enter values for list',0,'\n','|'),(2,'CustomField','Serialized array in the following format:\r\n<KEY>|<SERIALIZED ARRAY>\r\n\r\nSerialized Array:\r\n[type] => text | checkbox | select | textarea | label\r\n[name] => <Field name>\r\n[options] => <html capture options>\r\n[selects] => <options for select and checkbox>',0,'\n','|'),(3,'ColorSelection','Hex color values for type=>color association.',0,'\n','|'),(5,'HelpDeskList','Enter values for list',0,'\n','|');


CREATE TABLE `%dbprefix%sysvals` (
  `sysval_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sysval_key_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sysval_title` varchar(48) NOT NULL DEFAULT '',
  `sysval_value` text NOT NULL,
  PRIMARY KEY (`sysval_id`),
  UNIQUE KEY `idx_sysval_title` (`sysval_title`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%sysvals` VALUES (1,1,'ProjectStatus','0|Not Defined\r\n1|Proposed\r\n2|In Planning\r\n3|In Progress\r\n4|On Hold\r\n5|Complete\r\n6|Template\r\n7|Archived'),(2,1,'CompanyType','0|Not Applicable\n1|Client\n2|Vendor\n3|Supplier\n4|Consultant\n5|Government\n6|Internal'),(3,1,'TaskDurationType','1|hours\n24|days'),(5,1,'TaskStatus','0|Active\n-1|Inactive'),(6,1,'TaskType','0|Unknown\n1|Administrative\n2|Operative'),(7,1,'ProjectType','0|Unknown\n1|Administrative\n2|Operative'),(8,3,'ProjectColors','Web|FFE0AE\nEngineering|AEFFB2\nHelpDesk|FFFCAE\nSystem Administration|FFAEAE'),(9,1,'FileType','0|Unknown\n1|Document\n2|Application'),(10,1,'TaskPriority','-1|low\n0|normal\n1|high'),(11,1,'ProjectPriority','-1|low\n0|normal\n1|high'),(12,1,'ProjectPriorityColor','-1|#E5F7FF\n0|\n1|#FFDCB3'),(13,1,'TaskLogReference','0|Not Defined\n1|Email\n2|Helpdesk\n3|Phone Call\n4|Fax'),(14,1,'TaskLogReferenceImage','0| 1|./images/obj/email.gif 2|./modules/helpdesk/images/helpdesk.png 3|./images/obj/phone.gif 4|./images/icons/stock_print-16.png'),(15,1,'UserType','0|Default User\r\n1|Administrator\r\n2|CEO\r\n3|Director\r\n4|Branch Manager\r\n5|Manager\r\n6|Supervisor\r\n7|Employee'),(16,1,'ProjectRequiredFields','f.project_name.value.length|<3\r\nf.project_color_identifier.value.length|<3\r\nf.project_company.options[f.project_company.selectedIndex].value|<1'),(19,1,'TicketStatus','0|Open\n1|Closed\n2|Deleted'),(21,5,'HelpDeskPriority','0|Not Specified\n1|Low\n2|Medium\n3|High'),(22,5,'HelpDeskSeverity','0|Not Specified\n1|No Impact\n2|Low\n3|Medium\n4|High\n5|Critical'),(23,5,'HelpDeskCallType','0|Not Specified\r\n1|Bug\r\n2|Feature Request\r\n3|Complaint\r\n4|Suggestion'),(24,5,'HelpDeskSource','0|Not Specified\n1|E-Mail\n2|Phone\n3|Fax\n4|In Person\n5|E-Lodged\n6|WWW'),(25,5,'HelpDeskOS','0|Not Applicable\n1|Linux\n2|Unix\n3|Solaris 8\n4|Solaris 9\n5|Red Hat 6\n6|Red Hat 7\n7|Red Hat 8\n8|Windows 95\n9|Window 98\n10|Windows 2000\n11|Window 2000 Server\n12|Windows XP'),(26,5,'HelpDeskApplic','0|Not Applicable\n1|Word\n2|Excel'),(27,5,'HelpDeskStatus','0|Unassigned\n1|Open\n2|Closed\n3|On Hold\n4|Testing'),(28,5,'HelpDeskAuditTrail','0|Created\n1|Title\n2|Requestor Name\n3|Requestor E-mail\n4|Requestor Phone\n5|Assigned To\n6|Notify by e-mail\n7|Company\n8|Project\n9|Call Type\n10|Call Source\n11|Status\n12|Priority\n13|Severity\n14|Operating System\n15|Application\n16|Summary\n17|Deleted');


CREATE TABLE `%dbprefix%task_contacts` (
  `task_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%task_departments` (
  `task_id` int(10) NOT NULL,
  `department_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%task_dependencies` (
  `dependencies_task_id` int(11) NOT NULL,
  `dependencies_req_task_id` int(11) NOT NULL,
  PRIMARY KEY (`dependencies_task_id`,`dependencies_req_task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%task_log` (
  `task_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_log_task` int(11) NOT NULL DEFAULT '0',
  `task_log_help_desk_id` int(11) NOT NULL DEFAULT '0',
  `task_log_name` varchar(255) DEFAULT NULL,
  `task_log_description` text,
  `task_log_creator` int(11) NOT NULL DEFAULT '0',
  `task_log_hours` float NOT NULL DEFAULT '0',
  `task_log_date` datetime DEFAULT NULL,
  `task_log_costcode` varchar(8) NOT NULL DEFAULT '',
  `task_log_problem` tinyint(1) DEFAULT '0',
  `task_log_reference` tinyint(4) DEFAULT '0',
  `task_log_related_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`task_log_id`),
  KEY `idx_log_task` (`task_log_task`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(255) DEFAULT NULL,
  `task_parent` int(11) DEFAULT '0',
  `task_milestone` tinyint(1) DEFAULT '0',
  `task_project` int(11) NOT NULL DEFAULT '0',
  `task_owner` int(11) NOT NULL DEFAULT '0',
  `task_start_date` datetime DEFAULT NULL,
  `task_duration` float unsigned DEFAULT '0',
  `task_duration_type` int(11) NOT NULL DEFAULT '1',
  `task_hours_worked` float unsigned DEFAULT '0',
  `task_end_date` datetime DEFAULT NULL,
  `task_status` int(11) DEFAULT '0',
  `task_priority` tinyint(4) DEFAULT '0',
  `task_percent_complete` tinyint(4) DEFAULT '0',
  `task_description` text,
  `task_target_budget` decimal(10,2) DEFAULT '0.00',
  `task_related_url` varchar(255) DEFAULT NULL,
  `task_creator` int(11) NOT NULL DEFAULT '0',
  `task_order` int(11) NOT NULL DEFAULT '0',
  `task_client_publish` tinyint(1) NOT NULL DEFAULT '0',
  `task_dynamic` tinyint(1) NOT NULL DEFAULT '0',
  `task_access` int(11) NOT NULL DEFAULT '0',
  `task_notify` int(11) NOT NULL DEFAULT '0',
  `task_departments` char(100) DEFAULT NULL,
  `task_contacts` char(100) DEFAULT NULL,
  `task_custom` longtext,
  `task_type` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_id`),
  KEY `idx_task_parent` (`task_parent`),
  KEY `idx_task_project` (`task_project`),
  KEY `idx_task_owner` (`task_owner`),
  KEY `idx_task_order` (`task_order`),
  KEY `idx_task1` (`task_start_date`),
  KEY `idx_task2` (`task_end_date`)
) ENGINE=MyISAM AUTO_INCREMENT=332 DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%user_access_log` (
  `user_access_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `session_id` varchar(40) NOT NULL,
  `user_ip` varchar(15) NOT NULL,
  `last_page_url` varchar(128) NOT NULL,
  `browser_language` varchar(128) DEFAULT NULL,
  `date_time_in` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_out` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_last_action` datetime DEFAULT '0000-00-00 00:00:00',
  `ismobiledevice` tinyint(1) DEFAULT NULL,
  `referer` varchar(128) DEFAULT NULL,
  `crawler` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`user_access_log_id`),
  KEY `session_id` (`session_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24156 DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%user_access_log` VALUES (24155,1,'','127.0.0.1','',NULL,'2015-03-17 11:44:22','0000-00-00 00:00:00','2015-03-18 13:55:53',NULL,NULL,NULL);


CREATE TABLE `%dbprefix%user_events` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL DEFAULT '0',
  KEY `uek1` (`user_id`,`event_id`),
  KEY `uek2` (`event_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%user_preferences` (
  `pref_user` varchar(12) NOT NULL DEFAULT '',
  `pref_name` varchar(72) NOT NULL DEFAULT '',
  `pref_value` varchar(32) NOT NULL DEFAULT '',
  KEY `pref_user` (`pref_user`,`pref_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%user_roles` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%user_task_pin` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `task_id` int(10) NOT NULL DEFAULT '0',
  `task_pinned` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`,`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%user_tasks` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_type` tinyint(4) NOT NULL DEFAULT '0',
  `task_id` int(11) NOT NULL DEFAULT '0',
  `perc_assignment` int(11) NOT NULL DEFAULT '100',
  `user_task_priority` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`user_id`,`task_id`),
  KEY `user_type` (`user_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `%dbprefix%users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_contact` int(11) NOT NULL DEFAULT '0',
  `user_username` varchar(255) NOT NULL DEFAULT '',
  `user_password` varchar(32) NOT NULL DEFAULT '',
  `user_parent` int(11) NOT NULL DEFAULT '0',
  `user_type` tinyint(3) NOT NULL DEFAULT '0',
  `user_company` int(11) DEFAULT '0',
  `user_department` int(11) DEFAULT '0',
  `user_owner` int(11) NOT NULL DEFAULT '0',
  `user_signature` text,
  PRIMARY KEY (`user_id`),
  KEY `idx_uid` (`user_username`),
  KEY `idx_pwd` (`user_password`),
  KEY `idx_user_parent` (`user_parent`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

INSERT INTO `%dbprefix%users` VALUES (1,61,'admin','76a2173be6393254e72ffa4d6df1030a',0,1,0,0,0,'Jean Lelarge\r\nDatabase Administrator\r\nwww.ppkl.net'),(6,62,'guess','084e0343a0486ff05530df6c705c8bb4',0,0,0,0,0,'guest');


CREATE TABLE `tasks_critical` (
  `task_project` varchar(10) DEFAULT NULL,
  `critical_task` int(11) DEFAULT NULL,
  `project_actual_end_date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `tasks_problems` (
  `task_project` varchar(10) DEFAULT NULL,
  `task_log_problem` tinyint(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `tasks_sum` (
  `task_project` varchar(10) DEFAULT NULL,
  `total_tasks` int(6) DEFAULT NULL,
  `project_percent_complete` varchar(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE `tasks_summy` (
  `task_project` varchar(10) DEFAULT NULL,
  `my_tasks` varchar(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

