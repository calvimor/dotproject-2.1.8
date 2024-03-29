<?php
	// $Id: queuescanner.php 6192 2013-01-05 12:31:23Z ajdonnison $

/*

Copyright (c) 2003-2005 The dotProject Development Team <core-developers@dotproject.net>

    This file is part of dotProject.

    dotProject is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    dotProject is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with dotProject; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

The full text of the GPL is in the COPYING file.
*/

	// Function to scan the event queue and execute any functions required.

	require_once 'base.php';
	require_once DP_BASE_DIR.'/includes/config.php';
	require_once DP_BASE_DIR.'/includes/main_functions.php';
	require_once DP_BASE_DIR.'/includes/db_connect.php';
	require_once DP_BASE_DIR.'/classes/ui.class.php';
	require_once DP_BASE_DIR.'/classes/event_queue.class.php';
	require_once DP_BASE_DIR.'/classes/query.class.php';

	$AppUI = new CAppUI;
	$AppUI->setUserLocale();
	$perms =& $AppUI->acl();

	echo "Scanning Queue ...\n";
	$queue = EventQueue::getInstance();
	# Determine if we are called from the command line or from a web page,
	# In either case we may have an argument telling us if we are scanning
	# the batch or the immediate queue.  If no argument, scan everything.
	$batch = null;
	if (isset($_REQUEST['batch'])) {
		$batch = strtolower($_REQUEST['batch']);
	} else if (isset($argv) && !empty($argv[1])) {
		$batch = strtolower($argv[1]);
	}
	if (!empty($batch)) {
		if ( is_numeric($batch)) {
			$batch = intval($batch);
			if ($batch[0] == 'y' || $batch[0] == 't') {
				$batch = 1;
			} else {
				$batch = 0;
			}
		}
		if ($batch) {
			$queue->scanBatched();
		} else {
			$queue->scanImmediate();
		}
	} else {
		$queue->scan();
	}
	echo 'Done, '.$queue->eventCount().' events processed'."\n";
