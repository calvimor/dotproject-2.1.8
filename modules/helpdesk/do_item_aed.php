<?php /* HELPDESK $Id: do_item_aed.php 265 2006-12-14 18:06:35Z kang $ */
$del = dPgetParam( $_POST, 'del', 0 );// the last parameter is the default value
$item_id = dPgetParam( $_POST, 'item_id', 0 );
$do_task_log = dPgetParam( $_POST, 'task_log', 0 );
$deadline=dPgetParam( $_POST, 'item_deadline', 0 );

$new_item = !($item_id>0);
$updated_date = new CDate();
$udate = $updated_date->format( FMT_DATETIME_MYSQL );
//$notify_watcher=dPgetParam($_POST,'item_notify_watcher',0);
//$notify_requestor=dPgetParam($_POST,'item_notify_requestor',0);
$notify_all=dPgetParam($_POST,'item_notify',0);

if($do_task_log){ // When is this brach called???
	//first update the status on to current helpdesk item.
	$hditem = new CHelpDeskItem();
	$hditem->load( $item_id );
	$hditem->item_updated = $udate;

	$new_status = dPgetParam( $_POST, 'item_status', 0 );
	$new_assignee = dPgetParam( $_POST, 'item_assigned_to', 0 );
	$users = getAllowedUsers();
	
	if($new_status!=$hditem->item_status){
		$status_log_id = $hditem->log_status(11, $AppUI->_('changed from')
                                           . " \"".$AppUI->_($ist[$hditem->item_status])."\" "
                                           . $AppUI->_('to')
                                           . " \"".$AppUI->_($ist[$new_status])."\"");
		$hditem->item_status = $new_status;
		
		if (($msg = $hditem->store())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		} 
		else {
      			$hditem->notify(STATUS_LOG, $status_log_id);
    		}
	} 
	else {
	//Store the item_update no matter if the status was changed or not
		if (($msg = $hditem->store())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		}
	}

	if($new_assignee!=$hditem->item_assigned_to){
		$status_log_id = $hditem->log_status(5, $AppUI->_('changed from')
                                           . " \"".$AppUI->_($users[$hditem->item_assigned_to])."\" "
                                           . $AppUI->_('to')
                                           . " \"".$AppUI->_($users[$new_assignee])."\"");
		$hditem->item_assigned_to = $new_assignee;
		
		if (($msg = $hditem->store())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		}
		else {
      		$hditem->notify(STATUS_LOG, $status_log_id);
    		}
	}
	
	//then create/update the task log
	$obj = new CHDTaskLog();

	if (!$obj->bind( $_POST )) {
		$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
		$AppUI->redirect();
	}

	if ($obj->task_log_date) {
		$date = new CDate( $obj->task_log_date );
		$obj->task_log_date = $date->format( FMT_DATETIME_MYSQL );
	}

	$AppUI->setMsg( 'Task Log' );

  $obj->task_log_costcode = $obj->task_log_costcode;
  if (($msg = $obj->store())) {
    $AppUI->setMsg( $msg, UI_MSG_ERROR );
    $AppUI->redirect();
  } else {
    $hditem->notify(TASK_LOG, $obj->task_log_id);
    if($AppUI->msgNo != UI_MSG_ERROR) {
        $AppUI->setMsg( @$_POST['task_log_id'] ? 'updated' : 'added', UI_MSG_OK, true );
    }
  }

	$AppUI->redirect("m=helpdesk&a=view&item_id=$item_id&tab=0");

} 

else {  // by KZHAO: for creating or editting Helpdesk items
	$hditem = new CHelpDeskItem();
	if ( !$hditem->bind( $_POST )) {
		$AppUI->setMsg( $hditem->error, UI_MSG_ERROR );
		$AppUI->redirect();
	}

	$AppUI->setMsg( 'Help Desk Item', UI_MSG_OK );
	
	if ($del) {// to delete an item
		$hditem->item_updated = $udate;
		if (($msg = $hditem->store())){ 
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
			$AppUI->redirect();
		}
		if (($msg = $hditem->delete())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
		} else {
			$AppUI->setMsg( 'deleted', UI_MSG_OK, true );
			$hditem->log_status(18);
			$AppUI->redirect('m=helpdesk&a=list');
		}
	}
	else {	
      		$status_log_id = $hditem->log_status_changes();
		if ($new_item) {
			$item_date = new CDate();
  			$idate = $item_date->format( FMT_DATETIME_MYSQL );
			$hditem->item_created = $idate;
			$hditem->item_updated = $udate;
		} 
		else { 
			$hditem->item_updated = $udate;
		}
		
		//KZHAO  8-10-2006
		// get the deadline for the HD item
		if(!strcmp($deadline, 'N/A')){
			$dl=new CDate($deadline);
			$dl->setTime(23,59,59);
			//$dl->addDays($deadlineIn);
			$hditem->item_deadline=$dl->format( FMT_DATETIME_MYSQL );
    } else {
        $hditem->item_deadline=NULL;
    }

		if (($msg = $hditem->store())) {
			$AppUI->setMsg( $msg, UI_MSG_ERROR );
		} 
		else {
		    if($new_item){// new item creation
				$status_log_id = $hditem->log_status(0,$AppUI->_('Created'),$new_item);
				//Lets create a log for the item creation:
				$obj = new CHDTaskLog();
				$new_item_log = array('task_log_id' => 0,'task_log_help_desk_id' => $hditem->item_id, 'task_log_creator' => $AppUI->user_id, 'task_log_name' => 'Item Created: '.$_POST['item_title'], 'task_log_date' => $hditem->item_created, 'task_log_description' => $_POST['item_title'], 'task_log_hours' => $_POST['task_log_hours'], 'task_log_costcode' => $_POST['task_log_costcode']);
				if (!$obj->bind( $new_item_log )) {
					$AppUI->setMsg( $obj->getError(), UI_MSG_ERROR );
					$AppUI->redirect();
				}
  				if (($msg = $obj->store())) {
    				$AppUI->setMsg( $msg, UI_MSG_ERROR );
    				$AppUI->redirect();
  				}	
		    }
	      	// KZHAO  8-7-2006
		    doWatchers(dPgetParam( $_POST, 'watchers', 0 ), $hditem, $notify_all);
        // KZHAO  8-7-2006
        if($AppUI->msgNo != UI_MSG_ERROR) {
            $AppUI->setMsg( $new_item ? ($AppUI->_('Help Desk Item') .' '. $AppUI->_('added')) : ($AppUI->_('Help Desk Item') . ' ' . $AppUI->_('updated')) , UI_MSG_OK, false );
        }
        $AppUI->redirect('m=helpdesk&a=view&item_id='.$hditem->item_id);
		}
	}
}
// dealing with the helpdesk_item_watchers table in DB and send emails
// send emails to acknowledge that they are added to the watcher list
function doWatchers($list, $hditem, $notify_all){//KZHAO 8-7-2006
	global $AppUI;
	
	$dbPrefix = dPgetConfig( 'dbprefix' );

	# Create the watcher list
	$watcherlist = split(',', $list);
	
	$sql = "SELECT user_id FROM " . $dbPrefix . "helpdesk_item_watchers WHERE item_id=" . $hditem->item_id;
	$current_users = db_loadHashList($sql);
	$current_users = array_keys($current_users);

	# Delete the existing watchers as the list might have changed
	$sql = "DELETE FROM " . $dbPrefix . "helpdesk_item_watchers WHERE item_id=" . $hditem->item_id;
	db_exec($sql);
	
	//print_r($current_users);
	//echo "!!!<br>";
	if (!$del){
		if($list){
			foreach($watcherlist as $watcher){
				$sql = "SELECT user_id, contact_email FROM " . $dbPrefix . "users LEFT JOIN " . $dbPrefix . "contacts ON user_contact = contact_id WHERE user_id=" . $watcher;
				//echo "..".$notify_all."..<br>";
				if($notify_all){
					$rows = db_loadlist($sql);
					foreach($rows as $row){
					# Send the notification that they've been added to a watch list.
						//KZHAO 8-3-2006: only when users choose to send emails
						//echo $row['user_id']."--".$row['contact_email']."<br>";
						if(!in_array($row['user_id'],$current_users)){
							//echo "go!<br>";
							notifyWatchers($row['contact_email'], $hditem);
						}
					}
				}

				$sql = "INSERT INTO " . $dbPrefix . "helpdesk_item_watchers VALUES(". $hditem->item_id . "," . $watcher . ",'Y')";
				db_exec($sql);
			}
		}
	}
	
}
// send notification email to one watcher
function notifyWatchers($address, $hditem){
	global $AppUI, $HELPDESK_CONFIG, $dPconfig;

	$mail = new Mail;
	if($mail->ValidEmail($address)){
		if ($mail->ValidEmail($AppUI->user_email)) {
			$email = $AppUI->user_email;
		} else {
			$email = "admin@".$AppUI->cfg['site_domain'];
		}

		$mail->From("\"{$AppUI->user_first_name} {$AppUI->user_last_name}\" <{$email}>");
		$mail->To($address);
		$mail->Subject(
			$AppUI->_('Help Desk Item')." #".
			$hditem->item_id." ".
			$AppUI->_('Watchers Notification')
			);
		$mail->Body(
			"You have been added to the watchers list for the following Help Desk ticket:\n\n".
			$HELPDESK_CONFIG['email_header'].
			"\nTicket #    : ".$hditem->item_id.
			"\nTicket title: ".$hditem->item_title.
			"\nTicket link : ".$dPconfig['base_url']."/index.php?m=helpdesk&a=view&item_id={$hditem->item_id}"
			//$AppUI->_('IsNowWatched')
			);
		$mail->Send();
	}
}

?>
