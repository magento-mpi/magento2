<?php
/**
 * Customer
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Customer extends Varien_Object
{
    /**
     * Customer address collection
     *
     * @var Varien_Data_Colection_Db
     */
    protected $_addresses;
    
    static protected $_entity;
    
    public function __construct($customer=false) 
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getEntityIdField());
        
        if (is_numeric($customer)) {
            $this->load($customer);
        } elseif (is_array($customer)) {
            $this->setData($customer);
        }
    }
    
    public function __sleep()
    {
        unset($this->_addresses);
        return parent::__sleep();
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('customer/customer');
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
        $this->getResource()->load($this, $customerId);
        #$this->setData($this->getResource()->load($customerId));
        return $this;
    }
    
    public function loadByEmail($customerEmail)
    {
        #$this->setData($this->getResource()->loadByEmail($customerEmail));
        $this->getResource()->loadByEmail($this, $customerEmail);
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
        $this->getResource()->changePassword($this, $data, $checkCurrent);
        return $this;
    }
    
    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }
    
    public function getName()
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    public function addAddress(Mage_Customer_Model_Address $address)
    {
        if (!$this->_addresses) {
            $this->_addresses = Mage::getResourceModel('customer/address_collection');
        }
        
        $this->getAddressCollection()->addItem($address);
        return $this;
    }   
    
    public function getAddressById($addressId)
    {
        $address = Mage::getConfig()->getModelClassName('customer/address')
            ->load($addressId);
        return $address;
    }

    public function getAddressCollection($reload=false)
    {
        if ($this->_addresses && !$reload) {
            return $this->_addresses;
        }
        
        if ($this->getCustomerId()) {
            $this->_addresses = Mage::getResourceModel('customer/address_collection')->loadByCustomerId($this->getCustomerId());
        }
        else {
            $this->_addresses = Mage::getResourceModel('customer/address_collection');
        }
        
        return $this->_addresses;
    }
    
    public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        return $this;
    }
    
    public function getWishlistCollection()
    {
        if ($this->_wishlist && !$reload) {
            return $this->_wishlist;
        }
        
        $this->_wishlist = Mage::getResourceModel('customer/wishlist_collection');
        $this->_wishlist->addCustomerFilter($this->getId());

        return $this->_wishlist;
    }
    
    public function hashPassword($password)
    {
        return md5($password);
    }
}