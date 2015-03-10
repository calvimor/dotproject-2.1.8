<?php /* HELPDESK $Id: view.php 265 2006-12-14 18:06:35Z kang $ */
$df = $AppUI->getPref( 'SHDATEFORMAT' );
$tf = $AppUI->getPref( 'TIMEFORMAT' );
$format = $df." ".$tf;

$item_id = dPgetParam( $_GET, 'item_id', 0 );

$dbPrefix = dPgetConfig( 'dbprefix' );

// Get pagination page
if (isset($_GET['page'])) {
  $AppUI->setState('HelpDeskLogPage', $_GET['page']);
} else {
  $AppUI->setState('HelpDeskLogPage', 0);
}

$page = $AppUI->getState('HelpDeskLogPage') ? $AppUI->getState('HelpDeskLogPage') : 0;

// Get tab state
if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'HelpLogVwTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'HelpLogVwTab' ) !== NULL ? $AppUI->getState( 'HelpLogVwTab' ) : 0;

// Pull data
$sql = "SELECT hi.*,
        CONCAT(co.contact_first_name,' ',co.contact_last_name) assigned_to_fullname,
        co.contact_email as assigned_email,
        p.project_id,
        p.project_name,
        p.project_color_identifier,
        c.company_name
        FROM " . $dbPrefix . "helpdesk_items hi
        LEFT JOIN " . $dbPrefix . "users u ON u.user_id = hi.item_assigned_to
        LEFT JOIN " . $dbPrefix . "contacts co ON co.contact_id = u.user_contact
        LEFT JOIN " . $dbPrefix . "projects p ON p.project_id = hi.item_project_id
        LEFT JOIN " . $dbPrefix . "companies c ON c.company_id = hi.item_company_id
        WHERE item_id = '$item_id'";

if (!db_loadHash( $sql, $hditem )) {
	$titleBlock = new CTitleBlock( $AppUI->_('Invalid item id'), 'helpdesk.png', $m, 'ID_HELP_HELPDESK_VIEW' );
	$titleBlock->addCrumb( "?m=helpdesk", 'home' );
	$titleBlock->addCrumb( "?m=helpdesk&a=list", 'list' );
	$titleBlock->show();
} else {
  // Check permissions on this record

  $canRead = hditemReadable($hditem);
  $canEdit = hditemEditable($hditem);

  if(!$canRead && !$canEdit){
	  $AppUI->redirect( "m=public&a=access_denied" );
  }

  $name = $hditem['item_requestor'];
  $assigned_to_name = $hditem["item_assigned_to"] ? $hditem["assigned_to_fullname"] : "";
  $assigned_email = $hditem["assigned_email"];

$sql = "
	SELECT 
		" . $dbPrefix . "helpdesk_item_watchers.user_id, 
		CONCAT(contact_first_name, ' ', contact_last_name) as name,
		contact_email
	FROM 
		" . $dbPrefix . "helpdesk_item_watchers
		LEFT JOIN " . $dbPrefix . "users ON " . $dbPrefix . "helpdesk_item_watchers.user_id = " . $dbPrefix . "users.user_id
		LEFT JOIN " . $dbPrefix . "contacts ON user_contact = contact_id
	WHERE 
		item_id = ".$item_id."
	ORDER BY contact_last_name, contact_first_name";

 $watchers = db_loadlist( $sql );

  $titleBlock = new CTitleBlock( 'Viewing Help Desk Item', 'helpdesk.png', $m, 'ID_HELP_HELPDESK_IDX' );
  if (hditemCreate()) {
    $titleBlock->addCell(
      '<input type="submit" class="button" value="'.$AppUI->_('new item').'" />', '',
      '<form action="?m=helpdesk&a=addedit" method="post">', '</form>'
    );
  }

	$titleBlock->addCrumb( "?m=helpdesk", 'home');
	$titleBlock->addCrumb( "?m=helpdesk&a=list", 'list');

	if ($canEdit) {
		$titleBlock->addCrumbDelete('delete this item', 1);
		$titleBlock->addCrumb( "?m=helpdesk&a=addedit&item_id=$item_id", 'edit this item' );
		$titleBlock->addCrumb( "?m=helpdesk&a=export&item_id=$item_id", 'export this item to task' );
	}

	$titleBlock->show();
?>
  <script language="JavaScript">
  function delIt() {
    if (confirm( "<?php print $AppUI->_('doDelete').' '.$AppUI->_('Item').'?';?>" )) {
      document.frmDelete.submit();
    }
  }

  function toggle_comment(id){
     var element = document.getElementById(id)
     element.style.display = (element.style.display == '' || element.style.display == "none") ? "inline" : "none"
  }
  </script>

  <table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td valign="top">
  <table border="0" cellpadding="4" cellspacing="0" width="100%" class="std">
  <tr>
    <td valign="top" width="50%" colspan="2">
      <strong><?php echo $AppUI->_('Item Details')?>  </strong>
       <?php if($hditem["item_deadline"]!=NULL) {
      		$date3 = new CDate( $hditem['item_deadline'] );
		echo "<a title='Deadline: ".$date3->format($format)."'>(";
		echo get_due_time($hditem["item_deadline"]);
		echo ")</a>";
	}
       ?>
     </td>   
  </tr>
  <tr>
    <td valign="top">
      <table cellspacing="1" cellpadding="2" width="100%">
      <tr>
        <!--KZHAO  8-3-2006-->
	<td align="right" nowrap="nowrap"><?php echo $AppUI->_('Item Created')?>:</td>
	<td class="hilite" width="100%"><?php
	if($hditem["item_created"]!=NULL){
		$date1 = new CDate( $hditem['item_created'] );
	  	echo $date1->format($format);
	    }
	 else echo "N/A";
	 
	   ?>
		(<font color="#ff0000"><?php 
			if($hditem["item_created"]!=NULL) { 
				echo get_time_ago($hditem["item_created"]);
			}
			else echo "N/A";			
			?>
		</font>)
	</td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Number')?>:</td>
        <td class="hilite" width="100%"><?php echo $hditem["item_id"]?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Title')?>:</td>
        <td class="hilite" width="100%"><?php echo $hditem["item_title"]?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Requestor')?>:</td>
        <td class="hilite" width="100%"><?php
          print $hditem["item_requestor_email"] ? 
            "<a href=\"mailto:".$hditem["item_requestor_email"]."\">".$hditem['item_requestor']."</a>" :
            $hditem['item_requestor'];?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Requestor Phone')?>:</td>
        <td class="hilite" width="100%"><?php echo $hditem["item_requestor_phone"]?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Assigned To')?>:</td>
        <td class="hilite" width="100%"><?php
          print $assigned_email ?
            "<a href=\"mailto:$assigned_email\">$assigned_to_name</a>" :
            $assigned_to_name;?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Company')?>:</td>
        <td class="hilite" width="100%"><?php echo $hditem["company_name"]?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Project')?>:</td>
        <td class="hilite" width="100%" style="background-color: #<?php echo $hditem['project_color_identifier']?>;"><a href="./index.php?m=projects&a=view&project_id=<?php echo $hditem["project_id"]?>"; style="color: <?php echo  bestColor( $hditem['project_color_identifier'] ) ?>;"><?php echo $hditem["project_name"]?></a></td>
      </tr>
    </table>
    </td><td valign="top">
    <table cellspacing="1" cellpadding="2" width="100%">
      
      <tr>
        <!--KZHAO  8-7-2006-->
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Item Updated')?>:</td>
        <td class="hilite" width="100%">
	<?php if($hditem["item_updated"]!=NULL){
		$date2 = new CDate( $hditem['item_updated'] );
	  	echo $date2->format($format);
	    }
	   elseif($hditem["item_modified"]!=NULL){
	        $date2 = new CDate( $hditem['item_modified'] );
	        echo $date2->format($format);
	    }
	   else echo "Unknown";
	   
	   ?>
		(<font color="#ff0000"><?php 
			if($hditem["item_updated"]!=NULL) { 
				echo get_time_ago($hditem["item_updated"]);
			}
			elseif($hditem["item_modified"]!=NULL){
				echo get_time_ago($hditem["item_modified"]);
			}
			else echo "N/A";			
			?></font>)
	</td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Call Type')?>:</td>
        <td class="hilite" width="100%"><?php
          print $AppUI->_($ict[$hditem["item_calltype"]])." ";
          print dPshowImage (dPfindImage( 'ct'.$hditem["item_calltype"].'.png', $m ), 15, 17, 'align=center');
        ?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Call Source')?>:</td>
        <td class="hilite" width="100%"><?php echo $AppUI->_(@$ics[$hditem["item_source"]])?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Status')?>:</td>
        <td class="hilite" width="100%"><?php echo $AppUI->_(@$ist[$hditem["item_status"]])?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Priority')?>:</td>
        <td class="hilite" width="100%"><?php echo $AppUI->_(@$ipr[$hditem["item_priority"]])?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Severity')?>:</td>
        <td class="hilite" width="100%"><?php echo $AppUI->_(@$isv[$hditem["item_severity"]])?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Operating System')?>:</td>
        <td class="hilite" width="100%"><?php echo $AppUI->_(@$ios[$hditem["item_os"]])?></td>
      </tr>

      <tr>
        <td align="right" nowrap="nowrap"><?php echo $AppUI->_('Application')?>:</td>
        <td class="hilite" width="100%"><?php echo $AppUI->_(@$iap[$hditem["item_application"]])?></td>
      </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td valign="top" colspan="2">
      
      <table cellspacing="0" cellpadding="2" border="0" width="100%">
      <tr>
        <td><strong><?php echo $AppUI->_('Summary')?></strong></td>
        <td><strong><?php echo $AppUI->_('Watchers')?></strong></td>
      <tr>
        <td class="hilite" width="50%"><?php echo str_replace( chr(10), "<br />", linkLinks($hditem["item_summary"]))?>&nbsp;</td>
        <td class="hilite" width="50%"><?php
		$delimiter = "";
		foreach($watchers as $watcher){
			echo "$delimiter <a href=\"mailto: {$watcher['contact_email']}\">".$watcher['name']."</a>";
			$delimiter = ",";
		}
        ?>&nbsp;</td>
      </tr>
      
      </table>

    </td>
  </tr>
  </table>
  </td></tr>
  <tr><td valign="top">
  <?php 

  $tabBox = new CTabBox( "?m=helpdesk&a=view&item_id=$item_id", "", $tab );
  $tabBox->add( dPgetConfig('root_dir') . '/modules/helpdesk/vw_logs', 'Task Logs' );

  if ($canEdit) {
    $tabBox->add( dPgetConfig('root_dir') . '/modules/helpdesk/vw_log_update', 'New Log' );
  }
  $tabBox->add( dPgetConfig('root_dir') . '/modules/helpdesk/vw_history', 'Item History' );

  $tabBox->show();
} 
?>
</td></tr></table>

<form name="frmDelete" action="./index.php?m=helpdesk&a=list" method="post">
  <input type="hidden" name="dosql" value="do_item_aed">
  <input type="hidden" name="del" value="1" />
  <input type="hidden" name="item_id" value="<?php echo $item_id?>" />
</form>


