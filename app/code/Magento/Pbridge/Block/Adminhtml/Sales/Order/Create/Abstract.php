<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Paypal Direct payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Block_Adminhtml_Sales_Order_Create_Abstract
    extends Magento_Pbridge_Block_Payment_Form_Abstract
{
    /**
     * Paypal payment code
     *
     * @var string
     */
    protected $_code = Magento_Paypal_Model_Config::METHOD_WPP_DIRECT;

    /**
     * Adminhtml template for payment form block
     *
     * @var string
     */
    protected $_template = 'Magento_Pbridge::sales/order/create/pbridge.phtml';

    /**
     * Adminhtml Iframe block type
     *
     * @var string
     */
    protected $_iframeBlockType = 'Magento_Adminhtml_Block_Template';

    /**
     * Adminhtml iframe template
     *
     * @var string
     */
    protected $_iframeTemplate = 'Magento_Pbridge::iframe.phtml';

    /**
     * Backend url
     *
     * @var Magento_Backend_Model_Url
     */
    protected $_backendUrl;

    /**
     * Adminhtml session quote
     *
     * @var Magento_Adminhtml_Model_Session_Quote
     */
    protected $_adminhtmlSessionQuote;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Pbridge_Model_Session $pbridgeSession
     * @param Magento_Directory_Model_RegionFactory $regionFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Pbridge_Helper_Data $pbridgeData
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Adminhtml_Model_Session_Quote $adminhtmlSessionQuote
     * @param Magento_Backend_Model_Url $backendUrl
     * @param Magento_Core_Model_Config $config
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Pbridge_Model_Session $pbridgeSession,
        Magento_Directory_Model_RegionFactory $regionFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Pbridge_Helper_Data $pbridgeData,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Adminhtml_Model_Session_Quote $adminhtmlSessionQuote,
        Magento_Backend_Model_Url $backendUrl,
        Magento_Core_Model_Config $config,
        array $data = array()
    ) {
        $this->_adminhtmlSessionQuote = $adminhtmlSessionQuote;
        $this->_backendUrl = $backendUrl;
        $this->_config = $config;
        parent::__construct($coreData, $context, $customerSession, $pbridgeSession, $regionFactory, $storeManager,
            $pbridgeData, $checkoutSession, $data);
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->_backendUrl->getUrl('*/pbridge/result',
            array('store' => $this->getQuote()->getStoreId())
        );
    }

    /**
     * Getter
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_adminhtmlSessionQuote->getQuote();
    }

    /**
     * Generate and return variation code
     *
     * @return string
     */
    protected function _getVariation()
    {
        return $this->_config->getValue('payment/pbridge/merchantcode', 'default')
            . '_' . $this->getQuote()->getStore()->getWebsite()->getCode();
    }

    /**
     * Disable external CSS in admin order creation
     * @return null
     */
    public function getCssUrl()
    {
        return null;
    }

    /**
     * Get current customer object
     *
     * @return null|Magento_Customer_Model_Customer
     */
    protected function _getCurrentCustomer()
    {
        if ($this->_adminhtmlSessionQuote->getCustomer() instanceof Magento_Customer_Model_Customer) {
            return $this->_adminhtmlSessionQuote->getCustomer();
        }

        return null;
    }

    /**
     * Return store for current context
     *
     * @return Magento_Core_Model_Store
     */
    protected function _getCurrentStore()
    {
        return $this->getQuote()->getStore();
    }
}
