<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer model
 *
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Model_Customer extends Mage_Core_Model_Abstract
{
    const XML_PATH_REGISTER_EMAIL_TEMPLATE  = 'customer/create_account/email_template';
    const XML_PATH_REGISTER_EMAIL_IDENTITY  = 'customer/create_account/email_identity';
    const XML_PATH_FORGOT_EMAIL_TEMPLATE    = 'customer/password/forgot_email_template';
    const XML_PATH_FORGOT_EMAIL_IDENTITY    = 'customer/password/forgot_email_identity';
    const XML_PATH_DEFAULT_EMAIL_DOMAIN     = 'customer/create_account/email_domain';

    protected $_eventPrefix = 'customer';
    protected $_eventObject = 'customer';

    protected $_addresses = null;

    function _construct()
    {
        $this->_init('customer/customer');
    }

    /**
     * Retrieve customer sharing configuration model
     *
     * @return unknown
     */
    public function getSharingConfig()
    {
        return Mage::getSingleton('customer/config_share');

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
        if ($this->_getResource()->authenticate($this, $login, $password)) {
            return $this;
        }
        return false;
    }

    /**
     * Load customer by email
     *
     * @param   string $customerEmail
     * @return  Mage_Customer_Model_Customer
     */
    public function loadByEmail($customerEmail)
    {
        $this->_getResource()->loadByEmail($this, $customerEmail);
        return $this;
    }


    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        $storeId = $this->getStoreId();
        if (is_null($storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        $this->getGroupId();
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
        $this->_getResource()->changePassword($this, $newPassword, $checkCurrent);
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
        $this->getAddresses();
        $this->_addresses[] = $address;
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
     * @return Mage_Customer_Model_Address_Collection
     */
    public function getAddressCollection()
    {
        return Mage::getResourceModel('customer/address_collection');
    }

    /**
     * Retrieve customer address array
     *
     * @return array
     */
    public function getAddresses()
    {
        if (is_null($this->_addresses)) {
            $this->_addresses = array();
            $collection = $this->getAddressCollection()
                ->setCustomerFilter($this)
                ->addAttributeToSelect('*')
                ->load();
            foreach ($collection as $address) {
            	$this->_addresses[] = $address;
            }
        }

        return $this->_addresses;
    }

    /**
     * Retrieve all customer attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->_getResource()
            ->loadAllAttributes($this)
            ->getAttributesByCode();
    }

    public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        return $this;
    }

    /**
     * Hach customer password
     *
     * @param   string $password
     * @return  string
     */
    public function hashPassword($password)
    {
        return $this->_getResource()->getHashPassword($password);
    }

    /**
     * Encrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function encryptPassword($password)
    {
        return Mage::helper('core')->encrypt($password);
    }

    /**
     * Decrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function decryptPassword($password)
    {
        return Mage::helper('core')->decrypt($password);
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
            foreach ($this->getAddresses() as $address) {
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
        foreach ($this->getAddresses() as $address) {
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

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length=6)
    {
        return substr(md5(uniqid(rand(), true)), 0, $length);
    }

    /**
     * Send email with account information
     *
     * @return Mage_Customer_Model_Customer
     */
    public function sendNewAccountEmail()
    {
        Mage::getModel('core/email_template')
            ->setDesignConfig(array('area'=>'frontend', 'store'=>$this->getStoreId()))
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_REGISTER_EMAIL_TEMPLATE),
                Mage::getStoreConfig(self::XML_PATH_REGISTER_EMAIL_IDENTITY),
                $this->getEmail(),
                $this->getName(),
                array('customer'=>$this));
        return $this;
    }

    /**
     * Send email with new customer password
     *
     * @return Mage_Customer_Model_Customer
     */
    public function sendPasswordReminderEmail()
    {
        Mage::getModel('core/email_template')
            ->sendTransactional(
              Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_TEMPLATE),
              Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_IDENTITY),
              $this->getEmail(),
              $this->getName(),
              array('customer'=>$this));
        return $this;
    }

    /**
     * Retrieve customer group identifier
     *
     * @return int
     */
    public function getGroupId()
    {
        if (!$this->getData('group_id')) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();
            $this->setData('group_id', Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID, $storeId));
        }
        return $this->getData('group_id');
    }

    /**
     * Retrieve customer tax class identifier
     *
     * @return int
     */
    public function getTaxClassId()
    {
        if (!$this->getData('tax_class_id')) {
            $this->setTaxClassId(Mage::getModel('customer/group')->load($this->getGroupId())->getTaxClassId());
        }
        return $this->getData('tax_class_id');
    }

    /**
     * Check store availability for customer
     *
     * @param   mixed $store
     * @return  bool
     */
    public function isInStore($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $storeId = $store->getId();
        }
        else {
            $storeId = $store;
        }
        $availableStores = $this->getSharedStoreIds();
        return in_array($storeId, $availableStores);
    }

    /**
     * Retrieve store where customer was created
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }

    /**
     * Retrieve shared store ids
     *
     * @return array|false
     */
    public function getSharedStoreIds()
    {
        return $this->getStore()->getWebsite()->getStoresIds();
    }

    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Customer_Model_Customer
     */
    public function setStore(Mage_Core_Model_Store $store)
    {
        $this->setStoreId($store->getId());
        return $this;
    }

    public function importFromTextArray(array $row)
    {
        $hlp = Mage::helper('customer');
        $line = $row['i'];
        $row = $row['row'];
        $isError = false;        
        $config = Mage::getSingleton('eav/config')->getEntityType('customer');
        // Validate Email
        if (empty($row['email'])) {
            $this->printError($hlp->__('Missing email, skipping the record'), $line);
            return ;
        } // End
        
        if (empty($row['entity_id'])) {
            $row['entity_id'] = $this->getIdByEloadByEmail($row['email']);
        }
        if (!empty($row['entity_id'])) {
            $this->unsetData();
            $this->load($row['entity_id']);
            if (isset($row['store'])) {
                $storeId = Mage::app()->getStore($row['store'])->getId();
                if ($storeId) $this->setStoreId($storeId);                
            }        
        } 
            
        if (empty($row['website'])) {
            $this->printError($hlp->__('Missing website, skipping the record'), $line);
            return;            
        }
        
        $website = Mage::getModel('core/website')->load($row['website'], 'code');
        if (!$website->getId()) {
            $this->printError('Invalid website, skipping the record', $line);
            return;                        
        } else {
            $row['website_id'] = $website->getWebsiteId();
        }        
        if (empty($row['group'])) {
            $row['group'] = 'General';
        }        
        
        if (empty($row['firstname'])) {
            $this->printError($hlp->__('Missing firstname, skipping the record'), $line);
            return;
        }
        if (empty($row['lastname'])) {
            $this->printError($hlp->__('Missing lastname, skipping the record'), $line);
            return;
        }
        $entity = $this->getResource();
        
        foreach ($row as $field=>$value) {
            /*
            $attribute = $entity->getAttribute($field);
            if (!$attribute) {
                echo $field;
                continue;
            }
            if ($attribute->usesSource()) {
                $source = $attribute->getSource();
                $optionId = $config->getSourceOptionId($source, $value);
                if (is_null($optionId)) {
                    $this->printError($hlp->__("Invalid attribute option specified for attribute attribute %s (%s)", $field, $value), $line);
                }
                $value = $optionId;
            }
            */
            $this->setData($field, $value);
        } 
 
        $billingAddress = $this->getPrimaryBillingAddress();
        if (!$billingAddress  instanceof Mage_Customer_Model_Address) {
            $billingAddress = new Mage_Customer_Model_Address();
            if ($this->getId() && $this->getDefaultBilling()) {
                $billingAddress->setId($this->getDefaultBilling());
            }
        }
        $regions = Mage::getResourceModel('directory/region_collection')
            ->addRegionNameFilter($row['billing_region'])->load();
        if ($regions) foreach($regions as $region) {
            $regionId = $region->getId();
        }

        $billingAddress->setFirstname($row['firstname']);
        $billingAddress->setLastname($row['lastname']);
        $billingAddress->setCity($row['billing_city']);
        $billingAddress->setRegion($row['billing_region']);
        $billingAddress->setRegionId($regionId);
        $billingAddress->setCountryId($row['billing_country']);
        $billingAddress->setPostcode($row['billing_postcode']);
        $billingAddress->setStreet(array($row['billing_street1'],$row['billing_street2']));
        if (!empty($row['billing_telephone'])) {
            $billingAddress->setTelephone($row['billing_telephone']);
        }
        if (!$this->getDefaultBilling()) {
            $billingAddress->setCustomerId($this->getId());
            $billingAddress->setIsDefaultBilling(true);
            $billingAddress->save();
            $this->setDefaultBilling($billingAddress->getId());
            $this->addAddress($billingAddress);
            if ($this->getDefaultBilling()) {
                $this->setDefaultBilling($this->getDefaultBilling());
            } else {
                $billingAddress->save();
                $this->setDefaultShipping($billingAddress->getId());
                $this->addAddress($billingAddress);

            }
        }

        $shippingAddress = $this->getPrimaryShippingAddress();
        if (!$shippingAddress instanceof Mage_Customer_Model_Address) {
            $shippingAddress = new Mage_Customer_Model_Address();
            if ($this->getId() && $this->getDefaultShipping()) {
                $shippingAddress->setId($this->getDefaultShipping());
            }
        }

        $regions = Mage::getResourceModel('directory/region_collection')->addRegionNameFilter($row['shipping_region'])->load();
        if ($regions) foreach($regions as $region) {
           $regionId = $region->getId();
        }

        $shippingAddress->setFirstname($row['firstname']);
        $shippingAddress->setLastname($row['lastname']);
        $shippingAddress->setCity($row['shipping_city']);
        $shippingAddress->setRegion($row['shipping_region']);
        $shippingAddress->setRegionId($regionId);
        $shippingAddress->setCountryId($row['shipping_country']);
        $shippingAddress->setPostcode($row['shipping_postcode']);
        $shippingAddress->setStreet(array($row['shipping_street1'], $row['shipping_street2']));
        $shippingAddress->setCustomerId($this->getId());
        if (!empty($row['shipping_telephone'])) {
            $shippingAddress->setTelephone($row['shipping_telephone']);
        }

        if (!$this->getDefaultShipping()) {
            if ($this->getDefaultShipping()) {
                $this->setDefaultShipping($this->getDefaultShipping());
            } else {
                $shippingAddress->save();
                $this->setDefaultShipping($shippingAddress->getId());
                $this->addAddress($shippingAddress);

            }
            $shippingAddress->setIsDefaultShipping(true);
        }       
        return $this;
    }

    function printError($error, $line = null)
    {
        if ($error == null) return false;
        $img = 'error_msg_icon.gif';
        $liStyle = 'background-color:#FDD; ';
        echo '<li style="'.$liStyle.'">';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/'.$img).'" class="v-middle"/>';
        echo $error;
        if ($line) {
            echo '<small>, Line: <b>'.$line.'</b></small>';
        }
        echo "</li>";
    }    
}