<?php
/*
Copyright (c) 2016, Gody - ORM
All rights reserved.
*/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';

/******************************************
* Begin Form configuration
******************************************/

$list_def_file = "list/proxmox_cn.list.php";

/******************************************
* End Form configuration
******************************************/


//* Check permissions for module
$app->auth->check_module_permissions('proxmox');

$app->uses('listform_actions');

$app->listform_actions->onLoad();

?>
