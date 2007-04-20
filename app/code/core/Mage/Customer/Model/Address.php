<?php
/**
 * Customer address
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Address extends Varien_Data_Object 
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
    
    public function getId()
    {
        return $this->getAddressId();
    }
    
    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('customer_resource', 'address');
        }
        return $resource;
    }
    
    public function load($addressId) 
    {
        $this->setData($this->getResource()->load($addressId));
        $types = $this->getResource()->loadTypes($addressId);
        foreach ($types as $typeId => $typeInfo) {
            $this->setType($typeId, $typeInfo['code'], $typeInfo['primary']);
        }
        return $this;
    }

    public function save($useTransaction=true) 
    {
        $this->getResource()->save($this, $useTransaction);
        return $this;
    }

    public function delete() 
    {
        $this->getResource()->delete($this->getId());
        $this->setData(array());
        return $this;
    }
    
    public function getAvailableTypes($by='code', $langCode='en') 
    {
        return $this->getResource()->getAvailableTypes($by, $langCode);
    }
    
    public function setPrimaryTypes($types)
    {
        $this->_primaryTypes = $types;
    }
    
    public function getPrimaryTypes()
    {
        return $this->_primaryTypes;
    }
    
    public function setTypes($types)
    {
        $this->_types = $types;
    }
    
    public function getTypes()
    {
        return $this->_types;
    }
    
    public function isPrimary($type)
    {
        if (is_numeric($type)) {
            return in_array($type, $this->_primaryTypes);
        }
        else {
            return ($typeId = array_search($type, $this->_types)) ? in_array($typeId, $this->_primaryTypes) : false;
        }
    }
    
    public function setType($typeId, $typeCode,$isPrimary=null)
    {
        $this->_types[$typeId] = $typeCode;
        if ($isPrimary && !in_array($typeId, $this->_primaryTypes)) {
            $this->_primaryTypes[] = $typeId;
        }
    }
    
    public function getStreet($line=0)
    {
        if (-1===$line) {
            return $this->getData('street');
        } else {
            $arr = explode("\n", trim($this->getData('street')));
            if (0===$line) {
                return $arr;
            } elseif (isset($arr[$line-1])) {
                return $arr[$line-1];
            } else {
                return '';
            }
        }
    }

    public function setStreet($street)
    {
        if (is_array($street)) {
            $street = trim(implode("\n", $street));
        }
        $this->setData('street', $street);
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
     *
     */
    public function implodeStreetAddress()
    {
        $this->setStreet($this->getData('street'));
    }
}