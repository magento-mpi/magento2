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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category filter Mysql4 resource
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Mysql4_Category_Filter
{
    protected $_filterTable;
    protected $_filterValueTable;
    protected $_write;
    protected $_read;
    
    public function __construct() 
    {
        $this->_filterTable = Mage::getSingleton('core/resource')->getTableName('catalog/category_filter');
        $this->_filterValueTable = Mage::getSingleton('core/resource')->getTableName('catalog/category_filter_value');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('catalog_write');
        $this->_read  = Mage::getSingleton('core/resource')->getConnection('catalog_read');
    }
    
    public function load($filterId)
    {
        
    }
    
    public function getValues($filterId)
    {
        $sql = "SELECT * FROM $this->_filterValueTable WHERE filter_id=:id ORDER BY position";
        $arr = $this->_read->fetchAll($sql, array('id'=>$filterId));
        return $arr;
    }
}