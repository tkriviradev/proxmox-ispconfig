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

$prox_id = $app->functions->intval($_REQUEST['vm']);
$condition = $_REQUEST['periode'];
$vm_pvesvr = $_REQUEST['vm_pvesvr'];

if($_POST)
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
				switch  ($condition)
				{
					case 'ah' :
						$params = "&timeframe=hour&cf=AVERAGE";
						break;
					case 'mh' :
						$params = "&timeframe=hour&cf=MAX";
						break;
					case 'ad' :
						$params = "&timeframe=day&cf=AVERAGE";
						break;
					case 'md' :
						$params = "&timeframe=day&cf=MAX";
						break;
					case 'aw' :
						$params = "&timeframe=week&cf=AVERAGE";
						break;
					case 'mw' :
						$params = "&timeframe=week&cf=MAX";
						break;
					case 'am' :
						$params = "&timeframe=month&cf=AVERAGE";
						break;
					case 'mm' :
						$params = "&timeframe=month&cf=MAX";
						break;
					case 'ay' :
						$params = "&timeframe=year&cf=AVERAGE";
						break;
					case 'my' :
						$params = "&timeframe=year&cf=MAX";
						break;
				}
			
				if( isset($params) )
				{
					$graphs['cpu'] = $pve2->get("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/rrd?ds=cpu{$params}");
					$graphs['memory'] = $pve2->get("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/rrd?ds=mem,maxmem{$params}");
					$graphs['network'] = $pve2->get("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/rrd?ds=netin,netout{$params}");
					$graphs['disk'] = $pve2->get("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/rrd?ds=diskread,diskwrite{$params}");
				
					$retour['CPU'] = base64_encode(utf8_decode($graphs['cpu']['image']));
					$retour['MEM'] = base64_encode(utf8_decode($graphs['memory']['image']));
					$retour['NET'] = base64_encode(utf8_decode($graphs['network']['image']));
					$retour['HDD'] = base64_encode(utf8_decode($graphs['disk']['image']));
				}
		
				echo json_encode($retour);
			
			
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

?>
