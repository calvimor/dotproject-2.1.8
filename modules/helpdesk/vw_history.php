<?php
global $HELPDESK_CONFIG, $hditem, $isa, $m, $item_id, $dbPrefix;

// User's specified format for date and time
$df = $AppUI->getPref('SHDATEFORMAT');
$tf = $AppUI->getPref('TIMEFORMAT');

$isa = dPgetSysVal( 'HelpDeskAuditTrail' );

// Get pagination page
if (isset($_GET['page'])) {
  $AppUI->setState('HelpDeskLogPage', $_GET['page']);
} else {
  $AppUI->setState('HelpDeskLogPage', 0);
}

$page = $AppUI->getState('HelpDeskLogPage') ? $AppUI->getState('HelpDeskLogPage') : 0;

// Load status log
$sql = "SELECT *,
        TRIM(CONCAT(co.contact_first_name,' ',co.contact_last_name)) modified_by,
        co.contact_email as email
        FROM " . $dbPrefix . "helpdesk_item_status h
        LEFT OUTER JOIN " . $dbPrefix . "users u ON u.user_id = h.status_modified_by
        LEFT OUTER JOIN " . $dbPrefix . "contacts co  ON u.user_contact = co.contact_id
        WHERE h.status_item_id='{$hditem['item_id']}'
        ORDER BY h.status_date";

// Pagination
$status_log_items_per_page = $HELPDESK_CONFIG['status_log_items_per_page'];

// Figure out number of total log entries
$total_logs = db_num_rows(db_exec($sql));

// Now lets do the offset
$offset = $page * $status_log_items_per_page;

// Limit the results to enable pagination
$sql .= " LIMIT $offset,$status_log_items_per_page";

$status_log = db_loadList($sql);
  
?>

<table border="0" cellpadding="4" cellspacing="0" width="100%" >
<tr>
  <td><b><?php echo $AppUI->_('Item History')?></b></td>
  <td align="right">
    <?php
    if ($total_logs > $status_log_items_per_page) {
      $pages_per_side = $HELPDESK_CONFIG['pages_per_side'];
      $pages = ceil($total_logs / $status_log_items_per_page) - 1;

      $link = "?m=helpdesk&a=view&item_id=$item_id&page=";

      if ($page < $pages_per_side) {
        $start = 0;
      } else {
        $start = $page - $pages_per_side;
      }

      if ($page > ($pages - $pages_per_side)) {
        $end = $pages;
      } else {
        $end = $page + $pages_per_side;
      }

      if ($page > 0) {
        print "<a href=\"$link".($page - 1)."\">&larr; Previous</a>&nbsp;&nbsp;";
      }

      for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) {
          print " <b>".($i + 1)."</b>";
        } else {
          print " <a href=\"$link$i\">".($i + 1)."</a>";
        }
      }

      if ($page < $pages) {
        print "&nbsp;&nbsp;<a href=\"$link".($page + 1)."\">Next &rarr;</a>";
      }
    }
    ?></td>
  </tr>
</table>
<table cellspacing="1" cellpadding="2" border="0" width="100%" class="std">
<?php
$last_date = "";

if (is_array($status_log)) {
  foreach ($status_log as $log) {
    $log_date = new CDate($log['status_date']);
    $date = $log_date->format( $df );
    if($date!=$last_date){
      $last_date = $date;
    ?>
    <tr>
      <th nowrap="nowrap" colspan="3"><?php echo $date?>:</th>
    </tr>
    <?php
    }
  
    $time = $log_date->format( $tf );
    ?>
    <tr>
      <td class="hilite" nowrap="nowrap" width="1%"><?php echo $time?></td>
      <td class="hilite" nowrap="nowrap" width="1%"><?php echo ($log['email']?"<a href=\"mailto: {$log['email']}\">{$log['modified_by']}</a>":$log['modified_by'])?></td>
      <td class="hilite" width="98%"><?php
        if($log['status_code']==0 || $log['status_code']==17){
          // Created or Deleted
          print $AppUI->_($isa[$log['status_code']]);
        } else if ($log['status_code'] == 16) {
          // Comment
          print "<a href=\"javascript:void(0);\"
                    onClick=\"toggle_comment('{$log['status_id']}_short');
                              toggle_comment('{$log['status_id']}_long');\">"
              . dPshowImage (dPfindImage( 'toggle.png', $m ), 16, 16, '')
              . "</a>";

          print "<span style='display: inline' id='{$log['status_id']}_short'> "
              . $AppUI->_($isa[$log['status_code']]) . " "
              . htmlspecialchars(substr($log['status_comment'],0,8))
              . "</span><span style='display: none' id='{$log['status_id']}_long'> "
              . $AppUI->_($isa[$log['status_code']]) . " "
              . htmlspecialchars($log['status_comment'])
              . "</span>";
        } else {
          // Everything else
          print $AppUI->_($isa[$log['status_code']])." ".$log['status_comment'];
        }
      ?></td>
    </tr>
    <?php
  }
}
?>
</table>
