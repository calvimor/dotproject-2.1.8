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
	var $table_key = 'helpdesk';
	var $table_link = '?m=helpdesk&amp;a=view&amp;ticket=';
	var $table_title = 'Helpdesk';
	var $table_orderby = 'subject';
	var $search_fields = array('author', 'recipient', 'subject', 'type', 'cc', 'body', 'signature');
	var $display_fields = array('author', 'recipient', 'subject', 'type', 'cc', 'body', 
	                            'signature');

	function ctickets () {
		return new tickets();
	}
}
?>
