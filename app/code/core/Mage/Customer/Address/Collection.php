<?php
/**
 * Customer address collection
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Address_Collection extends Mage_Core_Resource_Model_Db_Collection
{
    protected $_addressTable = null;

    public function __construct() 
    {
        parent::__construct(Mage::getResourceModel('customer'));
        
        $this->_addressTable = $this->_dbModel->getTableName('customer', 'address');

        $this->_sqlSelect->from($this->_addressTable);

        $this->setItemObjectClass('Mage_Customer_Address');
    }
    
    public function filterByCustomerId($customerId)
    {
        $this->addFilter('customer_id', (int)$customerId, 'and');
        return $this;
    }
    
    public function filterByCondition($condition)
    {
        $this->addFilter('', $condition, 'string');
        return $this;
    }
    
    public function load($printQuery = false, $logQuery = false)
    {
        // load addresses data
        $data = parent::load($printQuery, $logQuery);
        
        // if empty return
        if (empty($data)) {
            return $data;
        }
        
        // collect address ids
        $addressIds = array();
        foreach ($this->_items as $item) {
            $addressIds[] = $item->getAddressId();
        }
        
        // fetch all types for collection addresses
        $condition = $this->_dbModel->getReadConnection()->quoteInto("address_id in (?)", $addressIds);
        $typesArr = Mage::getResourceModel('customer', 'Address_Type')->getCollection($condition);
        
        // process result
        $types = array('primary_types'=>array(), 'alternative_types'=>array());
        foreach ($typesArr as $type) {
            $priority = $type['is_primary'] ? 'primary_types' : 'alternative_types';
            $types[$type['address_id']][$priority][] = $type['address_type_code'];
        }
       
        // set types to address objects and explode street address
        foreach ($this->_items as $item) {
            if (isset($types[$item->getAddressId()]['primary_types'])) {
                $item->setPrimaryTypes($types[$item->getAddressId()]['primary_types']);
            }
            if (isset($types[$item->getAddressId()]['alternative_types'])) {
                $item->setAlternativeTypes($types[$item->getAddressId()]['alternative_types']);
            }
            
            $item->explodeStreetAddress();
        }
    }
    
    public function loadByCustomer($customerId)
    {
        $this->filterByCustomerId($customerId);
        $this->load();
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @param string $priority primary|alternative
     */
    public function getPrimaryTypes($primary=true)
    {
        $items = $this->getItems();
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