<?php /* HELPDESK $Id: export.php v 0.1*/
//KZHAO  10-24-2006
global $HELPDESK_CONFIG, $dPconfig;
require_once('./modules/tasks/tasks.class.php');
$item_id = dPgetParam($_GET, 'item_id', 0);

//$allowedCompanies = arrayMerge( array( 0 => '' ), getAllowedCompanies() );
//$projects = getAllowedProjectsForJavascript();

// Pull data
$sql = "SELECT *
        FROM " . $dbPrefix . "helpdesk_items
        WHERE item_id = '$item_id'";

db_loadHash( $sql, $hditem );
// Check permissions for this record
if ($item_id) {
  // Already existing item
  $canEdit = $perms->checkModuleItem($m, 'edit', $item_id);
} else {
  echo "Cannot find the item id!";
  return;
}

if(!$canEdit){
  $AppUI->redirect( "m=public&a=access_denied" );
}

//KZHAO 10-24-2006
//Load helpdesk item
$org_hditem = new CHelpDeskItem();
$org_hditem->load( $item_id );

//Check required information before export
if(!@$hditem["item_project_id"]){
	 $AppUI->setMsg( "Project must be specified for this item before exporting to task!" , UI_MSG_ERROR );
         $AppUI->redirect("m=helpdesk&a=view&item_id=$item_id");		 
}
//KZHAO 10-24-2006
// Check status
if($ist[@$hditem["item_status"]]=="Closed"){
         $AppUI->setMsg( "Closed helpdesk items cannot be exported to tasks!" , UI_MSG_ERROR );
         $AppUI->redirect("m=helpdesk&a=view&item_id=$item_id");
}
		  
if(!@$hditem["item_assigned_to"] && $HELPDESK_CONFIG['default_assigned_to_current_user']){
  @$hditem["item_assigned_to"] = $AppUI->user_id;
  @$hditem["item_status"] = 1;
}

if(!@$hditem["item_company_id"] && $HELPDESK_CONFIG['default_company_current_company']){
  @$hditem["item_company_id"] = $AppUI->user_company;
}
// Setup the title block

  $df = $AppUI->getPref('SHDATEFORMAT');
  $tf = $AppUI->getPref('TIMEFORMAT');
  $item_date = new CDate( $hditem["item_created"] );
  $deadline_date = new CDate( $hditem["item_deadline"] );
  $tc = $item_date->format( "$df $tf" );

  $dateNow = new CDate();
  $dateNowSQL = $dateNow->format( FMT_DATETIME_MYSQL );
  
  $newTask = new CTask();
  $ref_task ="This task is exported from Helpdesk item #".$item_id.".\n";
  $ref_task.="Link:".$dPconfig['base_url']."/index.php?m=helpdesk&a=view&item_id=".$item_id."\n";
  $ref_task.= "-----------------------\n";

  if(@$hditem["item_priority"]==0 || @$hditem["item_priority"]==2)
  	$taskPrio=0;
  elseif(@$hditem["item_priority"]==1)
        $taskPrio=-1;
  else
        $taskPrio=1;
			       
  $taskInfo= array( "task_id"=>0,
  		    "task_name"=> @$hditem["item_title"],
  		    "task_project"=> @$hditem["item_project_id"],
		    "task_start_date"=> $dateNowSQL,
		    "task_end_date"=>@$hditem["item_deadline"],
		    "task_priority"=>$taskPrio,// @$hditem["item_priority"],
		    "task_owner"=> $AppUI->user_id,//@$hditem["item_requestor_id"],
		    "task_creator"=>$AppUI->user_id,
		    "task_description"=>$ref_task.@$hditem["item_summary"]
		    );

  //print_r($taskInfo);
  echo "<br><br>";
  $result= $newTask->bind( $taskInfo);
  if (!$result) {
	$AppUI->setMsg( $newTask->getError(), UI_MSG_ERROR );
	$AppUI->redirect();
  }
 // echo "Task object created!!<br>";

  if (($msg = $newTask->store())) {
	  $AppUI->setMsg( $msg, UI_MSG_ERROR );
	  $AppUI->redirect(); // Store failed don't continue?
  }
  else {  $ref_hd ="This helpdesk item has been exported to task #".$newTask->task_id.".\n";
  	  $ref_hd.="Link: ".$dPconfig['base_url']."/index.php?m=tasks&a=view&task_id=".$newTask->task_id."\n";
	  $ref_hd.="---------------------------\n";
  	  $org_hditem->item_status=2;
	  $org_hditem->item_updated=$dateNowSQL;
	  $org_hditem->item_summary=$ref_hd.$org_hditem->item_summary;
	  if (($msg = $org_hditem->store())) {
	  	$AppUI->setMsg( $msg, UI_MSG_ERROR );
		  $AppUI->redirect();
	  }
    //Kang--4/18/2007  deal with the assignee
    if($AppUI->user_id!=@$hditem["item_assigned_to"]){
        $clear_assignee_sql="DELETE FROM " . $dbPrefix . "user_tasks WHERE user_id=".$AppUI->user_id." and task_id=".$newTask->task_id;
        db_exec($clear_assignee_sql);
        $assignee_sql="INSERT INTO " . $dbPrefix . "user_tasks VALUES (".@$hditem["item_assigned_to"].",0,".$newTask->task_id.",100,0)";
        db_exec($assignee_sql);
    }
	  //$org_hditem->store();
    $AppUI->setMsg( 'Task '.$newTask->task_id.' added!', UI_MSG_OK);
	  $AppUI->redirect("m=helpdesk&a=view&item_id=$item_id&tab=0");
	  //echo "<p><a href=".$dPconfig['base_url']."/index.php?m=tasks&a=view&task_id=".$newTask->task_id.">Click here to view the task</a>";
         // echo "<p><a href=".$dPconfig['base_url']."/index.php?m=helpdesk&a=view&item_id=".$item_id.">Click here to view the helpdesk ticket</a>";
	  
  }
				  
?>
