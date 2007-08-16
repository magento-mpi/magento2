DROP TABLE IF EXISTS `core_translate`;
CREATE TABLE `core_translate` (                                                                                                           
                  `key_id` int(10) unsigned NOT NULL auto_increment,                                                                                      
                  `string` varchar(255) NOT NULL default '',                                                                                              
                  `store_id` smallint(5) unsigned NOT NULL default '0',                                                                                   
                  `translate` varchar(255) NOT NULL default '',                                                                                           
                  PRIMARY KEY  (`key_id`),                                                                                                                
                  UNIQUE KEY `IDX_CODE` (`string`,`store_id`),                                                                                            
                  KEY `FK_CORE_TRANSLATE_STORE` (`store_id`),                                                                                             
                  CONSTRAINT `FK_CORE_TRANSLATE_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE  
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Translation data';