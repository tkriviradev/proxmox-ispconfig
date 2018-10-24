<?php
/*
Copyright (c) 2016, Gody - ORM
All rights reserved.
*/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';

include './lib/pve2_api.class.php';


//* Check permissions for module
$app->auth->check_module_permissions('proxmox');

// Loading classes
$app->uses('tpl');

$template = 'templates/proxmox_vm_logs.htm';

// Loading the template
$app->uses('tpl');
$app->tpl->newTemplate("form.tpl.htm");
$app->tpl->setInclude('content_tpl', $template);
		

$pve2 = new PVE2_API($conf["pve_link"], $conf["pve_username"], $conf["pve_realm"], $conf["pve_password"]);
		
if ($pve2) {
	if ($pve2->login()) {
	
		$app->tpl->setVar("task_title_txt", $app->lng("task_title_txt") );
		$app->tpl->setVar("start_time_txt", $app->lng("start_time_txt") );
		$app->tpl->setVar("end_time_txt", $app->lng("end_time_txt") );
		$app->tpl->setVar("username_txt", $app->lng("username_txt") );
		$app->tpl->setVar("description_txt", $app->lng("description_txt") );
		$app->tpl->setVar("status_txt", $app->lng("status_txt") );

		$tasks_history = $pve2->get("/cluster/tasks");

		$tasks_logs = array();
		foreach($tasks_history as $key => $task){
			$tasks_logs[] = array(
				'starttime' => date('d-m-Y H-i-s', $task['starttime']),
				'endtime' => date('d-m-Y H-i-s', $task['endtime']),
				'vmid' => $task['id'],
				'username' => $task['user'],
				'description' => $app->lng($task['type']),
				'status'  => $task['status'],
			);
		}
		
		$app->tpl->setloop('task_logs', $tasks_logs);
	}
}

$app->tpl_defaults();
$app->tpl->pparse();
?>
