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
 * Category attributes collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Mysql4_Category_Attribute_Collection extends Varien_Data_Collection_Db 
{
    protected $_attributeTable;
    protected $_attributeInSetTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('catalog_read'));
        
        $this->_attributeTable     = Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute');
        $this->_attributeInSetTable= Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute_in_set');
        
        $this->_sqlSelect->from($this->_attributeTable);
        $this->_sqlSelect->join($this->_attributeInSetTable, "$this->_attributeTable.attribute_id=$this->_attributeInSetTable.attribute_id");
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog/category_attribute'));
    }
    
    public function addSetFilter($attributeSetId)
    {
        $this->addFilter("$this->_attributeInSetTable.attribute_set_id", $attributeSetId);
        return $this;
    }

    public function getItemByCode($attributeCode)
    {
        foreach ($this as $attribute) {
            if ($attribute->getCode()==$attributeCode) {
                return $attribute;
            }
        }
        return new $this->_itemObjectClass();
    }

    public function setPositionOrder()
    {
        $this->setOrder($this->_attributeInSetTable.'.position', 'asc');
        return $this;
    }
}
