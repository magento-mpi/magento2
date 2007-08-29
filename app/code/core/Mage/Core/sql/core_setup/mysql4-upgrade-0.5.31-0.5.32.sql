/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
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