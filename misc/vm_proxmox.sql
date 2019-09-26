--
-- Table structure for table `proxmox_vm`
--

DROP TABLE IF EXISTS `proxmox_vm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proxmox_vm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sys_userid` int(11) NOT NULL DEFAULT '0',
  `sys_groupid` int(11) NOT NULL DEFAULT '0',
  `sys_perm_user` varchar(5) DEFAULT NULL,
  `sys_perm_group` varchar(5) DEFAULT NULL,
  `sys_perm_other` varchar(5) DEFAULT NULL,
  `server_id` int(11) NOT NULL DEFAULT '1',
  `vm_containers` varchar(45) NOT NULL,
  `vm_name` varchar(100) NOT NULL,
  `vm_id` int(11) NOT NULL,
  `vm_description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prox_id_UNIQUE` (`id`)
) ENGINE=MYISAM   DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
