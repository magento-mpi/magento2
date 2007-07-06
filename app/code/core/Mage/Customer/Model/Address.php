<?php
/**
 * Customer address
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Address extends Varien_Object 
{
    /**
     * Constructor receives $address as array of fields for new address or integer to load existing id
     *
     * @param array|integer $address
     */
    public function __construct() 
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getEntityIdField());
    }
    
    /**
     * Get customer address resource model
     *
     * @return mixed
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('customer/address');
    }
    
    /**
     * Load customer data
     *
     * @param   int $addressId
     * @return  Mage_Customer_Model_Address
     */
    public function load($addressId) 
    {
        $this->getResource()->load($this, $addressId);
        return $this;
    }
    
    /**
     * save customer address
     *
     * @return  Mage_Customer_Model_Address
     */
    public function save() 
    {
        $this->getResource()
            ->loadAllAttributes()
            ->save($this);
        return $this;
    }
    
    /**
     * Delete customer address
     *
     * @return Mage_Customer_Model_Address
     */
    public function delete() 
    {
        $this->getResource()->delete($this);
        $this->setData(array());
        return $this;
    }
    
    /**
     * get address street
     *
     * @param   int $line address line index
     * @return  string
     */
    public function getStreet($line=0)
    {
        $street = parent::getData('street');
        if (-1===$line) {
            return $street;
        } else {
            $arr = explode("\n", $street);
            if (0===$line) {
                return $arr;
            } elseif (isset($arr[$line-1])) {
                return $arr[$line-1];
            } else {
                return '';
            }
        }
    }
    
    /**
     * set address street informa
     *
     * @param unknown_type $street
     * @return unknown
     */
    public function setStreet($street)
    {
        if (is_array($street)) {
            $street = trim(implode("\n", $street));
        }
        $this->setData('street', $street);
        return $this;
    }
    
    /**
     * get address data
     *
     * @param   string $key
     * @param   int $index
     * @return  mixed
     */
    public function getData($key='', $index=null)
    {
        if (strncmp($key, 'street', 6)) {
            $index = substr($key, 6);
            if (!is_numeric($index)) {
                $index = null;
            }
        }
        return parent::getData($key, $index);
    }
    
    /**
     * Create fields street1, street2, etc.
     * 
     * To be used in controllers for views data
     *
     */
    public function explodeStreetAddress()
    {
        $streetLines = $this->getStreet();
        foreach ($streetLines as $i=>$line) {
            $this->setData('street'.($i+1), $line);
        }
    }
    
    /**
     * To be used when processing _POST
     */
    public function implodeStreetAddress()
    {
        $this->setStreet($this->getData('street'));
    }
    
    /**
     * Retrieve address entity attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->getResource()
            ->loadAllAttributes()
            ->getAttributesByName();
    }
}