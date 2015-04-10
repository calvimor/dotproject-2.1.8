<?php  /* PROJECTS $Id: index.php 6182 2012-11-02 09:17:02Z ajdonnison $ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $cBuffer;

$AppUI->savePlace();
$q = new DBQuery();

// load the companies class to retrieved denied companies
require_once ($AppUI->getModuleClass('companies'));

// Let's update project status!
if (isset($_GET['update_project_status']) && isset($_GET['project_status']) 
   && isset($_GET['project_id'])) {
	$projects_id = $_GET['project_id']; // This must be an array
	
	foreach ($projects_id as $project_id) {
		if (! getPermission('projects', 'edit', (int)$project_id)) {
			continue; /* Cannot update the status of a project we can't edit */
		}
		$q->addTable('projects');
		$q->addUpdate('project_status', $_GET['project_status']);
		$q->addWhere('project_id = ' . (int)$project_id);
		$q->exec();
		$q->clear();
	}
	// Insert our closing for the select
	$bufferUser .= '</select>'."\n";
}

// End of project status update
// retrieve any state parameters
if (isset($_GET['tab'])) {
	$AppUI->setState('ProjIdxTab', intval(dPgetCleanParam($_GET, 'tab')));
}

$tab = $AppUI->getState('ProjIdxTab') !== NULL ? $AppUI->getState('ProjIdxTab') : 500;

$currentTabId = $tab;
$active = intval(!$AppUI->getState('ProjIdxTab'));

// Projects filter
if (isset($_POST['company_id'])) {
	$AppUI->setState('ProjIdxCompany', intval($_POST['company_id']));
}

$company_id = (($AppUI->getState('ProjIdxCompany') !== NULL) 
               ? $AppUI->getState('ProjIdxCompany') 
               : $AppUI->user_company);

$company_prefix = 'company_';

if (isset($_POST['department'])) {
	$AppUI->setState('ProjIdxDepartment', dPgetCleanParam($_POST, 'department'));
	//if department is set, ignore the company_id field
	unset($company_id);
}

$department = (($AppUI->getState('ProjIdxDepartment') !== NULL) 
               ? $AppUI->getState('ProjIdxDepartment') 
               : ($company_prefix . $AppUI->user_company));

//if $department contains the $company_prefix string that it's requesting a company
// and not a department.  So, clear the $department variable, and populate the $company_id variable.
if (!(mb_strpos($department, $company_prefix)===false)) {
	
	$company_id = mb_substr($department,mb_strlen($company_prefix));
	$AppUI->setState('ProjIdxCompany', $company_id);
	unset($department);
}

//for getting permissions for records related to projects
$obj_project = new CProject();

// Not Gantt ?
if ( $tab !== 501 ){

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

	$AppUI->setState('ProjIdxOrderDir', $orderdir);

	$bufferUser = getUserFilter();
	
	// setting this to filter project_list_data function below
	// 0 = undefined
	// 3 = active
	// 5 = completed
	// 7 = archived
	// collect the full (or filtered) projects list data via function in projects.class.php only if not Gantt tab
	projects_list_data();

} else{
	
	// regenerate $cBuffer;

	$cBuffer = getCompanyFilter( $company_id, $company_prefix );
	
	$bufferUser = getUserFilter();

}	

// setup the title block
$titleBlock = new CTitleBlock('Projects', 'applet3-48.png', $m, ($m . '.' . $a));
$titleBlock->addCell($AppUI->_('Owner') . ':');
$titleBlock->addCell(('<form action="?m=projects" method="post" name="pickUser">' . "\n" 
                      . $bufferUser . "\n" . '</form>' . "\n"));
$titleBlock->addCell($AppUI->_('Company') . '/' . $AppUI->_('Division') . ':');
$titleBlock->addCell(('<form action="?m=projects" method="post" name="pickCompany">' . "\n" 
                      . $cBuffer . "\n" .  '</form>' . "\n"));
$titleBlock->addCell();
if ($canAuthor) {
	$titleBlock->addCell(('<form action="?m=projects&amp;a=addedit" method="post">' . "\n" 
	                      . '<input type="submit" class="button" value="' 
	                      . $AppUI->_('new project') . '" />'. "\n" . '</form>' . "\n"));
}
$titleBlock->show();

// Set the project counters per status
$project_types = dPgetSysVal('ProjectStatus');

if ( isset($department) ){
		
	//
	 //* If a department is specified, we want to display projects from the department
	 //* and all departments under that, so we need to build that list of departments
	 //
	$q->addTable('departments');
	$q->addQuery('dept_id, dept_parent');
	$q->addOrder('dept_parent,dept_name');
	$rows = $q->loadList();
		
} else{
	
	$q->addTable('project_departments');
	$q->addQuery('project_id');
	$q->addOrder('project_id');
	$project_department = $q->loadColumn();
	
}

$q->clear();

// count number of projects per project_status
// take into consideration the overall filters imposed on the list see projects_list_data()
$q->addTable('projects', 'p');
$q->addQuery('p.project_status, COUNT(p.project_id) as count');

if (isset($department) and ! empty( $department ) ) {

	if (!$addPwOiD) {

		// Join the departments table
		$q->addJoin('project_departments', 'pd', 'pd.project_id = p.project_id');
		$q->addJoin( 'departments', 'd', 'd.dept_id=pd.department_id');
		$q->addQuery( 'd.dept_name' );
			
		$dept_ids = addDeptId($rows, $department);
		$q->addWhere('pd.department_id in (' . implode(',',$dept_ids) . ')');
	} else {
		// Show Projects where the Project Owner is in the given department
		$q->addWhere('p.project_owner IN ('
		             . ((!empty($owner_ids)) ? implode(',', $owner_ids) : 0) . ')');
	}
		
} else{
	
	$project_department_list = implode( ',', $project_department );
	$q->addWhere( "p.project_id not in (" . $project_department_list . ")" );
		
}

$obj_project->setAllowedSQL($AppUI->user_id, $q, null, 'p');

if ($owner > 0) {
	$q->addWhere('p.project_owner = ' . $owner);
}	 
$q->addGroup('project_status');

$statuses = $q->loadHashList('project_status');

$q->clear();
$all_projects = 0;
foreach ($statuses as $k => $v) {
	$project_status_tabs[$v['project_status']] = ($AppUI->_($project_types[$v['project_status']]) 
													  . ' (' . $v['count'] . ')');
	//count all projects
	$all_projects += $v['count'];
}

//set file used per project status title
$fixed_status = array('In Progress' => 'vw_idx_active',
					  'Complete' => 'vw_idx_complete',
					  'Archived' => 'vw_idx_archived');

/**
* Now, we will figure out which vw_idx file are available
* for each project status using the $fixed_status array 
*/
$project_status_file = array();
foreach ($project_types as $status_id => $status_title) {
	//if there is no fixed vw_idx file, we will use vw_idx_proposed
	$project_status_file[$status_id] = ((isset($fixed_status[$status_title])) 
										? $fixed_status[$status_title] : 'vw_idx_proposed');
}

// tabbed information boxes
$tabBox = new CTabBox('?m=projects', DP_BASE_DIR . '/modules/projects/', $tab);

$tabBox->add('vw_idx_proposed', $AppUI->_('All') . ' (' . $all_projects . ')' , true,  500);
foreach ($project_types as $psk => $project_status) {
		$tabBox->add($project_status_file[$psk], 
					 (($project_status_tabs[$psk]) ? $project_status_tabs[$psk] : $AppUI->_($project_status)), true, $psk);
}
$min_view = true;
$tabBox->add('viewgantt', 'Gantt');
$tabBox->show();
?>
