<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Express Checkout Controller
 */
class Magento_Paypal_Controller_Express extends Magento_Paypal_Controller_Express_Abstract
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'Magento_Paypal_Model_Config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = Magento_Paypal_Model_Config::METHOD_WPP_EXPRESS;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Magento_Paypal_Model_Express_Checkout';

    /**
     * @var Magento_Core_Helper_Url
     */
    protected $_urlHelper;

    /**
     * @var Magento_Customer_Helper_Data
     */
    protected $_customerHelper;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Sales_Model_QuoteFactory $quoteFactory
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Paypal_Model_Express_Checkout_Factory $checkoutFactory
     * @param Magento_Core_Model_Session_Generic $paypalSession
     * @param Magento_Core_Helper_Url $urlHelper
     * @param Magento_Customer_Helper_Data $customerHelper
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_Sales_Model_QuoteFactory $quoteFactory,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Paypal_Model_Express_Checkout_Factory $checkoutFactory,
        Magento_Core_Model_Session_Generic $paypalSession,
        Magento_Core_Helper_Url $urlHelper,
        Magento_Customer_Helper_Data $customerHelper
    ) {
        $this->_customerSession = $customerSession;
        $this->_urlHelper = $urlHelper;
        $this->_customerHelper = $customerHelper;
        parent::__construct(
            $context,
            $customerSession,
            $urlBuilder,
            $quoteFactory,
            $checkoutSession,
            $orderFactory,
            $checkoutFactory,
            $paypalSession
        );
    }

    /**
     * Redirect to login page
     */
    public function redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        $this->_customerSession->setBeforeAuthUrl($this->_getRefererUrl());
        $this->getResponse()->setRedirect(
            $this->_urlHelper->addRequestParam($this->_customerHelper->getLoginUrl(), array('context' => 'checkout'))
        );
    }
}
