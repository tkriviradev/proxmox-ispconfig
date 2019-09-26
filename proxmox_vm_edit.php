<?php
/*
Copyright (c) 2016, Gody - ORM
All rights reserved.
*/

/******************************************
* Begin Form configuration
******************************************/

$tform_def_file = "form/proxmox_vm.tform.php";

/******************************************
* End Form configuration
******************************************/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';


//* Check permissions for module
$app->auth->check_module_permissions('proxmox');

// Loading classes
$app->uses('tpl,tform,tform_actions');
$app->load('tform_actions');


class page_action extends tform_actions {
	
	function onShowNew() {
		global $app, $conf;

		parent::onShowNew();
	}
	
	function onShowEnd() {
		global $app, $conf;
	
		
		if($_SESSION["s"]["user"]["typ"] == 'admin') {
			// Getting Clients of the user
			$sql = "SELECT sys_group.groupid, sys_group.name, CONCAT(IF(client.company_name != '', CONCAT(client.company_name, ' :: '), ''), client.contact_name, ' (', client.username, IF(client.customer_no != '', CONCAT(', ', client.customer_no), ''), ')') as contactname FROM sys_group, client WHERE sys_group.client_id = client.client_id AND sys_group.client_id > 0 ORDER BY client.company_name, client.contact_name, sys_group.name";

			$clients = $app->db->queryAllRecords($sql);
			$client_select = '';
			if($_SESSION["s"]["user"]["typ"] == 'admin') $client_select .= "<option value='0'></option>";
			//$tmp_data_record = $app->tform->getDataRecord($this->id);
			if(is_array($clients)) {
				foreach( $clients as $client) {
					$selected = @(is_array($this->dataRecord) && ($client["groupid"] == $this->dataRecord['client_group_id'] || $client["groupid"] == $this->dataRecord['sys_groupid']))?'SELECTED':'';
					$client_select .= "<option value='$client[groupid]' $selected>$client[contactname]</option>\r\n";
				}
			}
			$app->tpl->setVar("client_group_id", $client_select);

		} 
		
		parent::onShowEnd();
	}
	
	function onSubmit() {
		global $app, $conf;

		parent::onSubmit();
	}
	
	
	function onAfterInsert() {
		global $app, $conf;
		
		if($_SESSION["s"]["user"]["typ"] == 'admin' && isset($this->dataRecord["client_group_id"])) {
			$client_group_id = $app->functions->intval($this->dataRecord["client_group_id"]);
			$app->db->query("UPDATE proxmox_vm SET sys_groupid = $client_group_id, sys_perm_group = 'ru' WHERE id = ".$this->id);
		}
		if($app->auth->has_clients($_SESSION['s']['user']['userid']) && isset($this->dataRecord["client_group_id"])) {
            $client_group_id = $app->functions->intval($this->dataRecord["client_group_id"]);
            $app->db->query("UPDATE proxmox_vm SET sys_groupid = $client_group_id, sys_perm_group = 'riud' WHERE id = ".$this->id);
        }
		
	}
	
	
	function onBeforeUpdate() {
		global $app, $conf;
	}
	
	function onAfterUpdate() {
		global $app, $conf;
		
		if($_SESSION["s"]["user"]["typ"] == 'admin' && isset($this->dataRecord["client_group_id"])) {
			$client_group_id = $app->functions->intval($this->dataRecord["client_group_id"]);
			$app->db->query("UPDATE proxmox_vm SET sys_groupid = $client_group_id, sys_perm_group = 'ru' WHERE id = ".$this->id);
		}
		if($app->auth->has_clients($_SESSION['s']['user']['userid']) && isset($this->dataRecord["client_group_id"])) {
            $client_group_id = $app->functions->intval($this->dataRecord["client_group_id"]);
            $app->db->query("UPDATE proxmox_vm SET sys_groupid = $client_group_id, sys_perm_group = 'riud' WHERE id = ".$this->id);
        }
		

	}
}

$page = new page_action;
$page->onLoad();

?>
