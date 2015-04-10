<?php /* TASKS $Id: gantt.php 6149 2012-01-09 11:58:40Z ajdonnison $ */
if (!defined('DP_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $AppUI, $m, $a, $locale_char_set;
//global $proFilter, $projectStatus, $showCaption, $showAllGantt;
//global $sortTasksByName, $user_id, $m_orig, $a_orig
global $dPconfig;

ini_set('max_execution_time', 180);
ini_set('memory_limit', $dPconfig['reset_memory_limit']);

include ($AppUI->getLibraryClass('jpgraph/src/jpgraph'));
include ($AppUI->getLibraryClass('jpgraph/src/jpgraph_gantt'));

// get the prefered date format
$df = $AppUI->getPref('SHDATEFORMAT');
$user_id = intval(dPgetParam($_REQUEST, 'user_id', $AppUI->user_id));
$proFilter = (int)dPgetParam($_REQUEST, 'proFilter', '-1');
$company_id = intval(dPgetParam($_REQUEST, 'company_id', ''));
$department = intval(dPgetParam($_REQUEST, 'department', ''));
$showCaption = (int)dPgetParam($_REQUEST, 'showCaption', 0);

$sortTasksByName = (int)dPgetParam($_REQUEST, 'sortTasksByName', 0);
$addPwOiD = (int)dPgetParam($_REQUEST, 'addPwOiD', 0);
$m_orig = dPgetCleanParam($_REQUEST, 'm_orig', $m);
$a_orig = dPgetCleanParam($_REQUEST, 'a_orig', $a);
$taskPin = dPgetCleanParam($_REQUEST, 'taskPin', $a); 
$showWorker = dPgetCleanParam($_REQUEST, 'showWorker', $a );

$showPriority = dPgetCleanParam($_REQUEST, 'showPriority', "" );
$showType = dPgetCleanParam($_REQUEST, 'showType', "" );	

$showTaskPriority = dPgetCleanParam($_REQUEST, 'showTaskPriority', "" );
$showTaskType = dPgetCleanParam($_REQUEST, 'showTaskType', "" );
$showTaskStatus = dPgetCleanParam($_REQUEST, 'showTaskStatus', "" );
	
$projectStatus = dPgetSysVal('ProjectStatus');
$projectStatus = arrayMerge(array('-2' => $AppUI->_('All w/o in progress'), 
                                  '-3' => $AppUI->_(($AppUI->user_id == $user_id) 
                                                    ? 'My projects' : "User's projects")), 
                            $projectStatus);

$addProjectsWithAssignedTasks = (($AppUI->getState('addProjWithTasks'))
                                 ? $AppUI->getState('addProjWithTasks') : 0);

$pjobj = new CProject;
$working_hours = $dPconfig['daily_working_hours'];

$q = new DBQuery;

/* 
 * Load department info for the case where one
 * wants to see the ProjectsWithOwnerInDeparment (PwOiD)
 * instead of the projects related to the given department.
 */
$owner_ids = array();
if ($addPwOiD && $department > 0) {
	$q->addTable('users');
	$q->addQuery('user_id');
	$q->addJoin('contacts', 'c', 'c.contact_id = user_contact');
	$q->addWhere('c.contact_department = '.$department);
	$owner_ids = $q->loadColumn();	
	$q->clear();
}

if ( isset($department) and !empty($department) ){

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
	$sql = $q->prepare;
	//file_put_contents( 'files/temp/debug', "$sql\n", FILE_APPEND );
	$project_department = $q->loadColumn();

}

$q->clear();

// pull valid projects and their percent complete information
// GJB: Note that we have to special case duration type 24 and this refers to the hours in a day, NOT 24 hours
$q->addTable('projects', 'p');

$q->addQuery('DISTINCT p.project_id, project_color_identifier, project_name, project_start_date' 
             . ', project_end_date, max(t1.task_end_date) AS project_actual_end_date' 
             . ', SUM(task_duration * task_percent_complete * IF(task_duration_type = 24, ' 
             . $working_hours . ', task_duration_type))' 
             . ' / SUM(task_duration * IF(task_duration_type = 24, ' 
             . $working_hours . ', task_duration_type)) AS project_percent_complete' 
             . ', project_status');
$q->addJoin('tasks', 't1', 'p.project_id = t1.task_project');

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
	
if ($proFilter == '-4') {
	$q->addWhere('project_status != 7');
} else if ($proFilter == '-3') {
	$q->addWhere('project_owner = ' . $user_id);
} else if ($proFilter == '-2') {
	$q->addWhere('project_status != 3');
} else if ($proFilter != '-1') {
	$q->addWhere('project_status = ' . $proFilter);
}
//	if (isset($project_status) && $currentTabId != 500) {
	//	$q->addWhere('p.project_status = '.$project_status);
//	}

if ($user_id && $m_orig == 'admin' && $a_orig == 'viewuser') {
	$q->addWhere('project_owner = ' . $user_id);
}

if ($taskPin == '1') {
	$q->addJoin('user_task_pin','tp','tp.task_id=t1.task_id' );
	$q->addWhere('tp.task_pinned = 1');
}

if ( $showPriority ){
	$q->addWhere( 'project_priority = ' . $showPriority );
}	
if ( $showType ){
	$q->addWhere( 'project_type = ' . $showType );
}

$pjobj->setAllowedSQL($AppUI->user_id, $q, null, 'p');
$q->addGroup('p.project_id');
$q->addOrder('project_name, task_end_date DESC');

$sql = $q->prepare() . "\n";
// A prefered way to debug this code because gantt.php is placed as a src of an Html img tag
//file_put_contents( 'files/temp/gantt-debug', "$sql\n", FILE_APPEND );

$projects = $q->loadList();
$q->clear();

// Don't push the width higher than about 1200 pixels, otherwise it may not display.
$width = min((int)dPgetParam($_GET, 'width', 600), 1400);
$start_date = dPgetCleanParam($_GET, 'start_date', 0);
$end_date   = dPgetCleanParam($_GET, 'end_date', 0);

$showAllGantt = (int)dPgetParam($_REQUEST, 'showAllGantt', '0');

$graph = new GanttGraph($width);
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);

$graph->SetFrame(false);
$graph->SetBox(true, array(0,0,0), 2);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);

$pLocale = setlocale(LC_TIME, 0); // get current locale for LC_TIME
$res = @setlocale(LC_TIME, $AppUI->user_lang[0]);
if ($res) { // Setting locale doesn't fail
	$graph->scale->SetDateLocale($AppUI->user_lang[0]);
}
setlocale(LC_TIME, $pLocale);

if ($start_date && $end_date) {
	$graph->SetDateRange($start_date, $end_date);
}

$graph->scale->actinfo->SetFont(FF_CUSTOM, FS_NORMAL, 8);
$graph->scale->actinfo->vgrid->SetColor('gray');
$graph->scale->actinfo->SetColor('darkgray');
$graph->scale->actinfo->SetColTitles(array($AppUI->_('Project name', UI_OUTPUT_RAW), 
                                           $AppUI->_('Start Date', UI_OUTPUT_RAW), 
                                           $AppUI->_('Finish', UI_OUTPUT_RAW), 
                                           $AppUI->_('Actual End', UI_OUTPUT_RAW)), 
                                     array(160, 70, 70, 70));


$tableTitle = (($proFilter == '-1') ? $AppUI->_('All Projects') : $projectStatus[$proFilter]);
$graph->scale->tableTitle->Set($tableTitle);

// Use TTF font if it exists
// try commenting out the following two lines if gantt charts do not display
if (is_file(TTF_DIR . 'FreeSansBold.ttf')) {
	$graph->scale->tableTitle->SetFont(FF_CUSTOM,FS_BOLD,12);
}	
$graph->scale->SetTableTitleBackground('#EEEEEE');
$graph->scale->tableTitle->Show(true);

//-----------------------------------------
// nice Gantt image
// if diff(end_date,start_date) > 90 days it shows only
//week number
// if diff(end_date,start_date) > 240 days it shows only
//month number
//-----------------------------------------
if ($start_date && $end_date) {
	$min_d_start = new CDate($start_date);
	$max_d_end = new CDate($end_date);
	$graph->SetDateRange($start_date, $end_date);
	$msg = "Date set to\n";
	// $min_d_start, $max_d_end\n";
} else {
	// find out DateRange from gant_arr

	$d_start = new CDate();
	$d_end = new CDate();
	for ($i = 0, $xi = count(@$projects); $i < $xi; $i++) {
		
		/* Q. Where does $p come from > */
		$start = mb_substr($p['project_start_date'], 0, 10);
		$end = mb_substr($p['project_end_date'], 0, 10);
		
		$d_start->Date($start);
		$d_end->Date($end);
		
		if ($i == 0) {
			$min_d_start = $d_start;
			$max_d_end = $d_end;
		} else {
			if (Date::compare($min_d_start,$d_start)>0) {
				$min_d_start = $d_start;
			}
			if (Date::compare($max_d_end,$d_end)<0) {
				$max_d_end = $d_end;
			}
		}
	}
}

// check day_diff and modify Headers
$day_diff = $max_d_end->dateDiff($min_d_start);

if ($day_diff > 240) {
	//more than 240 days
	$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH);
} else if ($day_diff > 90) {
	//more than 90 days and less of 241
	$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HWEEK);
	$graph->scale->week->SetStyle(WEEKSTYLE_WNBR);
}

$row = 0;

if (!is_array($projects) || sizeof($projects) == 0) {
	$d = new CDate();
	$bar = new GanttBar($row++, array(' '.$AppUI->_('No projects found'),  ' ', ' ', ' '), 
	                    $d->getDate(), $d->getDate(), ' ', 0.6);
	$bar->title->SetFont(FF_CUSTOM, FS_NORMAL, 8);
	$bar->title->SetColor('red');
	$graph->Add($bar);
}

if (is_array($projects)) {
	foreach ($projects as $p) {
		if ($locale_char_set=='utf-8' && function_exists('utf8_decode')) {
			$name = ((mb_strlen(utf8_decode($p['project_name'])) > 25) 
			         ? (mb_substr(utf8_decode($p['project_name']), 0, 22) . '...') 
			         : utf8_decode($p['project_name']));
		} else {
			//while using charset different than UTF-8 we need not to use utf8_deocde
			$name = ((mb_strlen($p['project_name']) > 25) ? (mb_substr($p['project_name'], 0, 22).'...') 
			         : $p['project_name']) ;
		}
		
		//using new jpGraph determines using Date object instead of string
		$start = (($p['project_start_date'] > '0000-00-00 00:00:00') ? $p['project_start_date'] 
		          : date('Y-m-d H:i:s'));
		$end_date = (($p['project_end_date'] > '0000-00-00 00:00:00') ? $p['project_end_date'] 
		             : date('Y-m-d H:i:s', time()));
		
		$end_date = new CDate($end_date);
		//$end->addDays(0);
		$end = $end_date->getDate();
		
		$start = new CDate($start);
		//$start->addDays(0);
		$start = $start->getDate();
		
		$progress = $p['project_percent_complete'] + 0;
		
		$caption = '';
		if (!($start) || $start == '0000-00-00 00:00:00') {
			$start = ((!($end)) ? date('Y-m-d') : $end);
			$caption .= $AppUI->_('(no start date)');
		}
		
		if (!($end)) {
			$end = $start;
			$caption .= (' ' . $AppUI->_('(no end date)'));
		} else {
			$cap = '';
		}
		
        if ($showCaption) {
			$caption .= ($AppUI->_($projectStatus[$p['project_status']]) . ', ');
			$caption .= (($p['project_status'] <> 7) ? $AppUI->_('active') : $AppUI->_('archived'));
        }
		$enddate = new CDate($end);
		$startdate = new CDate($start);
		$actual_end = (($p['project_actual_end_date']) ? $p['project_actual_end_date'] : $end);
		
		$actual_enddate = new CDate($actual_end);
		$actual_enddate = $actual_enddate->after($startdate) ? $actual_enddate : $enddate;

        $bar = new GanttBar($row++, array($name, $startdate->format($df), $enddate->format($df), 
		                                  $actual_enddate->format($df)), 
		                    $start, $actual_end, $cap, 0.6);
		                    
		$bar->progress->Set(min(($progress/100), 1));
		
        if (is_file(TTF_DIR."FreeSans.ttf")) {
			$bar->title->SetFont(FF_CUSTOM, FS_NORMAL, 9);
        }

        $bar->SetFillColor('#' . $p['project_color_identifier']);
        $bar->SetPattern(BAND_SOLID, ('#' . $p['project_color_identifier']));

		//adding captions
		$bar->caption = new TextProperty($caption);
		$bar->caption->Align('left','center');
		$bar->caption->SetFont(FF_CUSTOM, FS_NORMAL, 8);
		
        // gray out templates, completes, on ice, on hold
        if ($p['project_status'] != '3' || $p['project_status'] == '7') {
			$bar->caption->SetColor('darkgray');
			$bar->title->SetColor('darkgray');
			$bar->SetColor('darkgray');
			$bar->SetFillColor('gray');
			//$bar->SetPattern(BAND_SOLID,'gray');
			$bar->progress->SetFillColor('darkgray');
			$bar->progress->SetPattern(BAND_SOLID,'darkgray',98);
        }
		
		$graph->Add($bar);
		
		// If showAllGant checkbox is checked 
		if ($showAllGantt) {
			// insert tasks into Gantt Chart
			// select for tasks for each project	
			$q->addTable('tasks', 't');

			$q->addQuery(' t.task_id, t.task_name, t.task_start_date, t.task_end_date' 
			             . ', t.task_milestone, t.task_dynamic');

			$q->addJoin('projects', 'p', 'p.project_id = t.task_project');
			$q->addWhere('p.project_id = '. $p['project_id']);

			if ( $showTaskPriority ){
				$q->addWhere( 'task_priority = ' . $showTaskPriority );
			}	
			if ( $showTaskType ){
				$q->addWhere( 'task_type = ' . $showTaskType );
			}

			if ( $showTaskStatus == '0' ){
				$q->addWhere( 'task_status =' . $showTaskStatus );
			}

			$q->addOrder((($sortTasksByName) ? 't.task_name' : 't.task_end_date ASC'));
			$sql = $q->prepare();
			
			// A prefered way to debug this code because gantt.php is placed as a src of an Html img tag
			//file_put_contents( 'files/temp/debug', "$sql\n", FILE_APPEND );
			$tasks = $q->loadList();
			$q->clear();
			if ( count( $tasks ) == 0 )
				$AppUI->setMsg( 'No tasks for project ' . $p['project_name'] );
			foreach ($tasks as $t) {
				if ($t['task_end_date'] == null) {
					$t['task_end_date'] = $t['task_start_date'];
				}

				$tStart = (($t['task_start_date'] > '0000-00-00 00:00:00') ? $t['task_start_date'] 
				           : $start);
				$tEnd = (($t['task_end_date'] > '0000-00-00 00:00:00') ? $t['task_end_date'] 
				         : $end);
				$tStartObj = new CDate($tStart);
				$tEndObj = new CDate($tEnd);
 				
				if ($t['task_milestone'] != 1) {
					$bar2 = new GanttBar($row++, array((mb_substr(' --' . $t['task_name'], 0, 20) 
					                                   . '...'), $tStartObj->format($df), 
					                                   $tEndObj->format($df), ' '), 
					                     $tStart, $tEnd, ' ', (($t['task_dynamic'] == 1) 
					                                           ? 0.1 : 0.6));
					
					$bar2->title->SetColor(bestColor('#ffffff', 
					                                 ('#' . $p['project_color_identifier']), 
					                                 '#000000'));
					$bar2->title->SetFont(FF_CUSTOM, FS_NORMAL, 9);
					$bar2->SetFillColor(('#' . $p['project_color_identifier']));
					$graph->Add($bar2);
				} else {
					$bar2 = new MileStone ($row++, ('-- ' . $t['task_name']), 
					                       $t['task_start_date'], $tStartObj->format($df));
					$bar2->title->SetFont(FF_CUSTOM, FS_NORMAL, 9);
					$bar2->title->SetColor('#CC0000');
					$graph->Add($bar2);
				}				
 				
				//Insert workers for each task into Gantt Chart 				
				$q->addTable('user_tasks', 't');
				$q->addQuery('DISTINCT user_username, t.task_id');
				$q->addJoin('users', 'u', 'u.user_id = t.user_id');
				$q->addWhere('t.task_id = ' . $t['task_id']);
				$q->addOrder('user_username ASC');
				$workers = $q->loadList();
				$q->clear();
				$workersName = '';
				
				if ( $showWorker ){
				
					foreach ($workers as $w) {	
						$workersName .= (' ' . $w['user_username']);
					
						$bar3 = new GanttBar($row++, 
											array(('   * ' . $w['user_username']), ' ', ' ',' '), 
											$tStartObj->format(FMT_DATETIME_MYSQL), 
											$tEndObj->format(FMT_DATETIME_MYSQL), 0.6);
						$bar3->title->SetFont(FF_CUSTOM, FS_NORMAL, 9);
						$bar3->title->SetColor(bestColor('#ffffff', 
														('#' . $p['project_color_identifier']), 
														'#000000'));
						$bar3->SetFillColor('#' . $p['project_color_identifier']);		
						$graph->Add($bar3);
					}	
				}
				//End of insert workers for each task into Gantt Chart
			}
			unset($tasks);
			//End of insert tasks into Gantt Chart 
		}			
		//End of if showAllGant checkbox is checked
	}
} // End of check for valid projects array.
unset($projects);

$today = date('y-m-d');
$vline = new GanttVLine($today, $AppUI->_('Today', UI_OUTPUT_RAW));
$vline->title->SetFont(FF_CUSTOM, FS_BOLD, 10);
$graph->Add($vline);
$graph->Stroke();
?>
