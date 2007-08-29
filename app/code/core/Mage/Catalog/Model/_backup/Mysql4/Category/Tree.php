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
 * Category tree model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Mysql4_Category_Tree extends Varien_Data_Tree_Db 
{
    protected $_attributeTable;
    protected $_attributeValueTable;
    protected $_attributes;
    protected $_storeId;
    protected $_joinedAttributes = array();
    
    public function __construct()
    {
        parent::__construct(
            Mage::getSingleton('core/resource')->getConnection('catalog_read'),
            Mage::getSingleton('core/resource')->getTableName('catalog/category'),
            array(
                Varien_Data_Tree_Db::ID_FIELD       => 'category_id',
                Varien_Data_Tree_Db::PARENT_FIELD   => 'pid',
                Varien_Data_Tree_Db::LEVEL_FIELD    => 'level',
                Varien_Data_Tree_Db::ORDER_FIELD    => 'order'
            )
        );
        
        $this->_attributes          = Mage::getResourceModel('catalog/category_attribute_collection')->load();
        $this->_attributeTable      = Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute');
        $this->_attributeValueTable = Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute_value');
    }
    
    public function joinAttribute($attributeCode)
    {
        $attribute = $this->_attributes->getItemByCode($attributeCode);
        if ($attribute->isEmpty()) {
            throw Mage::exception('Mage_Catalog', 'Category attribute with code "'.$attributeCode .' do not exist');
        }
        
        if ($this->_isAttributeJoined($attribute)) {
            return $this;
        }
        
        $condition = $attribute->getTableAlias().".category_id=$this->_table.category_id AND ".
                     $attribute->getTableAlias().'.attribute_id='.$attribute->getId().' AND ' .
                     $attribute->getTableAlias().'.store_id='.(int) $this->getStoreId();
        
        $this->_select->join($attribute->getSelectTable(), $condition, $attribute->getTableColumns());
        return $this;
    }
    
    protected function _isAttributeJoined(Mage_Catalog_Model_Category_Attribute $attribute)
    {
        return isset($this->_joinedAttributes[$attribute->getCode()]);
    }
    
    public function getStoreId()
    {
        if ($this->_storeId) {
            return $this->_storeId;
        }
        else {
            return Mage::getSingleton('core/store')->getId();
        }
    }
    
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }
}
