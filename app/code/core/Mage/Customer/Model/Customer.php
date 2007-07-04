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
    public function __construct($customer=false) 
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getEntityIdField());
    }
    
    /**
     * Retrieve customer resource model
     *
     * @return unknown
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('customer/customer');
    }
    
    /**
     * Authenticate customer
     *
     * @param   string $login
     * @param   string $password
     * @return  Mage_Customer_Model_Customer || false
     */
    public function authenticate($login, $password)
    {
        if ($this->getResource()->authenticate($this, $login, $password)) {
            return $this;
        }
        return false;
    }
    
    /**
     * Load customer by customer id
     *
     * @param   int $customerId
     * @return  Mage_Customer_Model_Customer
     */
    public function load($customerId)
    {
        $this->getResource()->load($this, $customerId);
        return $this;
    }
    
    /**
     * Load customer by email
     *
     * @param   string $customerEmail
     * @return  Mage_Customer_Model_Customer
     */
    public function loadByEmail($customerEmail)
    {
        $this->getResource()->loadByEmail($this, $customerEmail);
        return $this;
    }
    
    /**
     * Save customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function save()
    {
        $this->getResource()->loadAllAttributes()->save($this);
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
    
    /**
     * Delete customer  
     *
     * @return Mage_Customer_Model_Customer
     */
    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }
    
    /**
     * Get full customer name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }
    
    /**
     * Add address to address collection
     *
     * @param   Mage_Customer_Model_Address $address
     * @return  Mage_Customer_Model_Customer
     */
    public function addAddress(Mage_Customer_Model_Address $address)
    {
        $this->getAddressCollection()->addItem($address);
        return $this;
    }
    
    /**
     * Retrieve customer address by address id
     *
     * @param   int $addressId
     * @return  Mage_Customer_Model_Address
     */
    public function getAddressById($addressId)
    {
        return Mage::getModel('customer/address')
            ->load($addressId);
    }
    
    /**
     * Retrieve not loaded address collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getAddressCollection()
    {
        $collection = $this->getData('address_collection');
        if (is_null($collection)) {
            $collection = Mage::getResourceModel('customer/address_collection');
            $this->setData('address_collection', $collection);
        }
        
        return $collection;
    }
    
    /**
     * Retrieve loaded customer address collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedAddressCollection()
    {
        $collection = $this->getData('loaded_address_collection');
        if (is_null($collection)) {
            $collection = Mage::getResourceModel('customer/address_collection')
                ->setCustomerFilter($this)
                ->load();
            $this->setData('loaded_address_collection', $collection);
        }
        
        return $collection;
    }
    
    /**
     * Retrieve all customer attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->getResource()
            ->loadAllAttributes()
            ->getAttributesByName();
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