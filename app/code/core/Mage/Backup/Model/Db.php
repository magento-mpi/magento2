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
 * @package    Mage_Backup
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Database backup model
 *
 * @category   Mage
 * @package    Mage_Backup
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Backup_Model_Db
{
    public function __construct() 
    {
        
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('backup/db');
    }
    
    public function getTables()
    {
        return $this->getResource()->getTables();
    }
    
    public function getTableCreateScript($tableName, $addDropIfExists=false)
    {
        return $this->getResource()->getTableCreateScript($tableName, $addDropIfExists);
    }
    
    public function getTableDataDump($tableName)
    {
        return $this->getResource()->getTableDataDump($tableName);
    }
    
    public function getHeader()
    {
        return $this->getResource()->getHeader();
    }
    
    public function getFooter()
    {
        return $this->getResource()->getFooter();
    }
    
    public function renderSql()
    {
        $sql = $this->getHeader();
        
        $tables = $this->getTables();
        foreach ($tables as $tableName) {
        	$sql.= $this->getTableCreateScript($tableName, true);
        	$sql.= $this->getTableDataDump($tableName);
        }
        
        $sql.= $this->getFooter();
        return $sql;
    }
}
