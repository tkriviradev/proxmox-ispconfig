<?php
/*
Copyright (c) 2016, Gody - ORM
All rights reserved.
*/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';

include './lib/pve2_api.class.php'; // maybe we can move this class to another location? 

/******************************************
* Begin Form configuration
******************************************/

$list_def_file = "list/proxmox_vm.list.php";

/******************************************
* End Form configuration
******************************************/

//* Check permissions for module
$app->auth->check_module_permissions('proxmox');

$prox_id = $app->functions->intval($_REQUEST['id']);

if($_POST && !empty($prox_id))
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
	
	if(isset($vm_id) && isset($vm_containers))
	{
		$pve2 = new PVE2_API($conf["pve_link"], $conf["pve_username"], $conf["pve_realm"], $conf["pve_password"]);
	
		if ($pve2) {
			if ($pve2->login()) {
					
				$vm_temp = $pve2->get("/cluster/resources");
				$key = array_search($vm_id, array_column( $vm_temp , 'vmid'));
				$vm_pvesvr = $vm_temp[$key]['node'];

				// Get VM configurations
				$vm_config = $pve2->get("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/config");
				$vm_interface = $vm_config[$_REQUEST['interface']];
				
				$interface_settings = explode(',', $vm_config[$_REQUEST['interface']]	);	
				
				if( ($key = array_search('link_down=1',$interface_settings)) !== false ) 
				{
					unset($interface_settings[$key]);
				}
				else
				{
					$interface_settings[] = 'link_down=1';
				}
				
				/*
				 * Post new network vm configuration
				 * For change VM configurations your Proxmox user need VM.Config.Network right (PVEVMAdmin vs PVEVMUser) 
				 */ 
				$vm_interface = array($_REQUEST['interface'] => implode(",", $interface_settings));
				$vm_config = $pve2->post("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/config", $vm_interface );
				
				// TODO : return to vm information with succes or error message
				// echo (!empty( $vm_config ) ? 'Ok' : 'NOK' );

			} else {
				echo "Login to Proxmox Host failed.\n";
				exit;
			}
		} else {
			echo "Could not create PVE2_API object.\n";
			exit;
		}
	}
}

$app->uses('listform_actions');
$app->listform_actions->onLoad();
?>
