<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml quote session
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Model_Session_Quote extends Magento_Core_Model_Session_Abstract
{
    const XML_PATH_DEFAULT_CREATEACCOUNT_GROUP = 'customer/create_account/default_group';

    /**
     * Quote model object
     *
     * @var Magento_Sales_Model_Quote
     */
    protected $_quote   = null;

    /**
     * Customer mofrl object
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer= null;

    /**
     * Store model object
     *
     * @var Magento_Core_Model_Store
     */
    protected $_store   = null;

    /**
     * Order model object
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_order   = null;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct($logger, $eventManager, $coreHttp, $coreStoreConfig, $coreConfig, $data);
        $this->init('adminhtml_quote');
        if (Mage::app()->hasSingleStore()) {
            $this->setStoreId(Mage::app()->getStore(true)->getId());
        }
    }

    /**
     * Retrieve quote model object
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (is_null($this->_quote)) {
            $this->_quote = Mage::getModel('Magento_Sales_Model_Quote');
            if ($this->getStoreId() && $this->getQuoteId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->load($this->getQuoteId());
            }
            elseif($this->getStoreId() && $this->hasCustomerId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->setCustomerGroupId($this->_coreStoreConfig->getConfig(self::XML_PATH_DEFAULT_CREATEACCOUNT_GROUP))
                    ->assignCustomer($this->getCustomer())
                    ->setIsActive(false)
                    ->save();
                $this->setQuoteId($this->_quote->getId());
            }
            $this->_quote->setIgnoreOldQty(true);
            $this->_quote->setIsSuperMode(true);
        }
        return $this->_quote;
    }

    /**
     * Set customer model object
     * To enable quick switch of preconfigured customer
     * @param Magento_Customer_Model_Customer $customer
     * @return Magento_Adminhtml_Model_Session_Quote
     */
    public function setCustomer(Magento_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

/**
     * Retrieve customer model object
     * @param bool $forceReload
     * @param bool $useSetStore
     * @return Magento_Customer_Model_Customer
     */
    public function getCustomer($forceReload=false, $useSetStore=false)
    {
        if (is_null($this->_customer) || $forceReload) {
            $this->_customer = Mage::getModel('Magento_Customer_Model_Customer');
            if ($useSetStore && $this->getStore()->getId()) {
                $this->_customer->setStore($this->getStore());
            }
            if ($customerId = $this->getCustomerId()) {
                $this->_customer->load($customerId);
            }
        }
        return $this->_customer;
    }

    /**
     * Retrieve store model object
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore($this->getStoreId());
            if ($currencyId = $this->getCurrencyId()) {
                $this->_store->setCurrentCurrencyCode($currencyId);
            }
        }
        return $this->_store;
    }

    /**
     * Retrieve order model object
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        if (is_null($this->_order)) {
            $this->_order = Mage::getModel('Magento_Sales_Model_Order');
            if ($this->getOrderId()) {
                $this->_order->load($this->getOrderId());
            }
        }
        return $this->_order;
    }
}
