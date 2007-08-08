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
    protected $_addressCollection;
    
    /**
     * Customer subscription model
     *
     * @var Mage_Newsletter_Subscriber
     */
    protected $_subscriber = null;
    
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
        $this->getResource()
            ->loadAllAttributes()
            ->save($this);
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
    public function changePassword($newPassword, $checkCurrent=true)
    {
        $this->getResource()->changePassword($this, $newPassword, $checkCurrent);
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
        if (empty($this->_addressCollection)) {
            $this->_addressCollection = Mage::getResourceModel('customer/address_collection');
        }
        return $this->_addressCollection;
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
                ->addAttributeToSelect('*')
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
            ->getAttributesByCode();
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
    
    /**
     * Hach customer password
     *
     * @param   string $password
     * @return  string
     */
    public function hashPassword($password)
    {
        return md5($password);
    }
    
    /**
     * Retrieve primary address by type(attribute)
     *
     * @param   string $attributeCode
     * @return  Mage_Customer_Mode_Address
     */
    public function getPrimaryAddress($attributeCode)
    {
        $addressId = $this->getData($attributeCode);
        $primaryAddress = false;
        if ($addressId) {
            foreach ($this->getLoadedAddressCollection() as $address) {
            	if ($addressId == $address->getId()) {
            	    return $address;
            	}
            }
        }
        return $primaryAddress;
    }
    
    /**
     * Retrieve customer primary billing address
     *
     * @return Mage_Customer_Mode_Address
     */
    public function getPrimaryBillingAddress()
    {
        return $this->getPrimaryAddress('default_billing');
    }
    
    public function getDefaultBillingAddress()
    {
        return $this->getPrimaryBillingAddress();
    }
    
    /**
     * Retrieve primary customer shipping address
     *
     * @return Mage_Customer_Mode_Address
     */
    public function getPrimaryShippingAddress()
    {
        return $this->getPrimaryAddress('default_shipping');
    }
    
    public function getDefaultShippingAddress()
    {
        return $this->getPrimaryShippingAddress();
    }
    
    /**
     * Retrieve ids of primary addresses
     *
     * @return unknown
     */
    public function getPrimaryAddressIds()
    {
        $ids = array();
        if ($this->getDefaultBilling()) {
            $ids[] = $this->getDefaultBilling();
        }
        if ($this->getDefaultShipping()) {
            $ids[] = $this->getDefaultShipping();
        }
        return $ids;
    }
    
    /**
     * Retrieve all customer primary addresses
     *
     * @return array
     */
    public function getPrimaryAddresses()
    {
        $addresses = array();
        $primaryBilling = $this->getPrimaryBillingAddress();
        if ($primaryBilling) {
            $addresses[] = $primaryBilling;
            $primaryBilling->setIsPrimaryBilling(true);
        }
        
        $primaryShipping = $this->getPrimaryShippingAddress();
        if ($primaryShipping) {
            if ($primaryBilling->getId() == $primaryShipping->getId()) {
                $primaryBilling->setIsPrimaryShipping(true);
            }
            else {
                $primaryShipping->setIsPrimaryShipping(true);
                $addresses[] = $primaryShipping;
            }
        }
        return $addresses;
    }
    
    /**
     * Retrieve not primary addresses
     *
     * @return array
     */
    public function getAdditionalAddresses()
    {
        $addresses = array();
        $primatyIds = $this->getPrimaryAddressIds();
        foreach ($this->getLoadedAddressCollection() as $address) {
        	if (!in_array($address->getId(), $primatyIds)) {
        	    $addresses[] = $address;
        	}
        }
        return $addresses;
    }
    
    public function isAddressPrimary(Mage_Customer_Model_Address $address)
    {
        if (!$address->getId()) {
            return false;
        }
        return ($address->getId() == $this->getDefaultBilling()) || ($address->getId() == $this->getDefaultShipping());
    }
    
    public function generatePassword($length=6)
    {
        return substr(md5(uniqid(rand(), true)), 0, $length);
    }
}
