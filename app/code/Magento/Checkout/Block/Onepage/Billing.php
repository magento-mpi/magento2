<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout status
 *
 * @category   Magento
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Onepage_Billing extends Magento_Checkout_Block_Onepage_Abstract
{
    /**
     * Sales Qoute Billing Address instance
     *
     * @var Magento_Sales_Model_Quote_Address
     */
    protected $_address;

    /**
     * Customer Taxvat Widget block
     *
     * @var Magento_Customer_Block_Widget_Taxvat
     */
    protected $_taxvat;

    /**
     * @var Magento_Sales_Model_Quote_AddressFactory
     */
    protected $_addressFactory;

    /**
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Checkout_Model_Session $resourceSession
     * @param Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory
     * @param Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Sales_Model_Quote_AddressFactory $addressFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Checkout_Model_Session $resourceSession,
        Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory,
        Magento_Directory_Model_Resource_Region_CollectionFactory $regionCollFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Sales_Model_Quote_AddressFactory $addressFactory,
        array $data = array()
    ) {
        $this->_addressFactory = $addressFactory;
        parent::__construct($configCacheType, $coreData, $context, $customerSession, $resourceSession,
            $countryCollFactory, $regionCollFactory, $storeManager, $data);
    }

    /**
     * Initialize billing address step
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData('billing', array(
            'label'     => __('Billing Information'),
            'is_show'   => $this->isShow()
        ));

        if ($this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('billing', 'allow', true);
        }
        parent::_construct();
    }

    public function isUseBillingAddressForShipping()
    {
        if (($this->getQuote()->getIsVirtual())
            || !$this->getQuote()->getShippingAddress()->getSameAsBilling()) {
            return false;
        }
        return true;
    }

    /**
     * Return country collection
     *
     * @return Magento_Directory_Model_Resource_Country_Collection
     */
    public function getCountries()
    {
        return $this->_countryCollFactory->create()->loadByStore();
    }

    /**
     * Return checkout method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }

    /**
     * Return Sales Quote Address model
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            if ($this->isCustomerLoggedIn()) {
                $this->_address = $this->getQuote()->getBillingAddress();
                if(!$this->_address->getFirstname()) {
                    $this->_address->setFirstname($this->getQuote()->getCustomer()->getFirstname());
                }
                if(!$this->_address->getLastname()) {
                    $this->_address->setLastname($this->getQuote()->getCustomer()->getLastname());
                }
            } else {
                $this->_address = $this->_addressFactory->create();
            }
        }

        return $this->_address;
    }

    /**
     * Return Customer Address First Name
     * If Sales Quote Address First Name is not defined - return Customer First Name
     *
     * @return string
     */
    public function getFirstname()
    {
        $firstname = $this->getAddress()->getFirstname();
        if (empty($firstname) && $this->getQuote()->getCustomer()) {
            return $this->getQuote()->getCustomer()->getFirstname();
        }
        return $firstname;
    }

    /**
     * Return Customer Address Last Name
     * If Sales Quote Address Last Name is not defined - return Customer Last Name
     *
     * @return string
     */
    public function getLastname()
    {
        $lastname = $this->getAddress()->getLastname();
        if (empty($lastname) && $this->getQuote()->getCustomer()) {
            return $this->getQuote()->getCustomer()->getLastname();
        }
        return $lastname;
    }

    /**
     * Check is Quote items can ship to
     *
     * @return boolean
     */
    public function canShip()
    {
        return !$this->getQuote()->isVirtual();
    }

    public function getSaveUrl()
    {
    }

    /**
     * Get Customer Taxvat Widget block
     *
     * @return Magento_Customer_Block_Widget_Taxvat
     */
    protected function _getTaxvat()
    {
        if (!$this->_taxvat) {
            $this->_taxvat = $this->getLayout()->createBlock('Magento_Customer_Block_Widget_Taxvat');
        }

        return $this->_taxvat;
    }

    /**
     * Check whether taxvat is enabled
     *
     * @return bool
     */
    public function isTaxvatEnabled()
    {
        return $this->_getTaxvat()->isEnabled();
    }

    public function getTaxvatHtml()
    {
        return $this->_getTaxvat()
            ->setTaxvat($this->getQuote()->getCustomerTaxvat())
            ->setFieldIdFormat('billing:%s')
            ->setFieldNameFormat('billing[%s]')
            ->toHtml();
    }
}
