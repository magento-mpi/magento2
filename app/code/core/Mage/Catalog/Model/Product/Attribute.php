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
    
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    public function delete()
    {
        $this->getResource()->delete($this->getId());
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
        if ($type && $config = Mage::getConfig()->getGlobalCollection('product_attributes/types', $type)) {
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
    
    public function getMultipleOrder()
    {
        if ('decimal' == $this->getDataType()) {
            $order = new Zend_Db_Expr($this->getCode() . '_qty ASC');
        }
        else {
            $order = new Zend_Db_Expr($this->getCode() . ' ASC');
        }
        return $order;
    }
    
    public function getOptions()
    {
        $collection = Mage::getModel('catalog_resource', 'product_attribute_option_collection')
            ->addAttributeFilter($this->getId())
            ->load();
            
        return $collection;
    }
    
    /**
     * Retrieve attribute save object
     *
     * @return 
     */
    public function getSaver()
    {
        $saverName = $this->getDataSaver();
        if (empty($saverName)) {
            $saverName = 'default';
        }
        
        if ($config = Mage::getConfig()->getGlobalCollection('product_attributes/savers', $saverName)) {
            $module = (string) $config->module;
            $model  = (string) $config->model;
            $model = Mage::getModel($module, $model)->setAttribute($this);
            // TODO: check instanceof
            return $model;
        }
        
        throw new Exception('Attribute saver "'.$saverName.'" not found');
    }
    
    public function getSource()
    {
        $sourceName = $this->getDataSource();
        if (empty($sourceName)) {
            return false;
        }
        
        if ($config = Mage::getConfig()->getGlobalCollection('product_attributes/sources', $sourceName)) {
            $module = (string) $config->module;
            $model  = (string) $config->model;
            $model = Mage::getModel($module, $model)->setAttribute($this);
            // TODO: check instanceof
            return $model;
        }
        
        throw new Exception('Attribute source "'.$saverName.'" not found');
    }
    
    public function getAllowInput()
    {
        $config = (array) Mage::getConfig()->getGlobalCollection('product_attributes/inputs');
        $arr = array();
        foreach ($config as $input=>$inputInfo) {
            $arr[] = array(
                'value' => $input,
                'label' => $input
            );
        }
        return $arr;
    }

    public function getAllowSaver()
    {
        $config = (array) Mage::getConfig()->getGlobalCollection('product_attributes/savers');
        $arr = array();
        foreach ($config as $saver=>$saverInfo) {
            $arr[] = array(
                'value' => $saver,
                'label' => (string) $saverInfo->name
            );
        }
        return $arr;
    }

    public function getAllowSource()
    {
        $config = (array) Mage::getConfig()->getGlobalCollection('product_attributes/sources');
        $arr = array();
        foreach ($config as $source=>$sourceInfo) {
            $arr[] = array(
                'value' => $source,
                'label' => (string) $sourceInfo->name
            );
        }
        return $arr;
    }
}