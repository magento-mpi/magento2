<?php
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


$this->startSetup()
    ->run("
        DROP TABLE IF EXISTS `catalogsearch`;
        DROP TABLE IF EXISTS `catalogsearch_query`;
        CREATE TABLE `catalogsearch_query` (                    
                       `query_id` int(10) unsigned NOT NULL auto_increment,  
                       `query_text` varchar(255) NOT NULL default '',        
                       `num_results` int(10) unsigned NOT NULL default '0',  
                       `popularity` int(10) unsigned NOT NULL default '0',   
                       `redirect` varchar(255) NOT NULL default '',          
                       `synonim_for` varchar(255) NOT NULL default '',       
                       PRIMARY KEY  (`query_id`),                            
                       KEY `search_query` (`query_text`,`popularity`)        
                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ")
    ->endSetup();