<?php
/**
 * Customer address collection
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Address_Collection
{
    protected $_model = null;
    
    public function __construct() 
    {
        $this->_model = Mage::getResourceModel('customer', 'address_collection');
    }
    
    public function getModel()
    {
        return $this->_model;
    }
    
    public function load()
    {
        $this->_model->load();
        return $this;
    }
    
    public function loadByCustomer($customerId)
    {
        $this->_model->filterByCustomerId($customerId);
        $this->load();
        return $this;
    }
    
    public function getAll()
    {
        return $this->_model->getItems();
    }
    
    /**
     * Enter description here...
     *
     * @param string $priority primary|alternative
     */
    public function getPrimaryTypes($primary=true)
    {
        $items = $this->_model->getItems();
        $result = array();
        foreach ($items as $item) {
            $primaryTypes = $item->getPrimaryTypes();
            if ($primary && !empty($primaryTypes) || !$primary && empty($primaryTypes)) {
                $result[] = $item;
            }
        }
        return $result;
    }
}