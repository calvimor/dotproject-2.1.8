<?php /* SMARTSEARCH$Id: helpdesk.inc.php 6038 2010-10-03 05:49:01Z j.lelarge $ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

/**
* Helpdesk Class
*/
class helpdesk extends smartsearch 
{
	var $table = 'helpdesk_items';
	var $table_module = 'helpdesk';
	var $table_key = 'item_id';
	var $table_link = '?m=helpdesk&amp;a=view&amp;ticket=';
	var $table_title = 'Helpdesk';
	var $table_orderby = 'item_title';
	var $search_fields = array('item_title');
	var $display_fields = array('item_title'); 

	function ctickets () {
		return new tickets();
	}
}
?>
