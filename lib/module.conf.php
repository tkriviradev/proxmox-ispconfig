<?php

$userid=$app->auth->get_user_id();

$module['name']   = 'proxmox';
$module['title']   = 'PVE';
$module['template']  = 'module.tpl.htm';
$module['startpage']  = 'proxmox/proxmox_vm_list.php';
$module['tab_width']    = '60';
$module['order']    = '90';


//**** Templates menu
$items = array();

if($_SESSION["s"]["user"]["typ"] == 'admin') {
	$items[] = array( 'title'  => 'Assignation',
		'target'  => 'content',
		'link' => 'proxmox/proxmox_vm_edit.php',
		'html_id' => 'proxmox_vm_list');
		
	$items[] = array( 'title'  => 'Tasks logs',
		'target'  => 'content',
		'link' => 'proxmox/proxmox_vm_logs.php',
		'html_id' => 'proxmox_vm_logs');
}	



$items[] = array( 'title'  => 'Virtuals Instances',
	'target'  => 'content',
	'link' => 'proxmox/proxmox_vm_list.php',
	'html_id' => 'proxmox_vm_list');


$items[] = array( 'title'  => 'Containers',
	'target'  => 'content',
	'link' => 'proxmox/proxmox_cn_list.php',
	'html_id' => 'proxmox_cn_list');

if(count($items))
{
	$module['nav'][] = array( 'title' => 'VPS',
		'open'  => 1,
		'items' => $items);
}



?>
