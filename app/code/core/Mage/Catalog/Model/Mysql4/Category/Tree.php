<?php

/**
 * Category tree model
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Category_Tree extends Varien_Data_Tree_Db 
{
    protected $_attributeTable;
    protected $_attributeValueTable;
    protected $_attributes;
    protected $_websiteId;
    protected $_joinedAttributes = array();
    
    public function __construct()
    {
        parent::__construct(
            Mage::registry('resources')->getConnection('catalog_read'),
            Mage::registry('resources')->getTableName('catalog_resource', 'category'),
            array(
                Varien_Data_Tree_Db::ID_FIELD       => 'category_id',
                Varien_Data_Tree_Db::PARENT_FIELD   => 'pid',
                Varien_Data_Tree_Db::LEVEL_FIELD    => 'level',
                Varien_Data_Tree_Db::ORDER_FIELD    => 'order'
            )
        );
        
        $this->_attributes          = Mage::getModel('catalog_resource/category_attribute_collection')->load();
        $this->_attributeTable      = Mage::registry('resources')->getTableName('catalog_resource', 'category_attribute');
        $this->_attributeValueTable = Mage::registry('resources')->getTableName('catalog_resource', 'category_attribute_value');
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
                     $attribute->getTableAlias().'.website_id='.(int) $this->getWebsiteId();
        
        $this->_select->join($attribute->getSelectTable(), $condition, $attribute->getTableColumns());
        return $this;
    }
    
    protected function _isAttributeJoined(Mage_Catalog_Model_Category_Attribute $attribute)
    {
        return isset($this->_joinedAttributes[$attribute->getCode()]);
    }
    
    public function getWebsiteId()
    {
        if ($this->_websiteId) {
            return $this->_websiteId;
        }
        else {
            return Mage::registry('website')->getId();
        }
    }
    
    public function setWebsiteId($websiteId)
    {
        $this->_websiteId = $websiteId;
        return $this;
    }
}
