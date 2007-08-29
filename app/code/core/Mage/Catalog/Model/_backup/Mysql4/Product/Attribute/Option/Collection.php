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
 * Product attributes set collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Option_Collection extends Varien_Data_Collection_Db
{
    protected $_optionTable;
    protected $_storeId;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('catalog_read'));
        $this->_optionTable    = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_option');
        
        $this->_sqlSelect->from($this->_optionTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog/product_attribute_option'));
    }
    
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
    }
    
    public function getStoreId()
    {
        if ($this->_storeId) {
            return $this->_storeId;
        }
        return Mage::getSingleton('core/store')->getId();
    }
    
    public function loadData($printQuery = false, $logQuery = false)
    {
        $this->addFilter('store_id', $this->getStoreId());
        parent::loadData($printQuery, $logQuery);
        return $this;
    }
    
    public function addAttributeFilter($attributeId)
    {
        $this->addFilter('attribute_id', $attributeId);
        return $this;
    }
    
    public function getArrItemId()
    {
        $arr = array();
        foreach ($this as $option) {
            $arr[] = $option->getId();
        }
        return $arr;
    }
}
