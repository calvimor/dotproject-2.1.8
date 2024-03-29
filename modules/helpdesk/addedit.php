<?php /* HELPDESK $Id: addedit.php 334 2007-02-16 19:55:20Z kang $ */
//KZHAO  9-12-2006
global $HELPDESK_CONFIG, $dbPrefix;
$perms =& $AppUI->acl();
$item_id = dPgetParam($_GET, 'item_id', 0);

$dbPrefix = dPgetConfig( 'dbprefix' );

$allowedCompanies = arrayMerge( array( 0 => '' ), getAllowedCompanies() );

$projects = getAllowedProjectsForJavascript(1);
$dbPrefix = dPgetConfig('dbprefix');

// Lets check cost codes
/*$q = new DBQuery;
$q->addTable('billingcode');
$q->addQuery('billingcode_id, billingcode_name');
$q->addWhere('billingcode_status=0');

$task_log_costcodes[0]=$AppUI->_('None');
$ptrc = $q->exec();
echo db_error();
$nums = 0;
if ($ptrc)
	$nums=db_num_rows($ptrc);
for ($x=0; $x < $nums; $x++) {
        $row = db_fetch_assoc( $ptrc );
        $task_log_costcodes[$row["billingcode_id"]] = $row["billingcode_name"];
}
*/
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
  $canEdit = $perms->checkModule($m, 'add');
}

if(!$canEdit){
  $AppUI->redirect( "m=public&a=access_denied" );
}
// Use new default 'assigned to' ---KZHAO
if(!$hditem["item_assigned_to"]){
  if($HELPDESK_CONFIG['default_assigned_to_current_user']=='-1'){
      $hditem["item_assigned_to"] = 0;
      if(!$hditem["item_status"])
          $hditem["item_status"]=0;
  }
  elseif($HELPDESK_CONFIG['default_assigned_to_current_user']=='0'){
      $hditem["item_assigned_to"] = $AppUI->user_id;
      if(!$hditem["item_status"])
                $hditem["item_status"]=1;
  }
  else{
      $hditem["item_assigned_to"] = $HELPDESK_CONFIG['default_assigned_to_current_user'];
      if(!$hditem["item_status"])
                $hditem["item_status"]=1;
  }
}

if(!$hditem["item_company_id"] && $HELPDESK_CONFIG['default_company_current_company']){
  $hditem["item_company_id"] = $AppUI->user_company;
}

// KZHAO : 8-8-2006
// get current user's company id and use it to filter users
$sql="SELECT DISTINCT contact_company
      FROM " .$dbPrefix . "contacts, dotp_users
      WHERE user_id=".$AppUI->user_id." AND user_contact=contact_id ";
$allowedComp=db_loadHashList( $sql );
if(!count($allowedComp)){
	echo "ERROR: No company found for current user!!<br>";
	$compId=0;
}
elseif(count($allowedComp)==1){
	$tmp=array_keys($allowedComp);
//	print_r($tmp);
	$compId=$tmp[0];
//	echo $compId;
}
else{
	echo "ERROR: Multiple companies found for current user!!!<br>";
	$compId=0;
}

// Determine whether current user is a client
if($compId!=$HELPDESK_CONFIG['the_company'])
  $is_client=1;
else 
  $is_client=0;

//print_r($allowedComp);
$users = getAllowedUsers($compId,1);
//$allowedCompanies = arrayMerge( array( 0 => '' ), getAllowedCompanies($compId) );

$sql = "SELECT company_id, company_name
        FROM " . $dbPrefix . "companies
        WHERE "
     . getCompanyPerms("company_id")
     . " ORDER BY company_name";

$companies = arrayMerge( array( 0 => '' ), db_loadHashList( $sql ) );

//Use new watcher list --KZHAO
if($item_id){ 
  // if editing an existing helpdesk item, get its watchers from database
  $sql = "
	  SELECT 
		  " . $dbPrefix . "helpdesk_item_watchers.user_id, 
  		CONCAT(contact_last_name, ',', contact_first_name) as name,
  		contact_email
  	FROM 
	  	" . $dbPrefix . "helpdesk_item_watchers
	  	LEFT JOIN " . $dbPrefix ."users ON " . $dbPrefix . "helpdesk_item_watchers.user_id = " . $dbPrefix . "users.user_id
	  	LEFT JOIN " . $dbPrefix . "contacts ON user_contact = contact_id
        WHERE 
        	item_id = ".$item_id."
        ORDER BY contact_last_name, contact_first_name";

  $watchers = db_loadHashList( $sql );
}
else{ // for a new item, check default
  if($HELPDESK_CONFIG['default_watcher'] && $HELPDESK_CONFIG['default_watcher_list']){
      $watchers = explode(',',$HELPDESK_CONFIG['default_watcher_list']);
      /*echo "<pre>";
      print_r($watchers);
      echo "<pre>";*/
  }

}

// Setup the title block
$ttl = $item_id ? 'Editing Help Desk Item' : 'Adding Help Desk Item';

$titleBlock = new CTitleBlock( $ttl, 'helpdesk.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=helpdesk", 'home' );
$titleBlock->addCrumb( "?m=helpdesk&a=list", 'list');

if ($item_id) {
  $titleBlock->addCrumb( "?m=helpdesk&a=view&item_id=$item_id", 'view this item' );
}

$titleBlock->show();

if ($item_id) { 
  $df = $AppUI->getPref('SHDATEFORMAT');
  $tf = $AppUI->getPref('TIMEFORMAT');
  $item_date = new CDate( $hditem["item_created"] );
  !strcmp($hditem["item_deadline"], "N/A") ? 
      $deadline_date = new CDate( $hditem["item_deadline"] ) :
      $deadline_date = new CDate();
  $tc = $item_date->format( "$df $tf" );
} else {
//  $hditem["item_created"] = db_unix2dateTime(time());
//  $hditem["item_created"] = date('Y-m-d H:i:s'); 
  $item_date = new CDate();
  $deadline_date = new CDate();
  $item_date = $item_date->format( FMT_DATETIME_MYSQL );
  $hditem["item_created"] = $item_date;
}

?>
<html>

<script language="javascript" type="text/javascript">
function submitIt() {
  var f   = document.frmHelpDeskItem;
  var msg = '';

  if ( f.item_title.value.length < 1 ) {
    msg += "\n<?php echo $AppUI->_('Title'); ?>";
    f.item_title.focus();
  }

  if( f.item_requestor.value.length < 1 ) {
    msg += "\n<?php echo $AppUI->_('Requestor'); ?>";
    f.item_requestor.focus();
  }

  if( f.item_summary.value.length < 1 ) {
    msg += "\n<?php echo $AppUI->_('Summary'); ?>";
    f.item_summary.focus();
  }

  //concat all the multiselect values together for easier retrieval on the back end.
  var watchers = "";
  var list = f.watchers_select;
  for (var i=0, n = list.options.length; i < n; i++) {
    var user = list.options[i];
    if(user.selected)
    	watchers += user.value + ",";
  }
  if(watchers.length>0){
  	f.watchers.value = watchers.substring(0,watchers.length-1);
  }
  
  if( msg.length > 0) {
    alert('<?php echo $AppUI->_('helpdeskSubmitError', UI_OUTPUT_JS); ?>:' + msg);
  } else {
    f.submit();
  }
} 
// KZHAO  8-10-2006
function popUserDialog() {
  var target='./index.php?m=helpdesk&a=selector&callback=setUserRequestor&table=users&dialog=1&comp=<?php echo $compId; ?>';
  window.open(target, 'selector', 'left=50,top=50,height=250,width=400,resizable,scrollbars=yes');
}

function popContactDialog() {
 window.open('./index.php?m=helpdesk&a=selector&callback=setContactRequestor&table=contacts&dialog=1', 'selector', 'left=50,top=50,height=250,width=400,resizable,scrollbars=yes');
 
//  window.open('./index.php?m=public&a=selector&callback=setContactRequestor&table=contacts&dialog=1', 'selector', 'left=50,top=50,height=250,width=400,resizable')
}

var oldRequestor = '';

// Callback function for the generic selector
function setRequestor( key, val ) {
  var f = document.frmHelpDeskItem;

  if (val != '') {
    f.item_requestor_id.value = key;
    f.item_requestor.value = val;
    oldRequestor = val;

    // Since we probably chose someone else, wipe the e-mail and phone fields
    f.item_requestor_email.value = '';
    f.item_requestor_phone.value = '';
   
  }
}

function setUserRequestor( key, val ) {
  var f = document.frmHelpDeskItem;

  if (val != '') {
    setRequestor( key, val );
    f.item_requestor_type.value = 1
  }
 // alert("---".key.":".val."--");
}

function setContactRequestor( key, val ) {
  var f = document.frmHelpDeskItem;

  if (val != '') {
    setRequestor( key, val );
    f.item_requestor_type.value = 2
  }
}

function updateStatus(obj){
  var f = document.frmHelpDeskItem;

  if(obj.options[obj.selectedIndex].value>0){
    if(f.item_status.selectedIndex==0){
    	f.item_status.selectedIndex=1;
    }
  }
}

<?php 
	$ua = $_SERVER['HTTP_USER_AGENT'];
	$isMoz = strpos( $ua, 'Gecko' ) !== false;

	print "\nvar projects = new Array(";
	if(isset($projects) and is_array( $projects ) and count($projects)>0){
		print implode(",",$projects );			
	}
	else{
	}

	//print count($projects)>0 ? implode(",\n", $projects ) : "";
	print ");"; 
	
?>

// Dynamic project list handling functions
function emptyList( list ) {
<?php 
	if ($isMoz) { 
?>
	 list.options.length = 0;
<?php 
 	} else {
?>
	 while( list.options.length > 0 )
		list.options.remove(0);
<?php } ?>

}

function addToList( list, text, value ) {
<?php if ($isMoz) { ?>
  list.options[list.options.length] = new Option(text, value);
<?php } else { ?>
  var newOption = document.createElement("OPTION");
  newOption.text = text;
  newOption.value = value;
  list.add( newOption, 0 );
<?php } ?>

}

function changeList( listName, source, target ) {
  var f = document.frmHelpDeskItem;
  var list = eval( "f."+listName );
  // Clear the options
  emptyList( list );
  // Refill the list based on the target
  // Add a blank first to force a change
   addToList( list, '', '0' );

   for (var i=0, n = source.length; i < n; i++) {
    if( source[i][0] == target ) {
      addToList( list, source[i][2], source[i][1] );
    }
  }
}

// Select an item in the list by target value
function selectList( listName, target ) {
  var f = document.frmHelpDeskItem;
  var list = eval( 'f.'+listName );

  for (var i=0, n = list.options.length; i < n; i++) {
    if( list.options[i].value == target ) {
      list.options.selectedIndex = i;
      return;
    }
  }
 
}
				  
</script>

<!-- TIMER RELATED SCRIPTS -->
<script language="JavaScript">
	// please keep these lines on when you copy the source
	// made by: Nicolas - http://www.javascript-page.com
	// adapted by: Juan Carlos Gonzalez jcgonz@users.sourceforge.net
	
	var timerID       = 0;
	var tStart        = null;
	var total_minutes = -1;
	
	function UpdateTimer() {
	   if(timerID) {
	      clearTimeout(timerID);
	      clockID  = 0;
	   }
	
     // One minute has passed
     total_minutes = total_minutes+1;
	   
	   document.getElementById("timerStatus").innerHTML = "( "+total_minutes+" <?php echo $AppUI->_('minutes elapsed'); ?> )";

	   // Lets round hours to two decimals
	   var total_hours   = Math.round( (total_minutes / 60) * 100) / 100;
	   document.frmHelpDeskItem.task_log_hours.value = total_hours;
	   
	   timerID = setTimeout("UpdateTimer()", 60000);
	}
	
	function timerStart() {
		if(!timerID){ // this means that it needs to be started
			document.frmHelpDeskItem.timerStartStopButton.value = "<?php echo $AppUI->_('Stop');?>";
      total_minutes = Math.round(document.frmHelpDeskItem.task_log_hours.value*60) - 1;
      UpdateTimer();
		} else { // timer must be stoped
			document.frmHelpDeskItem.timerStartStopButton.value = "<?php echo $AppUI->_('Start');?>";
			document.getElementById("timerStatus").innerHTML = "";
			timerStop();
		}
	}
	
	function timerStop() {
	   if(timerID) {
	      clearTimeout(timerID);
	      timerID  = 0;
	      total_minutes = total_minutes-1;
	   }
	}
	
	function timerReset() {
		document.frmHelpDeskItem.task_log_hours.value = "0.00";
		total_minutes = -1;
	}
	
function popCalendar(now, field, cdate ){
    calendarField = field;
    if(now==0)// if the deadline is already specified
    	idate = eval( 'document.frmHelpDeskItem.item_' + field + '.value' );
    else  // if there is no deadline
        idate=cdate;
    window.open( 'index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false' );
}
     
function setCalendar( idate, fdate ) {
    fld_date = eval( 'document.frmHelpDeskItem.item_' + calendarField );
    fld_fdate = eval( 'document.frmHelpDeskItem.' + calendarField );
    fld_date.value = idate; 
    fld_fdate.value = fdate;
  }
 
</script>
<!-- END OF TIMER RELATED SCRIPTS -->

<table cellspacing="1" cellpadding="1" border="0" width="100%" class="std">
  <form name="frmHelpDeskItem" action="?m=helpdesk" method="post">
  <input type="hidden" name="dosql" value="do_item_aed" />
  <input name="del" type="hidden" value="0" />
  <input type="hidden" name="item_id" value="<?php echo $item_id; ?>" />
  <input type="hidden" name="item_requestor_type" value="<?php echo $hditem["item_requestor_type"]; ?>" />
  <input type="hidden" name="item_requestor_id" value="<?php echo $hditem["item_requestor_id"]; ?>" />
  <input type="hidden" name="item_created" value="<?php echo $hditem["item_created"]; ?>" />
  <?php if (!$item_id): ?>
  <input type="hidden" name="item_created_by" value="<?php echo $AppUI->user_id; ?>" />
  <?php endif; ?>

  <tr>
  <td valign="top" width="50%">
    <table cellspacing="0" cellpadding="2" border="0">
    <?php if ($item_id): ?>
    <tr>
      <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Date Created'); ?>:</td>
      <td width="100%"><strong><?php echo $tc; ?></strong></td>
    </tr>
    <?php endif; ?>
    <tr>
      <td align="right"><font color="red"><label for="it">* <?php echo $AppUI->_('Title'); ?>:</label></font></td>
      <td valign="top"><input type="text" class="text" id="it" name="item_title"
                              value="<?php echo $hditem["item_title"]; ?>" maxlength="64" /></td>
    </tr>

    <tr>
      <td align="right" nowrap="nowrap"><font color="red"><label for="ir">* <?php echo $AppUI->_('Requestor'); ?>:</label></font></td>
      <td valign="top" nowrap="nowrap">
        <input type="text" class="text" id="ir" name="item_requestor"
        value="<?php echo $hditem["item_requestor"]; ?>" maxlength="64"
        onChange="if (this.value!=oldRequestor) {
                    document.frmHelpDeskItem.item_requestor_id.value = 0;
                    oldRequestor = this.value;
                  }" />
      <input type="button" class="button" 
      		value="<?php echo $AppUI->_('Users'); ?>" onclick="popUserDialog();" />

<!--  <input type="button" class="button" 
		value="<?php echo $AppUI->_('Contacts'); ?>" onclick="popContactDialog();" />
-->
     
      </td>
    </tr>

    <tr>
      <td align="right" nowrap="nowrap"><label for="ire">&dagger; <?php echo $AppUI->_('Requestor E-mail'); ?>:</label></td>
      <td valign="top"><input type="text" class="text" id="ire"
                              name="item_requestor_email"
                              value="<?php echo $hditem["item_requestor_email"]; ?>"
                              maxlength="64" /></td>
    </tr>

    <tr>
      <td align="right" nowrap="nowrap"><label for="irp">&dagger; <?php echo $AppUI->_('Requestor Phone'); ?>:</label></td>
      <td valign="top"><input type="text" class="text" id="irp"
                              name="item_requestor_phone"
                              value="<?php echo $hditem["item_requestor_phone"]; ?>"
                              maxlength="30" /></td>
    </tr>

    <tr>
      <td align="right"><label for="c"><?php echo $AppUI->_('Company'); ?>:</label></td>
      <td><?php echo arraySelect( $allowedCompanies, 'item_company_id', 'size="1" class="text" id="c" onchange="changeList(\'item_project_id\',projects, this.options[this.selectedIndex].value)"',
                          $hditem["item_company_id"] ); ?></td>
    </tr>

    <tr>
      <td align="right"><label for="p"><?php echo $AppUI->_('Project'); ?>:</label></td>
      <td><select name="item_project_id" size="1" class="text" id="p"></select></td>
    </tr>

    <tr>
      <td align="right" valign="top"><label for="iat"><?php echo $AppUI->_('Assigned To'); ?>:</label></td>
      <td><?php 
          if($is_client)
            echo arraySelect( arrayMerge( array( 0 => '' ), $users), 'item_assigned_to', 'size="1" class="text" id="iat" disabled onchange="updateStatus(this)"', $hditem["item_assigned_to"] ); 
          else
            echo arraySelect( arrayMerge( array( 0 => '' ), $users), 'item_assigned_to', 'size="1" class="text" id="iat" onchange="updateStatus(this)"', $hditem["item_assigned_to"] );
          ?>
        <!--
	<br />Send email notification to:<br />  
	<input type="checkbox" name="item_notify_requestor" value="1" id="inr" checked />
	<label for="inw"><?php /*echo $AppUI->_( 'Requestor' );*/ ?></label>
	<input type="checkbox" name="item_notify" value="1" id="in"
        <?php 
          /*if (!$item_id) {
            print $HELPDESK_CONFIG['default_notify_by_email'] ? "checked" : "";
          } else {
            print $hditem["item_notify"] ? "checked" : "";
          }*/
        ?>
        />
        <label for="in"><?php /*echo $AppUI->_( 'Assignee' );*/ ?></label>
	<input type="checkbox" name="item_notify_watcher" value="1" id="inw" checked />
	<label for="inw"><?php /*echo $AppUI->_( 'Watchers' ); */?></label>
	-->
	</td>

    </tr>

    <?php   if($item_id){
    		//existing item
		if($hditem['item_notify']) $emailNotify=1;
		else $emailNotify=0;
	    }
	    else{
		$emailNotify=$HELPDESK_CONFIG['default_notify_by_email'];
	    }
	
    ?>
    <tr>
       <td align="right" valign="top"><label for="iat"><?php echo $AppUI->_('Email Notification'); ?>:</label>
       </td>  
       <td>
       	    <input type="radio" name="item_notify" value="1" id="ina" 
	    		<?php if($emailNotify) echo "checked";
             if($is_client) echo " disabled "; 
           ?> />
		<label for="ina"><?php echo $AppUI->_( 'Yes' ); ?></label>
	    <input type="radio" name="item_notify" value="0" id="inn" 
	    		<?php if(!$emailNotify) echo "checked";
             if($is_client) echo " disabled ";
          ?> />
	       <label for="inn"><?php echo $AppUI->_( 'No' ); ?></label>
       </td>
    </tr>
  </table>
  </td>
  <td valign="top" width="50%">
    <table cellspacing="0" cellpadding="2" border="0">
    <tr>
      <td align="right" nowrap="nowrap"><label for="ict"><?php echo $AppUI->_('Call Type'); ?>:</label></td>
      <td><?php echo arraySelect( $ict, 'item_calltype', 'size="1" class="text" id="ict"',
                          $hditem["item_calltype"], true ); ?></td>
    </tr>

    <tr>
      <td align="right" nowrap="nowrap"><label for="ics"><?php echo $AppUI->_('Call Source'); ?>:</label></td>
      <td><?php echo arraySelect( $ics, 'item_source', 'size="1" class="text" id="ics"',
                          $hditem["item_source"], true); ?></td>
    </tr>

    <tr>
      <td align="right"><label for="ist"><?php echo $AppUI->_('Status'); ?>:</label></td>
      <td><?php echo arraySelect( $ist, 'item_status', 'size="1" class="text" id="ist"',
                          $hditem["item_status"], true ); ?></td>
    </tr>

    <tr>
      <td align="right"><label for="ipr"><?php echo $AppUI->_('Priority'); ?>:</label></td>
      <td><?php echo arraySelect( $ipr, 'item_priority', 'size="1" class="text" id="ipr"',
                          $hditem["item_priority"], true ); ?></td>
    </tr>

    <tr>
      <td align="right"><label for="isv"><?php echo $AppUI->_('Severity'); ?>:</label></td>
      <td><?php echo arraySelect( $isv, 'item_severity', 'size="1" class="text" id="isv"',
                          $hditem["item_severity"], true ); ?></td>
    </tr>

    <tr>
      <td align="right" nowrap="nowrap"><label for="ios"><?php echo $AppUI->_('Operating System'); ?>:</label></td>
      <td><?php echo arraySelect( $ios, 'item_os', 'size="1" class="text" id="ios"',
                          $hditem["item_os"], true); ?></td>
    </tr>

    <tr>
      <td align="right"><label for="iap"><?php echo $AppUI->_('Application'); ?>:</label></td>
      <td><?php echo arraySelect( $iap, 'item_application', 'size="1" class="text" id="iap"',
                          $hditem["item_application"], true); ?></td>
    </tr>
    <tr>
      <!--
      <td align="right" nowrap="nowrap"><label for="idl"><?php echo $AppUI->_('Deadline'); ?>:</label></td>
      <td valign="top"><input type="text" class="text" id="idl"
                              name="item_deadline"
                              value="<?php echo "NA"/*@$hditem["item_deadline"]*/; ?>"
			      size="4"
                              maxlength="4" /><?php echo $AppUI->_('day(s) from today'); ?> 
      </td>
      -->
      <td align="right" nowrap="nowrap"><label for="idl"><?php echo $AppUI->_('Deadline'); ?>:</label>
      </td>
      <td>
      	<input type="hidden" name="item_deadline" value="
		<?php 
			if($item_id && $hditem["item_deadline"]!=NULL) 
				echo $deadline_date->format( FMT_DATETIME_MYSQL ); 
			else echo "N/A";
		?>">	
	<input type="text" name="deadline" value="
		<?php 
			if($item_id && $hditem['item_deadline']!=NULL) echo $deadline_date->format( $df ); 
			else echo "Not Specified";	
		?>" class="text" disabled="disabled">
     	    <a href="#" 
	    	<?php
		 if($item_id && $hditem['item_deadline']!=NULL) echo "onClick=\"popCalendar(0,'deadline','')\"";
		 else echo "onClick=\"popCalendar(1,'deadline','".$deadline_date->format( FMT_DATETIME_MYSQL)."')\"";
		?>
	    >
	    <img src="./images/calendar.gif" width="24" height="12" alt="<?php echo $AppUI->_('Calendar'); ?>" border="0" />
	    </a>
      </td>
																      
    </tr>
    </table>
  </td>
</tr>

<tr><td colspan="2">
<table cellspacing="0" cellpadding="0" border="0">
<tr>
  <td align="left"><font color="red"><label for="summary">* <?php echo $AppUI->_('Summary'); ?>:</label></font>
  </td>
  <td>&nbsp;&nbsp;</td>
  <td><label for="watchers"><?php echo $AppUI->_('Watchers'); ?>:</label></td>
</tr>

<tr>
  <td valign="top">
    <textarea id="summary" cols="75" rows="12" class="textarea"
              name="item_summary"><?php echo $hditem["item_summary"]; ?></textarea>
  </td>
  <td>&nbsp;&nbsp;</td>
      <td>
      <select name="watchers_select" size="14" id="watchers_select" multiple="multiple" 
      <?php if($is_client) echo "disabled class=disabledText";
            else echo "class=text";
      ?>
      >
      <?php
	      foreach($users as $id => $name){
		echo "<option value=\"{$id}\"";
    // Two situations -- KZHAO
		if($item_id && array_key_exists($id,$watchers))
			echo " selected";
    elseif(!$item_id && $watchers && in_array($id, $watchers))
      echo " selected";
		echo ">{$name}</option>";
	      }
      ?></select>
      <input type="hidden" name="watchers" value="" /></td>
</tr></table>
</td></tr>

<!--commented by KZHAO 7-20-2006
    code dealing with hours worked and cost code
-->

<tr>
  <td colspan="2">
  <br />
  <small>
    <font color="red">* <?php echo $AppUI->_('Required field'); ?></font><br />
    &dagger; <?php echo $AppUI->_('helpdeskFieldMessage'); ?>
  </small>
  <br /><br />
  </td>
</tr>

<tr>
  <td><input type="button" value="<?php echo $AppUI->_('back'); ?>" class="button" onClick="javascript:history.back(-1);" />
  </td>
  <td align="right"><input type="button" value="<?php echo $AppUI->_('submit'); ?>" class="button" onClick='submitIt()' >
  </td>
</tr>

</form>
</table>

<p>&nbsp;</p>
<?php 
  /* If we have a company stored, pre-select it.
     If we have a project but not a company (version <0.2) do a reverse
     lookup.
     Else, select nothing */
  if ($hditem['item_company_id']) {
    $target = $hditem['item_company_id'];
  } else if ($hditem['item_project_id']) {
    $target = $reverse[$hditem['item_project_id']];
  } else {
    $target = 0;
  }

  /* Select the project from the list */
  $select = $hditem['item_project_id'] ? $hditem['item_project_id'] : 0;
?>

<script language="javascript">
selectList('item_company_id',<?php echo $target?>);
changeList('item_project_id', projects, <?php echo $target?>);
selectList('item_project_id',<?php echo $select?>);
</script>

</html>
