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
     * address types
     *
     * @var array($id=>$code)
     */
    protected $_types = array();
    
    /**
     * address primary types
     *
     * @var array
     */
    protected $_primaryTypes = array();
    
    /**
     * Constructor receives $address as array of fields for new address or integer to load existing id
     *
     * @param array|integer $address
     */
    public function __construct($address=false) 
    {
        parent::__construct();
        
        if (is_numeric($address)) {
            $this->load($address);
        } elseif (is_array($address)) {
            $this->setData($address);
        }
    }
    
    /**
     * get customer address id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getAddressId();
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
        $this->setData($this->getResource()->load($addressId));
        $types = $this->getResource()->loadTypes($addressId);
        foreach ($types as $typeId => $typeInfo) {
            $this->setType($typeId, $typeInfo['code'], $typeInfo['primary']);
        }
        return $this;
    }
    
    /**
     * save customer address
     *
     * @param   bool $useTransaction
     * @return  Mage_Customer_Model_Address
     */
    public function save($useTransaction=true) 
    {
        $this->getResource()->save($this, $useTransaction);
        return $this;
    }
    
    /**
     * Delete customer address
     *
     * @return Mage_Customer_Model_Address
     */
    public function delete() 
    {
        $this->getResource()->delete($this->getId());
        $this->setData(array());
        return $this;
    }
    
    /**
     * Get address available types
     *
     * @param   string $by
     * @param   string $langCode
     * @return  array
     */
    public function getAvailableTypes($by='code', $langCode='en') 
    {
        return $this->getResource()->getAvailableTypes($by, $langCode);
    }
    
    /**
     * Set address primaty types
     *
     * @param Mage_Customer_Model_Address
     */
    public function setPrimaryTypes($types)
    {
        $this->_primaryTypes = $types;
        return $this;
    }
    
    /**
     * get address primary types
     *
     * @return array
     */
    public function getPrimaryTypes()
    {
        return $this->_primaryTypes;
    }
    
    /**
     * set address types
     *
     * @param array $types
     */
    public function setTypes($types)
    {
        $this->_types = $types;
        return $this;
    }
    
    /**
     * get address types
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->_types;
    }
    
    /**
     * Check address type by primary
     *
     * @param   string $type
     * @return  bool
     */
    public function isPrimary($type)
    {
        if (is_numeric($type)) {
            return in_array($type, $this->_primaryTypes);
        }
        else {
            return ($typeId = array_search($type, $this->_types)) ? in_array($typeId, $this->_primaryTypes) : false;
        }
    }
    
    /**
     * Set address type
     *
     * @param int $typeId
     * @param string $typeCode
     * @param bool $isPrimary
     * @return Mage_Customer_Model_Address
     */
    public function setType($typeId, $typeCode,$isPrimary=null)
    {
        $this->_types[$typeId] = $typeCode;
        if ($isPrimary && !in_array($typeId, $this->_primaryTypes)) {
            $this->_primaryTypes[] = $typeId;
        }
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
    public function getData($key='', $index=false)
    {
        if (strncmp($key, 'street', 6)) {
            $index = substr($key, 6);
            if (!is_numeric($index)) {
                $index = false;
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
}