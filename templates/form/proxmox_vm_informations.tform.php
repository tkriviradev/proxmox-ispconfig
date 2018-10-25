<?php

/*
	Form Definition

	Tabledefinition

	Datatypes:
	- INTEGER (Forces the input to Int)
	- DOUBLE
	- CURRENCY (Formats the values to currency notation)
	- VARCHAR (no format check, maxlength: 255)
	- TEXT (no format check)
	- DATE (Dateformat, automatic conversion to timestamps)

	Formtype:
	- TEXT (Textfield)
	- TEXTAREA (Textarea)
	- PASSWORD (Password textfield, input is not shown when edited)
	- SELECT (Select option field)
	- RADIO
	- CHECKBOX
	- CHECKBOXARRAY
	- FILE

	VALUE:
	- Wert oder Array

	Hint:
	The ID field of the database table is not part of the datafield definition.
	The ID field must be always auto incement (int or bigint).

	Search:
	- searchable = 1 or searchable = 2 include the field in the search
	- searchable = 1: this field will be the title of the search result
	- searchable = 2: this field will be included in the description of the search result


*/

$form["title"]    = "Informations";
$form["description"]  = "";
$form["name"]    = "proxmox_vm";
$form["action"]   = "proxmox_vm_informations.php";
$form["db_table"]  = "proxmox_vm";
$form["db_table_idx"] = "id";
$form["db_history"]  = "yes";
$form["tab_default"] = "informations";
$form["list_default"] = "proxmox_vm_list.php";
$form["auth"]   = 'yes'; // yes / no

$form["auth_preset"]["userid"]  = 0; // 0 = id of the user, > 0 id must match with id of current user
$form["auth_preset"]["groupid"] = 0; // 0 = default groupid of the user, > 0 id must match with groupid of current user
$form["auth_preset"]["perm_user"] = 'riud'; //r = read, i = insert, u = update, d = delete
$form["auth_preset"]["perm_group"] = 'riud'; //r = read, i = insert, u = update, d = delete
$form["auth_preset"]["perm_other"] = ''; //r = read, i = insert, u = update, d = delete

$form["tabs"]['informations'] = array (
	'title'  => "Informations", // Need to translate with variable
	'width'  => 100,
	'template'  => "templates/proxmox_vm_informations.htm",
	'readonly' => true,
	'fields'  => array (
		//#################################
		// Begin Datatable fields
		//#################################
		'id' => array (
			'datatype' => 'INTEGER',
			'formtype' => 'TEXT',
			'default' => '',
			'value'  => '',
			'separator' => '',
			'width'  => '30',
			'maxlength' => '255',
			'rows'  => '',
			'cols'  => '',
			'searchable' => 2
		),
		//#################################
		// ENDE Datatable fields
		//#################################
	)
);

$form["tabs"]['graphics'] = array (
	'title'  => "Graphics", // Need to translate with variable
	'width'  => 100,
	'template'  => "templates/proxmox_vm_graphiques.htm",
	'readonly' => true,
);

$form["tabs"]['backupreplication'] = array (
       'title'  => "Backup&Replication", // Need to translate with variable
       'width'  => 100,
       'template'  => "templates/proxmox_vm_backup_replication.htm",
       'readonly' => true,
);

$form["tabs"]['instancelogs'] = array (
       'title'  => "Logs", // Need to translate with variable
       'width'  => 100,
       'template'  => "templates/proxmox_vm_logs.htm",
       'readonly' => true,
);

$form["tabs"]['snapshotes'] = array (
       'title'  => "Snapshotes", // Need to translate with variable
       'width'  => 100,
       'template'  => "templates/proxmox_vm_snapshotes.htm",
       'readonly' => true,
);

?>
