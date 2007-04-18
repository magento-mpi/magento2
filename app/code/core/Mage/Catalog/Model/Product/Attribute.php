<?php
/**
 * Product attribute
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product_Attribute extends Varien_Data_Object
{
    public function __construct($data = array()) 
    {
        parent::__construct($data);
    }
    
    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('catalog_resource', 'product_attribute');
        }
        return $resource;
    }

    public function load($attributeId)
    {
        $this->setData($this->getResource()->load($attributeId));
        return $this;
    }
    
    public function loadByCode($attributeCode)
    {
        $this->setData($this->getResource()->loadByCode($attributeCode));
        return $this;
    }
    
    public function getId()
    {
        return $this->getAttributeId();
    }
    
    public function getCode()
    {
        return $this->getAttributeCode();
    }
    
    public function isSearchable()
    {
        return $this->getSearchable();
    }

    public function isRequired()
    {
        return $this->getRequired();
    }

    public function isMultiple()
    {
        return $this->getMultiple();
    }
    
    public function getTableName()
    {
        $type = $this->getDataType();
        if ($type && $config = Mage::getConfig()->getGlobalCollection('productAttributeTypes', $type)) {
            return (string) $config->table;
        }
        return false;
    }
    
    public function getTableAlias()
    {
        return $this->getAttributeCode() . '_' . $this->getDataType();
    }
    
    public function getSelectTable()
    {
        return $this->getTableName() . ' as ' . $this->getTableAlias();
    }

    public function getTableColumns()
    {
        if ('decimal' == $this->getDataType()) {
            $columns = array(
                new Zend_Db_Expr($this->getTableAlias().".attribute_value AS " . $this->getCode()),
                new Zend_Db_Expr($this->getTableAlias().".attribute_qty AS " . $this->getCode() . '_qty'),
            );
        }
        else {
            $columns = array(
                new Zend_Db_Expr($this->getTableAlias().".attribute_value AS " . $this->getCode()),
            );
        }
        return $columns;
    }
    
    public function getOptions()
    {
        $collection = Mage::getModel('catalog_resource', 'product_attribute_option_collection')
            ->addAttributeFilter($this->getId())
            ->load();
        return $collection;
    }
}