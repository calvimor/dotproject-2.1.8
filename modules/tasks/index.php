<?php /* TASKS $Id: index.php 6149 2012-01-09 11:58:40Z ajdonnison $ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

$AppUI->savePlace();

// retrieve any state parameters
$user_id = $AppUI->user_id;
if (getPermission('admin', 'view')) { // Only sysadmins are able to change users
	if ((int)dPgetParam($_POST, 'user_id', 0) != 0) { // this means that 
		$user_id = (int)dPgetParam($_POST, 'user_id', 0);
		$AppUI->setState('user_id', $_POST['user_id']);
	} else if ($AppUI->getState('user_id')) {
		$user_id = $AppUI->getState('user_id');
	} else {
		$AppUI->setState('user_id', $user_id);
	}
}


if (isset($_POST['f'])) {
	$AppUI->setState('TaskIdxFilter', $_POST['f']);
}
$f= $AppUI->getState('TaskIdxFilter') ? $AppUI->getState('TaskIdxFilter') : 'myunfinished';

//if (isset($_POST['f2'])) {
	//$AppUI->setState('CompanyIdxFilter', $_POST['f2']);
//}
//$f2 = $AppUI->getState('CompanyIdxFilter') ? $AppUI->getState('CompanyIdxFilter') : 'all';

/* Copy code from projects/index.php */

if (isset($_POST['company_id'])) {
	$AppUI->setState('ProjIdxCompany', intval($_POST['company_id']));
}

$company_id = (($AppUI->getState('ProjIdxCompany') !== NULL) 
               ? $AppUI->getState('ProjIdxCompany') 
               : $AppUI->user_company);

$company_prefix = 'company_';
if (isset($_POST['department'])) {
	$AppUI->setState('TaskIdxDepartment', dPgetCleanParam($_POST, 'department'));
	
	//if department is set, ignore the company_id field
	unset($company_id);
}

$department = (($AppUI->getState('TaskIdxDepartment') !== NULL) 
               ? $AppUI->getState('TaskIdxDepartment') 
               : ($company_prefix . $AppUI->user_company));

//if $department contains the $company_prefix string that it's requesting a company
// and not a department.  So, clear the $department variable, and populate the $company_id variable.
$company_prefix = 'company_';
if (!(mb_strpos($department, $company_prefix)===false)) {
	$company_id = mb_substr($department,mb_strlen($company_prefix));
	$AppUI->setState('TaskIdxCompany', $company_id);
	unset($department);
}

// collect the full (or filtered) projects list data via function in projects.class.php

$valid_ordering = array('project_name', 'user_username', 'my_tasks desc', 'total_tasks desc',
                        'total_tasks', 'my_tasks', 'project_color_identifier', 'company_name', 
                        'project_end_date', 'project_start_date', 'project_actual_end_date', 
                        'task_log_problem DESC,project_priority', 'project_status', 
                        'project_percent_complete');

$orderdir = $AppUI->getState('ProjIdxOrderDir') ? $AppUI->getState('ProjIdxOrderDir') : 'asc';
if (isset($_GET['orderby']) && in_array($_GET['orderby'], $valid_ordering)) {
	$orderdir = (($AppUI->getState('ProjIdxOrderDir') == 'asc') ? 'desc' : 'asc');
	$AppUI->setState('ProjIdxOrderBy', $_GET['orderby']);
}

$orderby = (($AppUI->getState('ProjIdxOrderBy'))
            ? $AppUI->getState('ProjIdxOrderBy') : 'project_end_date');
/*
$AppUI->setState('ProjIdxOrderDir', $orderdir);
*/
 
$obj_project = new CProject();
projects_list_data();

/* End of Code copy */

if (isset($_GET['project_id'])) {
	$AppUI->setState('TaskIdxProject', $_GET['project_id']);
}
$project_id = $AppUI->getState('TaskIdxProject') ? $AppUI->getState('TaskIdxProject') : 0;

// get CCompany() to filter tasks by company
require_once($AppUI->getModuleClass('companies'));
$obj = new CCompany();
$companies = $obj->getAllowedRecords($AppUI->user_id, 'company_id,company_name', 'company_name');
$filters2 = arrayMerge(array('all' => $AppUI->_('All Companies', UI_OUTPUT_RAW)), $companies);

// setup the title block
$titleBlock = new CTitleBlock('Tasks', 'applet-48.png', $m, "$m.$a");

// patch 2.12.04 text to search entry box
if (isset($_POST['searchtext'])) {
	$AppUI->setState('searchtext', $_POST['searchtext']);
}

$search_text = $AppUI->getState('searchtext') ? $AppUI->getState('searchtext'):'';
$search_text = dPformSafe($search_text);

$titleBlock->addCell('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $AppUI->_('Search') . ':');
$titleBlock->addCell(('<input type="text" class="text" size="20" name="searchtext"' 
                      . ' onchange="javascript:document.searchfilter.submit();" value="' . $search_text 
                      . '"title="' . $AppUI->_('Search in name and description fields') 
                      . '"/><!--<input type="submit" class="button" value=">" title="' 
                      . $AppUI->_('Search in name and description fields') . '"/>-->'), '',
                     '<form action="?m=tasks" method="post" id="searchfilter">', '</form>');
// Let's see if this user has admin privileges
if (getPermission('admin', 'view')) {
	$titleBlock->addCell();
	$titleBlock->addCell($AppUI->_('User') . ':');
	
	$perms =& $AppUI->acl();
	$user_list = $perms->getPermittedUsers('tasks');
	$titleBlock->addCell(arraySelect($user_list, 'user_id', 
	                                 ('size="1" class="text"' 
	                                  . ' onchange="javascript:document.userIdForm.submit();"'), 
	                                 $user_id, false), '',
	                     '<form action="?m=tasks" method="post" name="userIdForm">','</form>');
}

$titleBlock->addCell();
$titleBlock->addCell($AppUI->_('Company') . ':');
$titleBlock->addCell(('<form action="?m=tasks" method="post" name="pickCompany">' . "\n" 
                      . $cBuffer . "\n" .  '</form>' . "\n"));
/*
$titleBlock->addCell(arraySelect($filters2, 'department', 
                                 'size=1 class=text onchange="javascript:document.companyFilter.submit();"', 
                                 $department, false), '', 
                     '<form action="?m=tasks" method="post" name="companyFilter">', '</form>'
);
*/
$titleBlock->addCell();
if ($canEdit && $project_id) {
	$titleBlock->addCell(('<input type="submit" class="button" value="' . $AppUI->_('new task') 
	                      . '" />'), '', 
						 ('<form action="?m=tasks&amp;a=addedit&amp;task_project=' . $project_id 
	                      . '" method="post">'), '</form>');
}

$titleBlock->show();

if (dPgetCleanParam($_GET, 'inactive', '') == 'toggle')
	$AppUI->setState('inactive', $AppUI->getState('inactive') == -1 ? 0 : -1);
$in = $AppUI->getState('inactive') == -1 ? '' : 'in';

// use a new title block (a new row) to prevent from oversized sites
$titleBlock = new CTitleBlock('', 'shim.gif');
$titleBlock->showhelp = false;
$titleBlock->addCell('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $AppUI->_('Task Filter') . ':');
$titleBlock->addCell(arraySelect($filters, 'f', 
                                 'size=1 class=text onchange="javascript:document.taskFilter.submit();"', 
                                 $f, true), '',
                     '<form action="?m=tasks" method="post" name="taskFilter">', '</form>');
$titleBlock->addCell();

$titleBlock->addCrumb('?m=tasks&amp;a=todo&amp;user_id=' . $user_id, 'my todo');
if ((int)dPgetParam($_GET, 'pinned') == 1) {
	$titleBlock->addCrumb('?m=tasks', 'all tasks');
} else {
	$titleBlock->addCrumb('?m=tasks&amp;pinned=1', 'my pinned tasks');
}
$titleBlock->addCrumb('?m=tasks&amp;inactive=toggle', 'show '.$in.'active tasks');
$titleBlock->addCrumb('?m=tasks&amp;a=tasksperuser', 'tasks per user');
$titleBlock->addCrumb('?m=projects&amp;a=reports', 'reports');

$titleBlock->show();

// include the re-usable sub view
$min_view = false;

include(DP_BASE_DIR.'/modules/tasks/tasks.php');
