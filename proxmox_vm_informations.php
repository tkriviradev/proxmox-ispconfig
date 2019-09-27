<?php
/*
Copyright (c) 2016, Gody - ORM
All rights reserved.
*/

/******************************************
* Begin Form configuration
******************************************/

$tform_def_file = "form/proxmox_vm_informations.tform.php";

/******************************************
* End Form configuration
******************************************/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';

include './lib/pve2_api.class.php';

//* Check permissions for module
$app->auth->check_module_permissions('proxmox');

// Loading classes
$app->uses('tpl,tform,tform_actions');
$app->load('tform');

class page_action extends tform_actions {
		
	function onShowEnd() {
		global $app, $conf;
	
		$pve2 = new PVE2_API($conf["pve_link"], $conf["pve_username"], $conf["pve_realm"], $conf["pve_password"]);
		
		if ($pve2) {
			if ($pve2->login()) {
				
				$vm_id = $app->functions->intval($this->dataRecord['vm_id']);
				$vm_containers = $this->dataRecord['vm_containers'] ;
				
				$vm_temp = $pve2->get("/cluster/resources");
				$key = array_search($vm_id, array_column( $vm_temp , 'vmid'));
				$vm_pvesvr = $vm_temp[$key]['node'];
		
				$app->tpl->setVar("vm_id", $vm_id);
				$app->tpl->setVar("vm_pvesvr", $vm_pvesvr);
				
				switch($_REQUEST['next_tab'])
				{
					case 'graphics':
						//DO SOMETHING HERE
					break; 

					case 'backupreplication':
						$vm_replication = $pve2->get("/nodes/{$vm_pvesvr}/replication/{$vm_id}-0/status");
			
						if ($vm_replication != false)
						{
							$app->tpl->setVar("vm_rep_target", $vm_replication['target']);
							$app->tpl->setVar("vm_rep_schedule", $vm_replication['schedule']);
							$app->tpl->setVar("vm_rep_duration", round ($vm_replication['duration'],1 ));
							$app->tpl->setVar("vm_rep_fail_count", $vm_replication['fail_count']);
							$app->tpl->setVar("vm_rep_next_sync", $vm_replication['next_sync']);
							$app->tpl->setVar("vm_rep_last_sync", $vm_replication['last_sync']);
							$app->tpl->setVar("vm_rep_comment", $vm_replication['comment']);
						}
						else
						{
							$app->error($app->tform->wordbook["vm_err_assignation"]);
						}
					break;

					case 'informations':
					default:
						$vm_status = $pve2->get("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/status/current");
						$vm_config = $pve2->get("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/config");
					//	$vm_replication = $pve2->get("/nodes/{$vm_pvesvr}/replication/{$vm_id}-0/status"); // TEST
					
						if ($vm_status != false)
						{
							$app->tpl->setVar("vm_name", $vm_status['name']);
							$app->tpl->setVar("vm_status", $vm_status['status']);
							$app->tpl->setVar("vm_parent", $vm_config['parent']);
							$app->tpl->setVar("vm_ostype", $vm_config['ostype']);
							// $app->tpl->setVar("vm_arch", $vm_config['arch']);
							$app->tpl->setVar("vm_nameserver", $vm_config['nameserver']);
							$app->tpl->setVar("vm_uptime", $app->functions->intval($vm_status['uptime'] / 60 / 60 ) );
							$app->tpl->setVar("vm_cpu",  $vm_status['cpus'] );
							$app->tpl->setVar("vm_cpuu",  number_format ($vm_status['cpu'] * 100, 2 ) );
							$app->tpl->setVar("vm_mem", $app->functions->intval($vm_status['mem']/1024/1024 ) );
							$app->tpl->setVar("vm_maxmem", $app->functions->intval($vm_status['maxmem']/1024/1024 ) );
							$app->tpl->setVar("vm_maxhdd", $app->functions->intval($vm_status['maxdisk'] /1024 /1024 / 1024 ) );
							$app->tpl->setVar("vm_netin", $app->functions->intval($vm_status['netin']/1024 /1024 ) );
							$app->tpl->setVar("vm_netout", $app->functions->intval($vm_status['netout'] /1024 /1024 /1024 ) );
					
							$vm_percent_used = ($vm_status['mem'] * 100) / $vm_status['maxmem'] ;
							$app->tpl->setVar("used_percentage", $app->functions->intval($vm_percent_used) );

							$cpu_percent_used = ($vm_status['cpu'] * 100) ;
							$app->tpl->setVar("used_percentage1", $app->functions->intval($cpu_percent_used) );
						}
						else
						{
							$app->error($app->tform->wordbook["vm_err_assignation"]);
						}
					
						case 'get_snapshot':
						$vm_snapshot = $pve2->get("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/snapshot");

						$keys2 = array_keys($vm_snapshot);
						//$snp_temp = preg_grep('/^[1-9]+/', $keys2);
                        
                        foreach ($keys2 as $snp) 
						{
                        	//$arr_snp[$snp]['snpnum'] = $snp;
							// $arr_snp[$snp]['snpnum'] = implode(" ",$vm_snapshot[$snp]);
							// $arr_snp[$snp]['snpnum'] = implode(" ", array_keys($vm_snapshot[$snp]));
							   $arr_snp[$snp]['snpnum'] = $vm_snapshot[$snp]['name'];
							   $arr_snp[$snp]['snpnum1'] = $vm_snapshot[$snp]['description'];
							// $arr_snp[$snp]['snpnum'] = array_combine(array_column($vm_snapshot[$snp], 'name'), $vm_snapshot[$snp]);
                       	}
						case 'get_network':
						$vm_config = $pve2->get("/nodes/{$vm_pvesvr}/{$vm_containers}/{$vm_id}/config");

						$keys = array_keys($vm_config);
						$net_temp = preg_grep('/^net[0-9]+/',$keys);

						$arr_net = array();
						foreach($net_temp as $net)
						{
							$settings_temp = explode(',', $vm_config[$net]);
							$arr_net[$net]['interface'] = $net;
						
							foreach($settings_temp as $settings )
							{
								list($k, $v) = explode('=', $settings);
								
								if (preg_match('/(e1000|vmxnet3|virtio|veth|rtl8139)/i', $k ) )
								{
									$arr_net[$net]['virtio'|'hwaddr'|'ip'] = $v;
								}
								$arr_net[$net]['link_state'] = ($k == 'link_down' && $v == 1 ? 'Yes' : 'No');
								$arr_net[$net][$k] = $v;
							}
						}
						
						$app->tpl->setloop('networks', $arr_net);
                        $app->tpl->setloop('snps', $arr_snp);
						break;
					}
				
				}
			else {
				//print("Login to Proxmox Host failed.\n");
				$app->error($app->tform->wordbook["vm_err_login"]);
				exit;
			}
		}
		else {
			//print("Could not create PVE2_API object.\n");
			$app->error($app->tform->wordbook["vm_err_api_obj"]);
			exit;
		}

		parent::onShowEnd();
	}
}

$page = new page_action;
$page->onLoad();

?>
