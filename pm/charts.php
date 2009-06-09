<?php
/* $Id: charts.php,v 1.3 2009/06/08 21:13:04 markp Exp $ */

require_once('path.php');
require_once(BASE.'includes/security.php');

//
// The action handler
//
if (!isset($_REQUEST['action'])) {
	error('Task action handler', 'No action given');
}

// Tasks Action
switch($_REQUEST['action']) {

	case 'cht_pbe':
		include(BASE.'charts/cht_pbe.php');
		break;

	case 'cht_pso':
		include(BASE.'charts/cht_pso.php');
		break;

		//organize tasks
	case 'cht_pbs':
		include(BASE.'charts/cht_pbs.php');
		break;

		//organize tasks
	case 'cht_to':
		include(BASE.'charts/cht_to.php');
		break;

		//organize tasks
	case 'savePos':
		include(BASE.'charts/charts_submit.php');
		break;

		//Error case
	default:
		error('Task action handler', 'Invalid request');
		break;
}

?>