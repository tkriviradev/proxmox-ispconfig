<?php
/*
Copyright (c) 2016, Gody - ORM
All rights reserved.
*/

/******************************************
* Begin Form configuration
******************************************/

$list_def_file = "list/proxmox_vm.list.php";

/******************************************
* End Form configuration
******************************************/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';

include './lib/pve2_api.class.php';

//* Check permissions for module
$app->auth->check_module_permissions('proxmox');

$prox_id = $app->functions->intval($_REQUEST['id']);
$action = ( isset($_REQUEST['r']) && !empty($_REQUEST['r']) ?   $_REQUEST['r'] : false );
$vm_pvesvr = $_REQUEST['vm_pvesvr'];

if(isset($prox_id) && !empty($prox_id))
{	
	// Verify assignation VM

	if($_SESSION["s"]["user"]["typ"] != 'admin' && !$app->auth->has_clients($_SESSION['s']['user']['userid'])) 
	{
		$client_group_id = $app->functions->intval($_SESSION["s"]["user"]["default_group"]);
		$vm = $app->db->queryOneRecord("SELECT vm_containers, vm_id FROM proxmox_vm WHERE id = $prox_id AND sys_groupid = $client_group_id");	
	}
	else
	{
		$vm = $app->db->queryOneRecord("SELECT vm_containers, vm_id FROM proxmox_vm WHERE id = $prox_id");
	}
	

	$vm_containers = $vm['vm_containers'];
	$vm_id = $app->functions->intval($vm['vm_id']);

	if(isset($vm_pvesvr) &&  isset($vm_id) && isset($vm_containers))
	{
		$pve2 = new PVE2_API($conf["pve_link"], $conf["pve_username"], $conf["pve_realm"], $conf["pve_password"]);
		
		if ($pve2) {
			if ($pve2->login()) {
				
				switch ($action)
				{
					case 'start':
						$vm_status = ( $pve2->post("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/status/start", array()) ? true : false );
						break;
					case 'snapshot':
						$vm_status = ( $pve2->post("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/snapshot", array()) ? true : false );
						break;
					case 'shutdown':
						$vm_status = ( $pve2->post("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/status/shutdown", array())  ? true : false );
						break;
					case 'reset':
						$vm_status = ( $pve2->post("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/status/reset", array())  ? true : false );
						break;
					case 'kill':
						$vm_status = ( $pve2->post("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/status/stop", array())  ? true : false );
						break;
				}
			} else {
				$vm_status = $app->error($app->tform->wordbook["vm_err_login"]);
				exit;
			}
		} else {
			$vm_status = $app->error($app->tform->wordbook["vm_err_api_obj"]);
			exit;
		}
	}
}



$app->uses('listform_actions');
$app->listform_actions->onLoad();

?>
