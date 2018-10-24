<?php
/*
Copyright (c) 2016, Gody - ORM
All rights reserved.
*/

/******************************************
* Begin Form configuration
******************************************/

$list_def_file = "list/proxmox_vm.list.php";
$tform_def_file = "form/proxmox_vm.tform.php";

/******************************************
* End Form configuration
******************************************/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';

//* Check permissions for module
$app->auth->check_module_permissions('proxmox');




$app->uses("tform_actions");
$app->tform_actions->onDelete();

?>
