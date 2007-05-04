<?php
/**
 * Customer
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Customer extends Varien_Data_Object
{
    /**
     * Customer address collection
     *
     * @var Varien_Data_Colection_Db
     */
    protected $_addresses;
    
    public function __construct($customer=false) 
    {
        parent::__construct();
        
        if (is_numeric($customer)) {
            $this->loadByCustomerId($customer);
        } elseif (is_array($customer)) {
            $this->setData($customer);
        }
    }
    
    public function __sleep()
    {
        unset($this->_addresses);
        return parent::__sleep();
    }
    
    public function getId()
    {
        return $this->getCustomerId();
    }

    public function getResource()
    {
        return Mage::getSingleton('customer_resource', 'customer');
    }
    
    public function authenticate($login, $password)
    {
        if ($this->getResource()->authenticate($this, $login, $password)) {
            return $this;
        }
        return false;
    }
    
    public function load($customerId)
    {
        $this->setData($this->getResource()->load($customerId));
        return $this;
    }
    
    public function loadByEmail($customerEmail)
    {
        $this->setData($this->getResource()->loadByEmail($customerEmail));
        return $this;
    }
    
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    /**
     * Change customer password
     * $data = array(
     *      ['password']
     *      ['confirmation']
     *      ['current_password']
     * )
     * 
     * @param   array $data
     * @param   bool $checkCurrent
     * @return  this
     */
    public function changePassword($data, $checkCurrent=true)
    {
        $this->getResource()->changePassword($this->getId(), $data, $checkCurrent);
        return $this;
    }
    
    public function delete()
    {
        $this->getResource()->delete($this->getId());
        return $this;
    }
    
    public function getName()
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    public function addAddress(Mage_Customer_Model_Address $address)
    {
        if (!$this->_addresses) {
            $this->_addresses = Mage::getModel('customer_resource', 'address_collection');
        }
        
        $this->_addresses->addItem($address);
        return $this;
    }   
    
    public function getAddressById($addressId)
    {
        $address = Mage::getConfig()->getModelClassName('customer', 'address')
            ->load($addressId);
        return $address;
    }

    public function getAddressCollection($reload=false)
    {
        if ($this->_addresses && !$reload) {
            return $this->_addresses;
        }
        
        if ($this->getCustomerId()) {
            $this->_addresses = Mage::getModel('customer_resource', 'address_collection')->loadByCustomerId($this->getCustomerId());
        }
        else {
            $this->_addresses = Mage::getModel('customer_resource', 'address_collection');
        }
        
        return $this->_addresses;
    }
    
    public function getHashPassword()
    {
        if (!$this->getData('hash_password') && $this->getPassword()) {
            $this->setHashPassword($this->getResource()->hashPassword($this->getPassword()));
        }
        return $this->getData('hash_password');
    }
    
    public function getWishlistCollection()
    {
        $collection = Mage::getModel('customer_resource', 'wishlist_collection');
        $collection->addCustomerFilter($this->getId());

        return $collection;
    }
}