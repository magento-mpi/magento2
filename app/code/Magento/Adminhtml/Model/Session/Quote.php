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
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var Magento_Sales_Model_QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @param Magento_Sales_Model_QuoteFactory $quoteFactory
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Core_Model_Session_Validator $validator
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Model_Message_CollectionFactory $messageFactory
     * @param Magento_Core_Model_Message $message
     * @param Magento_Core_Model_Cookie $cookie
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Core_Model_Url_Proxy $url
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Sales_Model_QuoteFactory $quoteFactory,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Core_Model_Session_Validator $validator,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Model_Message_CollectionFactory $messageFactory,
        Magento_Core_Model_Message $message,
        Magento_Core_Model_Cookie $cookie,
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Dir $dir,
        Magento_Core_Model_Url_Proxy $url,
        array $data = array()
    ) {
        $this->_quoteFactory = $quoteFactory;
        $this->_customerFactory = $customerFactory;
        $this->_storeManager = $storeManager;
        $this->_orderFactory = $orderFactory;
        parent::__construct(
            $validator,
            $logger,
            $eventManager,
            $coreHttp,
            $coreStoreConfig,
            $coreConfig,
            $messageFactory,
            $message,
            $cookie,
            $request,
            $appState,
            $storeManager,
            $dir,
            $url,
            $data
        );
        $this->init('adminhtml_quote');
        if ($this->_storeManager->hasSingleStore()) {
            $this->setStoreId($this->_storeManager->getStore(true)->getId());
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
            $this->_quote = $this->_quoteFactory->create();
            if ($this->getStoreId() && $this->getQuoteId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->load($this->getQuoteId());
            } elseif ($this->getStoreId() && $this->hasCustomerId()) {
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
            $this->_customer = $this->_customerFactory->create();
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
            $this->_store = $this->_storeManager->getStore($this->getStoreId());
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
            $this->_order = $this->_orderFactory->create();
            if ($this->getOrderId()) {
                $this->_order->load($this->getOrderId());
            }
        }
        return $this->_order;
    }
}
