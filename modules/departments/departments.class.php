<?php /* DEPARTMENTS $Id: departments.class.php 6191 2013-01-05 04:28:23Z ajdonnison $ */
if (!defined('DP_BASE_DIR')) {
  die('You should not access this file directly.');
}

##
## CDepartment Class
##

class CDepartment extends CDpObject {
	var $dept_id = NULL;
	var $dept_parent = NULL;
	var $dept_company = NULL;
	var $dept_name = NULL;
	var $dept_phone = NULL;
	var $dept_fax = NULL;
	var $dept_address1 = NULL;
	var $dept_address2 = NULL;
	var $dept_city = NULL;
	var $dept_state = NULL;
	var $dept_zip = NULL;
	var $dept_url = NULL;
	var $dept_desc = NULL;
	var $dept_owner = NULL;

	public function __construct() {
		parent::__construct('departments', 'dept_id');
	}

	function load($oid=null, $strip = true) {
		$q  = new DBQuery;
		$q->addTable('departments','dep');
		$q->addQuery('dep.*');
		$q->addWhere('dep.dept_id = '.$oid);
		$sql = $q->prepare();
		$q->clear();
		return db_loadObject($sql, $this);
	}

	function bind($hash) {
		if (!is_array($hash)) {
			return get_class($this)."::bind failed";
		} else {
			bindHashToObject($hash, $this);
			return NULL;
		}
	}

	function check() {
		if ($this->dept_id === NULL) {
			return 'department id is NULL';
		}
		// TODO MORE
		if ($this->dept_id && $this->dept_id == $this->dept_parent) {
		 	return "cannot make myself my own parent (" . $this->dept_id . "=" . $this->dept_parent . ")";
		}
		return NULL; // object is ok
	}

	function store($updateNulls = false) {
		$msg = $this->check();
		if ($msg) {
			return get_class($this)."::store-check failed - $msg";
		}
		if ($this->dept_id) {
			$ret = db_updateObject('departments', $this, 'dept_id', false);
		} else {
			$ret = db_insertObject('departments', $this, 'dept_id');
		}
		if (!$ret) {
			return get_class($this)."::store failed <br />" . db_error();
		} else {
			return NULL;
		}
	}

	function delete($oid = NULL, $history_desc = '', $history_proj = 0) {
		$q  = new DBQuery;
		$q->addTable('departments','dep');
		$q->addQuery('dep.*');
		$q->addWhere('dep.dept_parent = '.$this->dept_id);
		$res = $q->exec();

		if (db_num_rows($res)) {
			$q->clear();
			return "deptWithSub";
		}
		$q->clear();
		$q->addTable('projects','p');
		$q->addQuery('p.*');
		$q->addWhere('p.project_department = '.$this->dept_id);
		$res = $q->exec();

		if (db_num_rows($res)) {
			$q->clear();
			return "deptWithProject";
		}
		// $sql = "DELETE FROM departments WHERE dept_id = $this->dept_id";
		$q->clear();
		$q->addQuery('*');
		$q->setDelete('departments');
		$q->addWhere('dept_id = '.$this->dept_id);
		if (!$q->exec()) {
			$result = db_error();
		} else {
			$result = NULL;
		}
		$q->clear();
		return $result;
	}
}

//writes out a single <option> element for display of departments
function showchilddept( &$a, $level=0 ) {
	global $AppUI, $cBuffer, $department;
	$s = ('<option value="' . $a['dept_id'] . '"' 
	      . ((isset($department) && $department == $a['dept_id']) ? 'selected="selected"' : '') 
	      . '>');
	      
	for ($y=0; $y < $level; $y++) {
		$s .= (($y+1 == $level) ? '&nbsp;&nbsp;' : '' );
	}
	
	$s .= '&nbsp;&nbsp;' . $AppUI->___($a['dept_name']) . "</option>\n";
	$cBuffer .= $s;

}

//recursive function to display children departments.
function findchilddept(&$tarr, $parent, $level=0) {
	$level = $level+1;
	$n = count($tarr);
	for ($x=0; $x < $n; $x++) {
		if ($tarr[$x]['dept_parent'] == $parent 
		    && $tarr[$x]['dept_parent'] != $tarr[$x]['dept_id']) {
				
			showchilddept($tarr[$x], $level);
			findchilddept($tarr, $tarr[$x]['dept_id'], $level);
		}
	}
}

/* Receiving a hash list */
function addDeptId($dataset, $parent) {

	if ( !is_array( $dataset ) or count( $dataset ) == 0 ) return array($parent);
		
	$dept_ids = array($parent);
	foreach ($dataset as $data) {
		if ($data['dept_parent'] == $parent) {
			$dept_ids[] = $data['dept_id'];
			addDeptId($dataset, $data['dept_id']);
		}
	}
	return $dept_ids;
}

?>
