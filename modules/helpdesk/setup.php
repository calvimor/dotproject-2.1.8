<?php /* HELPDESK $Id: setup.php,v 1.47 2005/10/07 16:09:10 pedroix Exp $ */

/* Help Desk module definitions */
$config = array();
$config['mod_name'] = 'HelpDesk';
$config['mod_version'] = '0.6';
$config['mod_directory'] = 'helpdesk';
$config['mod_setup_class'] = 'CSetupHelpDesk';
$config['mod_type'] = 'user';
$config['mod_config'] = true;
$config['mod_ui_name'] = 'Help Desk';
$config['mod_ui_icon'] = 'helpdesk.png';
$config['mod_description'] = 'Help Desk is a bug, feature request, '
                           . 'complaint and suggestion tracking centre';
//This will allow permissions to be applied to this module based on the following database criteria
$config['permissions_item_table'] = 'companies';
$config['permissions_item_label'] = 'company_name';
$config['permissions_item_field'] = 'company_id';

if (@$a == 'setup') {
	print dPshowModuleConfig( $config );
}

require_once( $dPconfig['root_dir'].'/modules/system/syskeys/syskeys.class.php');

class CSetupHelpDesk {
	
	var $dbPrefix = null;
	
	function install() {
		
		$this->dbPrefix = dPgetConfig('dbprefix');
	
		$success = 1;
		$bulk_sql[] = "
			CREATE TABLE " . $this->dbPrefix . "helpdesk_items (
			  `item_id` int(11) unsigned NOT NULL auto_increment,
			  `item_title` varchar(64) NOT NULL default '',
			  `item_summary` text,
			  `item_calltype` int(3) unsigned NOT NULL default '0',
			  `item_source` int(3) unsigned NOT NULL default '0',
			  `item_os` varchar(48) NOT NULL default '',
			  `item_application` varchar(48) NOT NULL default '',
			  `item_priority` int(3) unsigned NOT NULL default '0',
			  `item_severity` int(3) unsigned NOT NULL default '0',
			  `item_status` int(3) unsigned NOT NULL default '0',
			  `item_assigned_to` int(11) NOT NULL default '0',
			  `item_created_by` int(11) NOT NULL default '0',
			  `item_notify` int(1) DEFAULT '1' NOT NULL ,
			  `item_requestor` varchar(48) NOT NULL default '',
			  `item_requestor_id` int(11) NOT NULL default '0',
			  `item_requestor_email` varchar(128) NOT NULL default '',
			  `item_requestor_phone` varchar(30) NOT NULL default '',
			  `item_requestor_type` tinyint NOT NULL default '0',
			  `item_created` datetime default NULL,
			  `item_modified` datetime default NULL,
			  `item_parent` int(10) unsigned NOT NULL default '0',
			  `item_project_id` int(11) NOT NULL default '0',
			  `item_company_id` int(11) NOT NULL default '0',
			  `item_updated` datetime default NULL,
			  `item_deadline` datetime default NULL,
			  PRIMARY KEY (item_id)
			) ENGINE=MyISAM";

		$bulk_sql[] = "
      ALTER TABLE `task_log`
      ADD `task_log_help_desk_id` int(11) NOT NULL default '0' AFTER `task_log_task`
    ";

		$bulk_sql[] = "
		  CREATE TABLE `" . $this->dbPrefix . "helpdesk_item_status` (
		    `status_id` INT NOT NULL AUTO_INCREMENT,
		    `status_item_id` INT NOT NULL,
		    `status_code` TINYINT NOT NULL,
		    `status_date` TIMESTAMP NOT NULL,
		    `status_modified_by` INT NOT NULL,
		    `status_comment` TEXT DEFAULT '',
		    PRIMARY KEY (`status_id`)
		  )";

		$bulk_sql[] = "
		CREATE TABLE " . $this->dbPrefix . "helpdesk_item_watchers (
		  `item_id` int(11) NOT NULL default '0',
		  `user_id` int(11) NOT NULL default '0',
		  `notify` char(1) NOT NULL default ''
		)ENGINE=MyISAM";

    foreach ($bulk_sql as $s) {
      db_exec($s);

      if (db_error()) {
	      $success = 0;
      }
    }

		$sk = new CSysKey( 'HelpDeskList', 'Enter values for list', '0', "\n", '|' );
		$sk->store();

		$sv = new CSysVal( $sk->syskey_id, 'HelpDeskPriority', "0|Not Specified\n1|Low\n2|Medium\n3|High" );
		$sv->store();

		$sv = new CSysVal( $sk->syskey_id, 'HelpDeskSeverity', "0|Not Specified\n1|No Impact\n2|Low\n3|Medium\n4|High\n5|Critical" );
		$sv->store();

		/*$sv = new CSysVal( $sk->syskey_id, 'HelpDeskCallType', "0|Not Specified\n1|Bug\n2|Feature Request\n3|Complaint\n4|Suggestion" );
		$sv->store();*/
		$sv = new CSysVal( $sk->syskey_id, 'HelpDeskCallType', "0|Not Specified\n1|Bug\n2|Feature Request\n3|Complaint\n4|Suggestion" );
		$sv->store();

		$sv = new CSysVal( $sk->syskey_id, 'HelpDeskSource', "0|Not Specified\n1|E-Mail\n2|Phone\n3|Fax\n4|In Person\n5|E-Lodged\n6|WWW" );
		$sv->store();

		$sv = new CSysVal( $sk->syskey_id, 'HelpDeskOS', "0|Not Applicable\n1|Linux\n2|Unix\n3|Solaris 8\n4|Solaris 9\n5|Red Hat 6\n6|Red Hat 7\n7|Red Hat 8\n8|Windows 95\n9|Window 98\n10|Windows 2000\n11|Window 2000 Server\n12|Windows XP" );
		$sv->store();

		$sv = new CSysVal( $sk->syskey_id, 'HelpDeskApplic', "0|Not Applicable\n1|Word\n2|Excel" );
		$sv->store();

		$sv = new CSysVal( $sk->syskey_id, 'HelpDeskStatus', "0|Unassigned\n1|Open\n2|Closed\n3|On Hold\n4|Testing" );
		$sv->store();

		$sv = new CSysVal( $sk->syskey_id, 'HelpDeskAuditTrail', "0|Created\n1|Title\n2|Requestor Name\n3|Requestor E-mail\n4|Requestor Phone\n5|Assigned To\n6|Notify by e-mail\n7|Company\n8|Project\n9|Call Type\n10|Call Source\n11|Status\n12|Priority\n13|Severity\n14|Operating System\n15|Application\n16|Summary\n17|Deleted" );
		$sv->store();
		
	  return $success;
	}

	function remove() {
		
		$this->dbPrefix = dPgetConfig('dbprefix');

		$success = 1;

		$bulk_sql[] = "DROP TABLE " . $this->dbPrefix . "helpdesk_items";
		$bulk_sql[] = "DROP TABLE " . $this->dbPrefix . "helpdesk_item_status";
		$bulk_sql[] = "DROP TABLE " . $this->dbPrefix . "helpdesk_item_watchers";
		$bulk_sql[] = "ALTER TABLE `task_log` DROP COLUMN `task_log_help_desk_id`";

		foreach ($bulk_sql as $s) {
			db_exec($s);
			if (db_error())
				$success = 0;
		}

		$sql = "
			SELECT syskey_id
			FROM " . $this->dbPrefix . "syskeys
			WHERE syskey_name = 'HelpDeskList'";
		$id = db_loadResult( $sql );

  	unset($bulk_sql);

		$bulk_sql[] = "DELETE FROM " . $this->dbPrefix . "syskeys WHERE syskey_id = $id";
		$bulk_sql[] = "DELETE FROM " . $this->dbPrefix . "sysvals WHERE sysval_key_id = $id";

		foreach ($bulk_sql as $s) {
			db_exec($s);
			if (db_error())
				$success = 0;
		}

		return $success;
	}

	function upgrade($old_version) {
		$success = 1;

		$this->dbPrefix = dPgetConfig('dbprefix');

	  switch ($old_version) {
	    case "0.1":
        // Drop unused columns, add some new columns
        $bulk_sql[] = "
          ALTER TABLE `" . $this->dbPrefix . "helpdesk_items`
          ADD `item_requestor_phone` varchar(30) NOT NULL default '' AFTER `item_requestor_email`,
          ADD `item_company_id` int(11) NOT NULL default '0' AFTER `item_project_id`,
          ADD `item_requestor_type` tinyint NOT NULL default '0' AFTER `item_requestor_phone`,
          ADD `item_notify` int(1) DEFAULT '1' NOT NULL AFTER `item_assigned_to`,
          ADD `item_created_by` int(11) NOT NULL default '0',
	  ADD `item_updated` datetime default NULL,
	  ADD `item_deadline` datetime default NULL,
          DROP `item_receipt_target`,
          DROP `item_receipt_custom`,
          DROP `item_receipted`,
          DROP `item_resolve_target`,
          DROP `item_resolve_custom`,
          DROP `item_resolved`,
          DROP `item_assetno`
        ";

        // Add help desk item id to task log table
        $bulk_sql[] = "
          ALTER TABLE `task_log`
          ADD `task_log_help_desk_id` int(11) NOT NULL default '0' AFTER `task_log_task`
        ";

        // Add help desk item status log table
        $bulk_sql[] = "
          CREATE TABLE `" . $this->dbPrefix . "helpdesk_item_status` (
            `status_id` INT NOT NULL AUTO_INCREMENT,
            `status_item_id` INT NOT NULL,
            `status_code` TINYINT NOT NULL,
            `status_date` TIMESTAMP NOT NULL,
            `status_modified_by` INT NOT NULL,
            `status_comment` TEXT DEFAULT '',
            PRIMARY KEY (`status_id`)
          )
        ";

        // Execute the above SQL
        foreach ($bulk_sql as $s) {
          db_exec($s);
          if (db_error())
            $success = 0;
        }

        // Add audit trail to system values
        $sql = "SELECT syskey_id
                FROM " . $this->dbPrefix . "syskeys
                WHERE syskey_name = 'HelpDeskList'";

        $syskey_id = db_loadResult( $sql );

        $sv = new CSysVal( $syskey_id, 'HelpDeskAuditTrail', "0|Created\n1|Title\n2|Requestor Name\n3|Requestor E-mail\n4|Requestor Phone\n5|Assigned To\n6|Notify by e-mail\n7|Company\n8|Project\n9|Call Type\n10|Call Source\n11|Status\n12|Priority\n13|Severity\n14|Operating System\n15|Application\n16|Summary\n17|Deleted" );
        $sv->store();

        // Update help desk status values
        $sql = "UPDATE " . $this->dbPrefix . "sysvals
                SET sysval_value='0|Unassigned\n1|Open\n2|Closed\n3|On Hold\n4|Testing'
                WHERE sysval_title='HelpDeskStatus'
                LIMIT 1";

        db_exec($sql);

        /* Get data for conversion update */
        $sql = "SELECT item_id,item_requestor_id,item_created,item_project_id
                FROM " . $this->dbPrefix . "helpdesk_items";

        $items = db_loadList($sql);

        /* Populate the status log table with the item's creation date */
        foreach ($items as $item) {
          $timestamp = date('Ymdhis', db_dateTime2unix($item['item_created']));

          $sql = "INSERT INTO " . $this->dbPrefix . "helpdesk_item_status
                    (status_item_id,status_code,status_date,status_modified_by)
                  VALUES ({$item['item_id']},0,'$timestamp',
                          {$item['item_requestor_id']})";
        
          db_exec($sql);
        }

        /* Figure out the company for each item based on project id or based
           on requestor id */
        foreach ($items as $item) {
          if ($item['item_project_id']) {
            $sql = "SELECT project_company
                    FROM " . $this->dbPrefix . "projects
                    WHERE project_id='{$item['item_project_id']}'";

            $company_id = db_loadResult($sql);
          } else if ($item['item_requestor_id']) {
            $sql = "SELECT user_company
                    FROM " . $this->dbPrefix . "users
                    WHERE user_id='{$item['item_requestor_id']}'";

            $company_id = db_loadResult($sql);
          }

          if ($company_id) {
            $sql = "UPDATE " . $this->dbPrefix . "helpdesk_items
                    SET item_company_id='$company_id'
                    WHERE item_id='{$item['item_id']}'";

            db_exec($sql);
          }
        }

        // If our status was 5 (Testing), now it is 4 (Testing)
        $sql = "UPDATE helpdesk_items
                SET item_status='4'
                WHERE item_status='5'";

        db_exec($sql);

        break;
      case 0.2:
        // Version 0.3 features new permissions
        $success = 1;
        break;
      case 0.3:
        // Version 0.31 includes new watchers functionality
		$sql = "
		CREATE TABLE " . $this->dbPrefix . "helpdesk_item_watchers (
		  `item_id` int(11) NOT NULL default '0',
		  `user_id` int(11) NOT NULL default '0',
		  `notify` char(1) NOT NULL default ''
		) TYPE=MyISAM";
		db_exec($sql);
	  case 0.31:
	    $sql = "
          ALTER TABLE `" . $this->dbPrefix . "helpdesk_items`
	  ADD `item_updated` datetime default NULL,
	  ADD `item_deadline` datetime default NULL";
		db_exec($sql);
	    $sql = "SELECT `item_id` FROM " . $this->dbPrefix . "helpdesk_items";
		$rows = db_loadList( $sql );
		$sql = '';
		foreach ($rows as $row) {
	    	$sql = "SELECT MAX(status_date) status_date FROM " . $this->dbPrefix . "helpdesk_item_status WHERE status_item_id =".$row['item_id'];
			$sdrow = db_loadList( $sql );

		    $sql = '';
			$sql = "UPDATE `" . $this->dbPrefix . "helpdesk_items`
    	  	SET `item_updated`='".$sdrow[0]['status_date']."' 
    	  	WHERE `item_id`=".$row['item_id'];
			db_exec($sql);			
		}
	if (db_error())
		$success = 0;
	else  
	        $success = 1;
        break;
     case 0.4:
     	$sql = "ALTER TABLE `" . $this->dbPrefix . "helpdesk_items`
		ADD `item_deadline` datetime default NULL";
	db_exec($sql);
	if (db_error())
		$success = 0;
	else
		$success = 1;
	break;
						      
     	
      default:
        $success = 0;
	  }

		// NOTE: Need to return true, not null, if all is good
		return $success;
	}

  function configure() {
    global $AppUI;

    $AppUI->redirect("m=helpdesk&a=configure");

    return true;
  }
}
?>


