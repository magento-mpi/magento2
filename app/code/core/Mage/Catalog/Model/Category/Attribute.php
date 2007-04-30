<?php
/**
 * Category attribute
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Category_Attribute extends Varien_Data_Object
{
    public function __construct($data = array()) 
    {
        parent::__construct($data);
    }
    
    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('catalog_resource', 'category_attribute');
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
        return Mage::registry('resources')->getTableName('catalog_resource', 'category_attribute_value');
    }
    
    public function getTableAlias()
    {
        return $this->getAttributeCode();
    }
    
    public function getSelectTable()
    {
        return $this->getTableName() . ' as ' . $this->getTableAlias();
    }
    
    public function getTableColumns()
    {
        $columns = array(
            new Zend_Db_Expr($this->getTableAlias().".attribute_value AS " . $this->getCode()),
        );
        return $columns;
    }
    
    public function getSaver()
    {
        $saverName = $this->getDataSaver();
        if (empty($saverName)) {
            $saverName = 'default';
        }
        
        if ($config = Mage::getConfig()->getNode('global/category_attributes/savers/'.$saverName)) {
            $module = (string) $config->module;
            $model  = (string) $config->model;
            $model = Mage::getModel($module, $model)->setAttribute($this);
            // TODO: check instanceof
            return $model;
        }
        
        throw new Exception('Attribute saver "'.$saverName.'" not found');
    }
}