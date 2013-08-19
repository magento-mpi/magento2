<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page common functionality block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Checkout_Block_Onepage_Abstract extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    protected $_customer;
    protected $_checkout;
    protected $_quote;
    protected $_countryCollection;
    protected $_regionCollection;
    protected $_addressesCollection;

    /**
     * @var Mage_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     * @param Mage_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Cache_Type_Config $configCacheType,
        Mage_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_configCacheType = $configCacheType;
        $this->_storeManager = $storeManager;
    }

    /**
     * Get store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Get logged in customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        if (empty($this->_checkout)) {
            $this->_checkout = Mage::getSingleton('Mage_Checkout_Model_Session');
        }
        return $this->_checkout;
    }

    /**
     * Retrieve sales quote model
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }

    public function isCustomerLoggedIn()
    {
        return Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn();
    }

    public function getCountryCollection()
    {
        if (!$this->_countryCollection) {
            $this->_countryCollection = Mage::getSingleton('Mage_Directory_Model_Country')->getResourceCollection()
                ->loadByStore();
        }
        return $this->_countryCollection;
    }

    public function getRegionCollection()
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = Mage::getModel('Mage_Directory_Model_Region')->getResourceCollection()
                ->addCountryFilter($this->getAddress()->getCountryId())
                ->load();
        }
        return $this->_regionCollection;
    }

    public function customerHasAddresses()
    {
        return count($this->getCustomer()->getAddresses());
    }

    public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }

            $addressId = $this->getAddress()->getCustomerAddressId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
                //->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                // temp disable inline javascript, need to clean this later
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', __('New Address'));

            return $select->getHtml();
        }
        return '';
    }

    public function getCountryHtmlSelect($type)
    {
        $countryId = $this->getAddress()->getCountryId();
        if (is_null($countryId)) {
            $countryId = Mage::helper('Mage_Core_Helper_Data')->getDefaultCountry();
        }
        $select = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());
        return $select->getHtml();
    }


    public function getRegionHtmlSelect($type)
    {
        $select = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
            ->setName($type.'[region]')
            ->setId($type.':region')
            ->setTitle(__('State/Province'))
            ->setClass('required-entry validate-state')
            ->setValue($this->getAddress()->getRegionId())
            ->setOptions($this->getRegionCollection()->toOptionArray());

        return $select->getHtml();
    }

    public function getCountryOptions()
    {
        $options = false;
        $cacheId = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
        if ($optionsCache = $this->_configCacheType->load($cacheId)) {
            $options = unserialize($optionsCache);
        }

        if ($options == false) {
            $options = $this->getCountryCollection()->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheId);
        }
        return $options;
    }

    /**
     * Get checkout steps codes
     *
     * @return array
     */
    protected function _getStepCodes()
    {
        return array('login', 'billing', 'shipping', 'shipping_method', 'payment', 'review');
    }


    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return true;
    }
}
