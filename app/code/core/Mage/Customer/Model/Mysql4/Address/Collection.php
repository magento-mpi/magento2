<?php
/**
 * Customer address collection
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Address_Collection extends Varien_Data_Collection_Db
{
    static protected $_addressTable = null;

    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('customer_read'));
        self::$_addressTable = Mage::registry('resources')->getTableName('customer', 'address');
        $this->_sqlSelect->from(self::$_addressTable);
        $this->setItemObjectClass(Mage::getConfig()->getResourceModelClassName('customer', 'address'));
    }
    
    public function load($printQuery = false, $logQuery = false)
    {
        // try load addresses data
        if (!parent::load($printQuery, $logQuery)) {
            // if empty return
            return false;
        }
        
        // collect address ids
        $addressIds = array();
        foreach ($this->_items as $item) {
            $addressIds[] = $item->getAddressId();
        }
        
        // fetch all types for collection addresses
        $condition = $this->getConnection()->quoteInto("address_id in (?)", $addressIds);
        $typesArr = Mage::getResourceModel('customer', 'address')->getTypesByCondition($condition);
        
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
        }
    }
    
    public function loadByCustomerId($customerId)
    {
        $this->addFilter('customer_id', (int)$customerId, 'and');
        $this->load();
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @param string $priority primary|alternative
     */
    public function getPrimaryAddresses($primary=true)
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