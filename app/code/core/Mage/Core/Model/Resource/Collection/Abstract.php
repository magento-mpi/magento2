<?php

class Mage_Core_Model_Resource_Collection_Abstract extends Varien_Data_Collection_Db 
{
    /**
     * Resource instance
     *
     * @var Mage_Core_Model_Resource_Abstract
     */
    protected $_resource;
    
    /**
     * Collection constructor
     *
     * @param Mage_Core_Model_Resource_Abstract $resource
     */
    public function __construct($resource=null)
    {
        $this->_construct();
        
        $this->_resource = $resource;
        
        parent::__construct($this->getResource()->getConnection('read'));        
        
        $this->getSelect->from($this->getResource()->getMainTable());
    }
    
    /**
     * Initialization here
     *
     */
    protected function _construct()
    {
        
    }
    
    /**
     * Get Zend_Db_Select instance
     *
     * @return unknown
     */
    public function getSelect()
    {
        return $this->_sqlSelect;
    }
    
    /**
     * Set model name for collection items
     *
     * @param string $object
     * @return Mage_Core_Model_Resource_Collection_Abstract
     */
    public function setModel($object)
    {
        if (is_string($object)) {
            $this->setItemObjectClass(Mage::getConfig()->getModelClassName($object));
        }
        return $this;
    }
    
    /**
     * Get model instance
     *
     * @param array $args
     * @return Varien_Object
     */
    public function getModelName($args=array())
    {
        return $this->_itemObjectClass;
    }
        
    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function getResource()
    {
        if (empty($this->_resource)) {
            $this->_resource = Mage::getResourceModel($this->_itemObjectClass);
        }
        return $this->_resource;
    }
}