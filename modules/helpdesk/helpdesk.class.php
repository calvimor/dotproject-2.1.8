<?php /* HELPDESK $Id: helpdesk.class.php 265 2006-12-14 18:06:35Z kang $ */
//KZHAO 10-24-2006
// Use mutlipart header and send emails in two formats
require_once( $AppUI->getSystemClass( 'dp' ) );
require_once( $AppUI->getSystemClass( 'libmail' ) );
include_once("helpdesk.functions.php");
include_once("./modules/helpdesk/config.php");
require_once $AppUI->getSystemClass('date');

// Make sure we can read the module
//if (getDenyRead($m)) {
	//$AppUI->redirect( "m=public&a=access_denied" );
//}

// Define log types
define("STATUS_LOG", 1);
define("TASK_LOG", 2);

// Pull in some standard arrays
$ict = dPgetSysVal( 'HelpDeskCallType' );
$ics = dPgetSysVal( 'HelpDeskSource' );
$ios = dPgetSysVal( 'HelpDeskOS' );
$iap = dPgetSysVal( 'HelpDeskApplic' );
$ipr = dPgetSysVal( 'HelpDeskPriority' );
$isv = dPgetSysVal( 'HelpDeskSeverity' );
$ist = dPgetSysVal( 'HelpDeskStatus' );
$isa = dPgetSysVal( 'HelpDeskAuditTrail' );

//$field_event_map = $isa;
///*
$field_event_map = array(
//0=>Created
  1=>"item_title",            //Title
  2=>"item_requestor",        //Requestor Name
  3=>"item_requestor_email",  //Requestor E-mail
  4=>"item_requestor_phone",  //Requestor Phone
  5=>"item_assigned_to",      //Assigned To
  6=>"item_notify",           //Notify by e-mail
  7=>"item_company_id",       //Company
  8=>"item_project_id",       //Project
  9=>"item_calltype",         //Call Type
  10=>"item_source",          //Call Source
  11=>"item_status",          //Status
  12=>"item_priority",        //Priority
  13=>"item_severity",        //Severity
  14=>"item_os",              //Operating System
  15=>"item_application",     //Application
  16=>"item_summary",         //Summary
  17=>"item_deadline"	      //Deadline
 // 18=>Deleted
);
// */  
// Help Desk class
class CHelpDeskItem extends CDpObject {
  var $item_id = NULL;
  var $item_title = NULL;
  var $item_summary = NULL;

  var $item_calltype = NULL;
  var $item_source = NULL;
  var $item_os = NULL;
  var $item_application = NULL;
  var $item_priority = NULL;
  var $item_severity = NULL;
  var $item_status = NULL;
  var $item_project_id = NULL;
  var $item_company_id = NULL;

  var $item_assigned_to = NULL;
  var $item_notify = 0;
  var $item_requestor = NULL;
  var $item_requestor_id = NULL;
  var $item_requestor_email = NULL;
  var $item_requestor_phone = NULL;
  var $item_requestor_type = NULL;

  var $item_created_by = NULL;
  var $item_created = NULL;
  var $item_modified = NULL;
  var $item_updated = NULL;
  var $item_deadline =NULL;
  
  function CHelpDeskItem() {
  
		$table	= 'helpdesk_items';
		$key 	= 'item_id';
		$perm_name = '';
		
		$this->_tbl		= $table;
		$this->_tbl_key = $key;
		$this->_permission_name = (($perm_name) ? $perm_name : $table);
		$this->_query = new DBQuery;
  
  }

  function check() {
    if ($this->item_id === NULL) {

//Had to remove this check or else we couldn't add tasklogs
//      return ("$AppUI->_('Help Desk Item ID is NULL')");
    }
    if (!$this->item_created) { 
      $this->item_created = new CDate();
  	  $this->item_created = $this->item_created->format( FMT_DATETIME_MYSQL );
    }
    
    // TODO More checks
    return NULL;
  }

  function store() {
    global $AppUI;

    // Update the last modified time and user
    //$this->item_created = new CDate();
    
    $this->item_summary = strip_tags($this->item_summary);

    //if type indicates a contact or a user, then look up that phone and email
    //for those entries
    switch ($this->item_requestor_type) {
      case '0'://it's not a user or a contact
        break;
      case '1'://it's a system user
		$q = new DBQuery();
		$q->addTable('users','u');
		$q->addQuery('u.user_id as id');
		$q->addJoin('contacts','c','u.user_contact = c.contact_id');
		$q->addQuery("c.contact_email as email, c.contact_phone as phone, CONCAT(c.contact_first_name,' ', c.contact_last_name) as name");
		// KZHAO  8-3-2006
		$q->addWhere('u.user_id='.$this->item_requestor_id);
                
        break;
      case '2': //it's only a contact
		$q = new DBQuery();
		$q->addTable('contacts','c');
		$q->addQuery("c.contact_email as email, c.contact_phone as phone, CONCAT(c.contact_first_name,' ', c.contact_last_name) as name");
		$q->addWhere('contact_id='.$this->item_requestor_id);
                
        break;
      default:
        break;
    }
    // get requestor's information 
    if(isset($q)) {
      $result = $q->loadHash();
      $q->clear();
      $this->item_requestor_email = $result['email'];
      $this->item_requestor_phone = $result['phone'];
      $this->item_requestor = $result['name'];
    }
      
    /* if the store is successful, pull the new id value and insert it into the 
       object. */
    // call parent class' store method to insert this record into DB
    if (($msg = parent::store())) {
	    return $msg;
    } else {
	    if(!$this->item_id){  
	    	$this->item_id = mysql_insert_id();
	    }
	    return $msg;
    }
  }

  function delete() {
	  
		// This section will grant every request to delete an HPitem
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}
		//load the item first so we can get the item_title for history
		$this->load($this->item_id);
		addHistory($this->_tbl, $this->$k, 'delete', $this->item_title, $this->item_project_id);
		$result = null;
		$q  = new DBQuery;
		$q->setDelete($this->_tbl);
		$q->addWhere("$this->_tbl_key = '".$this->$k."'");
		if (!$q->exec()) {
			$result = db_error();
		}
		$q->clear();
		$q->setDelete('helpdesk_item_status');
		$q->addWhere("status_item_id = '".$this->item_id."'");
		if (!$q->exec()) {
			$result .= db_error();
		}
		$q->clear();
		$q->setDelete('helpdesk_item_watchers');
		$q->addWhere("item_id = '".$this->item_id."'");
		if (!$q->exec()) {
			$result .= db_error();
		}
		$q->clear();
		$q->setDelete('task_log');
		$q->addWhere("task_log_help_desk_id = '".$this->item_id."'");
		if (!$q->exec()) {
			$result .= db_error();
		}
		$q->clear();
		return $result;	
  }
  
  function oldnotify($type, $log_id, $newhdi=0) {
    global $AppUI, $ist, $ict, $isa, $dPconfig, $HELPDESK_CONFIG;

    // Pull up the email address of everyone on the watch list 
    // this list does not include the assignee
	$q = new DBQuery();
	$q->addTable('helpdesk_item_watchers','hdw');
	$q->addQuery('DISTINCT c.contact_email');
	$q->addJoin('users','u','hdw.user_id = u.user_id');
	$q->addJoin('contacts','c','u.user_contact = c.contact_id');
	$q->addWhere("hdw.item_id='".$this->item_id."'");

    $email_list = $q->loadHashList();
    $q->clear();
    $email_list = array_keys($email_list);
     
    //add the requestor email to the list of mailing people
    //$notify_Req=1;
    //if($notify_Req){
    $email_list[] = $this->item_requestor_email;
    // }
     
        //add the assigned user email to the list of mailing people
    	$assigned_user_email = array();
	$q = new DBQuery();
    	$q->addTable('users','u');
    	$q->addQuery('DISTINCT c.contact_email');
	$q->addJoin('contacts','c','u.user_contact = c.contact_id');
    	$q->addWhere('u.user_id='.$this->item_assigned_to);
    	$assigned_user_email = $q->loadHashList();
    	$assigned_user_email = array_keys($assigned_user_email);
	//print_r($assigned_user_email);
	//echo "<br>";
    	foreach ($assigned_user_email as $user_email) {
        	    if (trim($user_email)) {
                	  $email_list[] = $user_email;
            	    }
    	}
    	$q->clear();
    
    //echo $sql."\n";
    //if there's no one in the list, skip the rest.
    if(count($email_list)<=0)
      return;

    if (is_numeric($log_id)) {
      switch ($type) {
        case STATUS_LOG:
			$q = new DBQuery();
			$q->addTable('helpdesk_item_status','hds');
			$q->addQuery('hds.status_code, hds.status_comment');
			$q->addWhere('hds.status_id='.$log_id);
          break;
        case TASK_LOG:
			$q = new DBQuery();
			$q->addTable('task_log','tl');
			$q->addQuery('tl.task_log_name, tl.task_log_description');
			$q->addWhere('tl.task_log_id='.$log_id);
          break;
      }
        
      $log=$q->loadHash();
    }
      
      $email_list=array_unique($email_list);
      foreach($email_list as $assigned_to_email){
	    $mail = new Mail;
	    if ($mail->ValidEmail($assigned_to_email)) {
		// KZHAO 9-12-2006
		// Use subject and header from config.php
		$subject = $HELPDESK_CONFIG['email_subject']."---Ticket#{$this->item_id}";
		//$subject = $AppUI->cfg['page_title']." ".$AppUI->_('Help Desk Item')." #{$this->item_id}";
                $body = "Thank you for registering your request with us!\n\n";
		$body.= $HELPDESK_CONFIG['email_header']."\n";
		
	      switch ($type) {
		case STATUS_LOG:
		  $body .= $AppUI->_('Title').": {$this->item_title}\n";
		  $body .= $AppUI->_('Call Type').": {$ict[$this->item_calltype]}\n"
			. $AppUI->_('Status').": {$ist[$this->item_status]}\n";

		  if($newhdi){
		    $mail->Subject("$subject ".$AppUI->_('Created'));
		  } else {
		    $mail->Subject("$subject ".$AppUI->_('Updated'));
		    $body .= $AppUI->_('Update').": {$isa[$log['status_code']]} {$log['status_comment']}\n";
		  }

		  $body .= $AppUI->_('Link')
			 . ": {$dPconfig['base_url']}"."/index.php?m=helpdesk&a=view&item_id={$this->item_id}\n"
			 . "\n"
			 . $AppUI->_('Summary')
			 . ":\n"
			 . $this->item_summary;
		  break;
		case TASK_LOG:
		  $mail->Subject("$subject ".$AppUI->_('Task Log')." ".$AppUI->_('Update'));
		  $body .= $AppUI->_('Summary')
			. ": "
			. $log['task_log_name']
			. "\n"
			. $AppUI->_('Link')
			. ": {$dPconfig['base_url']}"."/index.php?m=helpdesk&a=view&item_id={$this->item_id}\n"
			. "\n"
			. $AppUI->_('Comments')
			. ":\n" 
			. $log['task_log_description'];
		  break;
		//default:
			//return;
			//break;
	      }

	      $body .= "\n\n-- \n"
		     . $AppUI->_('helpdeskSignature');

	      if ($mail->ValidEmail($AppUI->user_email)) {
		$email = $AppUI->user_email;
	      } else {
		$email = "admin@".$AppUI->cfg['site_domain'];
	      }
	      //echo $assigned_to_email;
	      //echo "<br>";
	      $mail->From("\"{$AppUI->user_first_name} {$AppUI->user_last_name}\" <{$email}>");
	      $mail->To($assigned_to_email);
	      $mail->Body($body, isset( $GLOBALS['locale_char_set']) ? $GLOBALS['locale_char_set'] : "");
	      $mail->Send();
      }
    }
  }
//KZHAO: 9-13-2006
// New notify mechanism to send HTML and text email ---work with Sendmail only
  function notify($type, $log_id, $newhdi=0) {
    global $AppUI, $ist, $ict, $isa, $dPconfig, $HELPDESK_CONFIG;

    // Pull up the email address of everyone on the watch list 
    // this list does not include the assignee
	$q = new DBQuery();
	$q->addTable('helpdesk_item_watchers','hdw');
	$q->addQuery('DISTINCT c.contact_email');
	$q->addJoin('users','u','hdw.user_id = u.user_id');
	$q->addJoin('contacts','c','u.user_contact = c.contact_id');
	$q->addWhere("hdw.item_id='".$this->item_id."'");
		/*	AND u.user_id <>'".$this->item_assigned_to."'");*/

    $email_list = $q->loadHashList();
    $q->clear();
    $email_list = array_keys($email_list);
     
    //add the requestor email to the list of mailing people
    $email_list[] = $this->item_requestor_email;
    
        //add the assigned user email to the list of mailing people
    	$assigned_user_email = array();
	$q = new DBQuery();
    	$q->addTable('users','u');
    	$q->addQuery('DISTINCT c.contact_email');
	$q->addJoin('contacts','c','u.user_contact = c.contact_id');
    	$q->addWhere('u.user_id='.$this->item_assigned_to);
    	$assigned_user_email = $q->loadHashList();
    	$assigned_user_email = array_keys($assigned_user_email);
	//print_r($assigned_user_email);
	//echo "<br>";
    	foreach ($assigned_user_email as $user_email) {
        	    if (trim($user_email)) {
                	  $email_list[] = $user_email;
            	    }
    	}
    	$q->clear();
    
    //if there's no one in the list, skip the rest.
    if(count($email_list)<=0)
      return;

    if (is_numeric($log_id)) {
      switch ($type) {
        case STATUS_LOG:
			$q = new DBQuery();
			$q->addTable('helpdesk_item_status','hds');
			$q->addQuery('hds.status_code, hds.status_comment');
			$q->addWhere('hds.status_id='.$log_id);
          break;
        case TASK_LOG:
			$q = new DBQuery();
			$q->addTable('task_log','tl');
			$q->addQuery('tl.task_log_name, tl.task_log_description');
			$q->addWhere('tl.task_log_id='.$log_id);
          break;
      }
        
      $log=$q->loadHash();
    }
      
      $email_list=array_unique($email_list);
      $boundary = "_lkqwkASDHASK89271893712893";
      foreach($email_list as $assigned_to_email){
	    //$mail = new Mail;
	    //if ($mail->ValidEmail($assigned_to_email)) {
		// KZHAO 9-12-2006
		// Use subject and header from config.php
		$to=$assigned_to_email;
		$subject = $HELPDESK_CONFIG['email_subject']."---Ticket#{$this->item_id} ";
		//$subject = $AppUI->cfg['page_title']." ".$AppUI->_('Help Desk Item')." #{$this->item_id}";
                
		//KZHAO 10-23-2006
                // New headers for HTML and Text formats
                //$body ="Content-type: multipart/alternative; boundary=\"$boundary\"\n";
                //$body .="Mime-Version: 1.0\n\n";
                $body ="--$boundary\n";
                $body .="Content-disposition: inline\n";
                $body .="Content-type: text/plain\n\n";
		//$body .= "Notification Email in text format\n\n";
		$body .="Thank you for registering your request with us!\n\n";
		$body .= $HELPDESK_CONFIG{'email_header'}."\n";
		//$body .=$HELPDESK_CONFIG['notity_email_address']."----\n";
		$body .="Ticket ID: ".$this->item_id."\n";
		$body .="Requestor: ".$this->item_requestor."\n";
		
		switch ($type) {
			case STATUS_LOG:
				$body .="Subject  : ".$this->item_title."\n";
				$body .="Call Type: ".$ict[$this->item_calltype]."\n";
				$body .="Status   : ".$ist[$this->item_status]."\n";
				
				if(!$newhdi){
					$body .="Update   : {$isa[$log['status_code']]} {$log['status_comment']}\n"; 		
				}
				
				$body .= "Link     : ".$dPconfig['base_url']."/index.php?m=helpdesk&a=view&item_id=".$this->item_id."\n";	
				$body .= "Summary  :\n".$this->item_summary."\n";
				break;

			 case TASK_LOG:
				$body .= $AppUI->_('Summary')."  : ".$log['task_log_name']."\n";	
				$body .= $AppUI->_('Link')."     : ".$dPconfig['base_url']."/index.php?m=helpdesk&a=view&item_id={$this->item_id}\n";
				$body .= $AppUI->_('Comments')." : ".$log['task_log_description']."\n";
				break;
		}																														     
		
		$body.= "\n--$boundary\n";
		$body.= "Content-disposition: inline\n";
		$body.= "Content-type: text/html\n\n";
		$body.= "<html><head><title>";
		$body.= $HELPDESK_CONFIG['email_header'];
		$body.= "</title></head>\n";
		$body.= "<body>\n<p>Thank you for registering your request with us!</p>\n";
		$body.= "<TABLE border=0 cellpadding=4 cellspacing=1>\n";
		$body.= "    <TR>\n";
		$body.= "            <TD nowrap><span class=title><strong>".$HELPDESK_CONFIG{'email_header'}."</strong></span></td>\n";
		$body.= "            <TD valign=top align=right width=100%>&nbsp;</td>\n";
		$body.= "    </TR>\n";
		$body.= "</TABLE>\n";
		$body.= "<TABLE width=600 border=0 cellpadding=4 cellspacing=1 bgcolor=#878676>\n";
		$body.= "    <TR>\n";
			
		$body.= "                <TD colspan=2><font face=arial,san-serif size=2 color=white>Ticket Detail</font></TD>\n";
		$body.= "    <TR>\n";
		$body.= "            <TD bgcolor=white nowrap class=td>Ticket ID:</TD>\n";
		$body.= "            <TD bgcolor=white nowrap class=td>".$this->item_id."</TD>\n";
		$body.= "    </tr>\n";
		$body.= "            <TD bgcolor=white class=td>Requestor:</TD>\n";
		$body.= "            <TD bgcolor=white class=td>".$this->item_requestor."</TD>\n";
		$body.= "    </tr>\n";
				
	      switch ($type) {
		case STATUS_LOG:
		  $body .= "    <TR>\n";
		  $body .= "            <TD bgcolor=white class=td>".$AppUI->_('Subject')."</TD>\n";
		  $body .= "            <TD bgcolor=white><font face=arial,san-serif size=2>".$this->item_title."</font></TD>";
		  $body .= "    </tr>\n";
		  $body .= "    <TR>\n";
                  $body .= "            <TD bgcolor=white class=td>".$AppUI->_('Call Type')."</TD>\n";
                  $body .= "            <TD bgcolor=white><font face=arial,san-serif size=2>".$ict[$this->item_calltype]."</font></TD>";
                  $body .= "    </tr>\n";
		  $body .= "    <TR>\n";
                  $body .= "            <TD bgcolor=white class=td>".$AppUI->_('Status')."</TD>\n";
                  $body .= "            <TD bgcolor=white><font face=arial,san-serif size=2>".$ist[$this->item_status]."</font></TD>";
                  $body .= "    </tr>\n";
		  
		  if($newhdi){
		  	$subject.=$AppUI->_(' Created');
		  } 
		  else {
		  	$subject.=$AppUI->_(' Updated');
		        $body .= "    <TR>\n";
                        $body .= "            <TD bgcolor=white class=td>".$AppUI->_('Update')."</TD>\n";
                        $body .= "            <TD bgcolor=white><font face=arial,san-serif size=2>{$isa[$log['status_code']]} {$log['status_comment']}</font></TD>";
                        $body .= "    </tr>\n";
			//$body .= $AppUI->_('Update').": {$isa[$log['status_code']]} {$log['status_comment']}\n";
		  }
		  $body .= "    <TR>\n";
                  $body .= "            <TD bgcolor=white class=td>".$AppUI->_('Link')."</TD>\n";
                  $body .= "            <TD bgcolor=white><font face=arial,san-serif size=2><a href={$dPconfig['base_url']}"."/index.php?m=helpdesk&a=view&item_id={$this->item_id}";
		  $body .='>'.$dPconfig['base_url'].'/index.php?m=helpdesk&a=view&item_id='.$this->item_id.'</a></font></TD>';
                  $body .= "    </tr>\n";
									

                  $body .= "    <TR>\n";
                  $body .= "            <TD bgcolor=white class=td>".$AppUI->_('Summary')."</TD>\n";
                  $body .= "            <TD bgcolor=white><font face=arial,san-serif size=2>".$this->item_summary."</font></TD>";
                  $body .= "    </tr>\n";
		
		  break;
	
		case TASK_LOG:
		  $subject.= " ".$AppUI->_('Task Log')." ".$AppUI->_('Update');
	          $body .= "    <TR>\n";
	          $body .= "            <TD bgcolor=white class=td>".$AppUI->_('Summary')."</TD>\n";
		  $body .= "            <TD bgcolor=white><font face=arial,san-serif size=2>".$log['task_log_name']."</font></TD>";
		  $body .= "    </tr>\n";

		  $body .= "    <TR>\n";
                  $body .= "            <TD bgcolor=white class=td>".$AppUI->_('Link')."</TD>\n";
                  $body .= "            <TD bgcolor=white><font face=arial,san-serif size=2>".$dPconfig['base_url']."/index.php?m=helpdesk&a=view&item_id={$this->item_id}</font></TD>";
                  $body .= "    </tr>\n";
									
		  $body .= "    <TR>\n";
                  $body .= "            <TD bgcolor=white class=td>".$AppUI->_('Comments')."</TD>\n";
                  $body .= "            <TD bgcolor=white><font face=arial,san-serif size=2>".$log['task_log_description']."</font></TD>";
                  $body .= "    </tr>\n";
									
		  break;
		//default:
			//return;
			//break;
	      }
		$body .= "</TABLE></body></html>";
		
	       // To send HTML mail, the Content-type header must be set
	       //$headers  = 'MIME-Version: 1.0' . "\r\n";
	       //$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	       	     
	       $headers = 'To:'.$to. "\r\n";
	       $headers .= 'From:'. $HELPDESK_CONFIG['notity_email_address']."\r\n";

	       //KZHAO 10-23-2006
	       // New headers for HTML and Text formats
	       $headers .="Content-type: multipart/alternative; boundary=\"$boundary\"\n";
	       $headers .="Mime-Version: 1.0\n\n";
	      
	      // Mail it
	      if(! mail($to, $subject, $body, $headers))
            $AppUI->setMsg("Email notification failed!", UI_MSG_ERROR);
    }
  }
 /////////////////////////////////////////// 
  function log_status_changes() {
    global $ist, $ict, $ics, $ios, $iap, $ipr, $isv, $ist, $isa,
    $field_event_map, $AppUI;
    
    $dbPrefix = dPgetConfig( 'dbprefix' );

    if(dPgetParam( $_POST, "item_id")){
       $hditem = new CHelpDeskItem();
       $hditem->load( dPgetParam( $_POST, "item_id") );
      
      //echo "field event map:<br>";
      //print_r($field_event_map);
      //echo "end <br>";
      $count=0;
      //var_dump( $field_event_map );
      //die();
      foreach($field_event_map as $key => $value){
       
		if ( !isset( $this->$value ) ) continue;
		
		if(!eval("return \$hditem->$value == \$this->$value;")){
			$old = $new = "";
	  
			$count++;
	  
	  //if ( !isset($this->$value ) ) die('no value' );
			//echo "<br>---".$hditem->$value."---".$value."---<br>";
          //die();
			
			switch($value){
				// Create the comments here
				case 'item_assigned_to':
				
					$sql = "
						SELECT 
							user_id, concat(contact_first_name,' ',contact_last_name) as user_name
						FROM 
							" . $dbPrefix . "users
							LEFT JOIN " . $dbPrefix . "contacts ON user_contact = contact_id
							WHERE 
								user_id in (".
								($hditem->$value?$hditem->$value:"").
								($this->$value&&$hditem->$value?", ":"").
								($this->$value?$this->$value:"").
							")";

					$ids = db_loadList($sql);
					
					foreach ($ids as $row){
						if($row["user_id"]==$this->$value){
							$new = $row["user_name"];
						} else if($row["user_id"]==$hditem->$value){
							$old = $row["user_name"];
						}
					}
					break;
				case 'item_company_id':
					$sql = "
						SELECT 
							company_id, company_name
						FROM 
							" . $dbPrefix . "companies
						WHERE 
							company_id in (".
							($hditem->$value?$hditem->$value:"").
							($this->$value&&$hditem->$value?", ":"").
							($this->$value?$this->$value:"").
						")";
                  
					$ids = db_loadList($sql);

					foreach ($ids as $row){
						if($row["company_id"]==$this->$value){
							$new = $row["company_name"];
						} 
						else if($row["company_id"]==$hditem->$value){
							$old = $row["company_name"];
						}
					}

					break;
				case 'item_project_id':
					$sql = "
						SELECT 
							project_id, project_name
						FROM 
							" . $dbPrefix . "projects
						WHERE 
							project_id in (".
							($hditem->$value?$hditem->$value:"").
							($this->$value&&$hditem->$value?", ":"").
							($this->$value?$this->$value:"").
						")";

					$ids = db_loadList($sql);
					foreach ($ids as $row){
						if($row["project_id"]==$this->$value){
							$new = $row["project_name"];
						} else if($row["project_id"]==$hditem->$value){
							$old = $row["project_name"];
						}
					}
					break;
				case 'item_calltype':
					$old = $AppUI->_($ict[$hditem->$value]);
					$new = $AppUI->_($ict[$this->$value]);
					break;
				case 'item_source':
					$old = $AppUI->_($ics[$hditem->$value]);
					$new = $AppUI->_($ics[$this->$value]);
					break;
				case 'item_status':
					$old = $AppUI->_($ist[$hditem->$value]);
					$new = $AppUI->_($ist[$this->$value]);
					break;
				case 'item_priority':
					$old = $AppUI->_($ipr[$hditem->$value]);
					$new = $AppUI->_($ipr[$this->$value]);
					break;
				case 'item_severity':
					$old = $AppUI->_($isv[$hditem->$value]);
					$new = $AppUI->_($isv[$this->$value]);
					break;
				case 'item_os':
					$old = $AppUI->_($ios[$hditem->$value]);
					$new = $AppUI->_($ios[$this->$value]);
					break;
				case 'item_application':
					$old = $AppUI->_($iap[$hditem->$value]);
					$new = $AppUI->_($iap[$this->$value]);
					break;
				case 'item_notify':
					$old = $hditem->$value ? $AppUI->_('On') : $AppUI->_('Off');
					$new = $this->$value ? $AppUI->_('On') : $AppUI->_('Off');
					break;
				default:
					$old = $hditem->$value;
					$new = $this->$value;
					break;
			}// end of switch

			//	echo "log_status_changes<br>";
			$last_status_log_id = $this->log_status($key, $AppUI->_('changed from')
                                                      . " \""
                                                      . addslashes($old)
                                                      . "\" "
                                                      . $AppUI->_('to')
                                                      . " \""
                                                      . addslashes($new)
                                                      . "\"", 0, $count);
		}//end of if
	  }//end of loop
      return $last_status_log_id;
    }
  }
  
  function log_status ($audit_code, $comment="", $newhdi=0, $notify=1) {
  	global $AppUI;

	$dbPrefix = dPgetConfig( 'dbprefix' );
	
    $sql = "
      INSERT INTO " . $dbPrefix . "helpdesk_item_status
      (status_item_id,status_code,status_date,status_modified_by,status_comment)
      VALUES('{$this->item_id}','{$audit_code}',NOW(),'{$AppUI->user_id}','$comment')
    ";

    db_exec($sql);

    if (db_error()) {
      return false;
    }
    
    $log_id = mysql_insert_id();
    // KZHAO 7-31-2006
    if($this->item_notify && $notify==1){
	    $this->notify(STATUS_LOG, $log_id, $newhdi);
    }
    return $log_id;
  }
}

/**
* Overloaded CTask Class
*/
class CHDTaskLog extends CDpObject {
  var $task_log_id = NULL;
  var $task_log_task = NULL;
  var $task_log_help_desk_id = NULL;
  var $task_log_name = NULL;
  var $task_log_description = NULL;
  var $task_log_creator = NULL;
  var $task_log_hours = NULL;
  var $task_log_date = NULL;
  var $task_log_costcode = NULL;

  function CHDTaskLog() {
//    $this->CDpObject( 'task_log', 'task_log_id' );

		$table	= 'task_log';
		$key 	= 'task_log_id';
		$perm_name = '';
		
		$this->_tbl		= $table;
		$this->_tbl_key = $key;
		$this->_permission_name = (($perm_name) ? $perm_name : $table);
		$this->_query = new DBQuery;
  }

  // overload check method
  function check() {
    $this->task_log_hours = (float) $this->task_log_hours;
    return NULL;
  }
}
?>
