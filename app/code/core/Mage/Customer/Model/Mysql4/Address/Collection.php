<?php
/**
 * Customer address collection
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Mysql4_Address_Collection extends Varien_Data_Collection_Db
{
    static protected $_addressTable;
    static protected $_typeTable;
    static protected $_typeLinkTable;

    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('customer_read'));
        $this->_addressTable    = Mage::getSingleton('core/resource')->getTableName('customer/address');
        $this->_typeTable       = Mage::getSingleton('core/resource')->getTableName('customer/address_type');
        $this->_typeLinkTable   = Mage::getSingleton('core/resource')->getTableName('customer/address_type_link');
        $this->_sqlSelect->from($this->_addressTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('customer/address'));
    }
    
    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        // try load addresses data
        if (count($this->_items) == 0) {
            return $this;
        }
        
        // collect address ids
        $arrId = array();
        foreach ($this->_items as $item) {
            $arrId[] = $item->getAddressId();
        }

        $sql = 'SELECT
                    at.address_id,
                    t.code,
                    t.address_type_id,
                    at.is_primary
                FROM
                    '.$this->_typeLinkTable.' AS at,
                    '.$this->_typeTable.' AS t
                WHERE
                    at.address_type_id=t.address_type_id
                    AND at.address_id IN ('.implode(',', $arrId).')';
        
        $arrTypes = $this->getConnection()->fetchAll($sql);

        foreach ($arrTypes as $type) {
            $types[$type['address_id']][] = $type;
        }
       
        // set types to address objects and explode street address
        foreach ($this->_items as $item) {
            if (isset($types[$item->getAddressId()])) {
                foreach ($types[$item->getAddressId()] as $type) {
                    $item->setType($type['address_type_id'], $type['code'], $type['is_primary']);
                }
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