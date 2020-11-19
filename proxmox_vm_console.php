<?php
/*
Copyright (c) 2016, Gody - ORM
All rights reserved.
*/

/******************************************
* Begin Form configuration
******************************************/ 

//$tform_def_file = "form/proxmox_vm_informations.tform.php";

/******************************************
* End Form configuration
******************************************/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';

include './lib/pve2_api.class.php';
$pve2 = new PVE2_API($conf["pve_link"], $conf["pve_username"], $conf["pve_realm"], $conf["pve_password"]);

if ($pve2) {
    if ($pve2->login()) {
        
            
    }  
}
?>