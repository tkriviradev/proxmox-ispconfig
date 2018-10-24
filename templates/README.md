# ISPConfig Module

## Application informations 
This application is an Add-ons for ISPConfig used for VPS Management (Proxmox)

## Requirements
- ISPConfig 3.1
- Mysql database (same as ISPconfig)
- Proxmox Virtual Environement
- Firewall permission

## Features 
- Manage your VPS :
 - Start
 - Shutdown
 - Reset
 - Hard Stop (unplug the power)
 - Change network states (enable / disable)
 
- Display :
 - Power status
 - Uptime
 - Load average
 - Installed Memory
 - Commsumed Memory
 - Installed CPU
 - Disk Space (total)
 - Graphics (Memory, CPU, Networks, DIsk IO)
 - Network interfaces (Mac address and state)

- Admin display :
 - Per client assignation
 - Task history (need to implement link to ispconfig user and pve user for non admin version)
 - Network Interfaces (Vlan, Rate)

## Screenshots
See misc folder For some screenshot

## How To implement this module
- Create Proxmox user for ISPConfig communication with PVEVMUser right
 - Assign VMs to this user
- Deploy the module into your ISPConfig setup -> /usr/local/ispconfig/interface/web
- Configure the module with your Proxmox user -> /usr/local/ispconfig/interface/lib/config.inc.local.php
 - $conf["pve_username"] 	= 'username';
 - $conf["pve_password"] 	= 'password';
 - $conf["pve_link"] 		= 'hostname / ip of your cluster head';
 - $conf["pve_realm"] 		= 'realm';
 - Please do not use your Proxmox root user !
- Enable the module -> /usr/local/ispconfig/interface/lib/config.inc.local.php
 - Add "proxmox" to the variable $conf['modules_available'] 
 - Dont forget to activate it for your Ispconfig user 
- Create MySQL table for this module -> misc/vm_proxmox.sql
 - If you are on multi-servers setup you have to do this on each server


## ToDo
- Admin function : 
 - Snapshot (add / remove)

- Display: 
 - Top menu icon.
 - NoVNC console integration (stuck for the moment ... :( )

# License
Copyright (c) 2017, Oricom Internet
