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
 * Abstract payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Pbridge_Block_Payment_Form_Abstract extends Magento_Pbridge_Block_Iframe_Abstract
{
    /**
     * Default template for payment form block
     *
     * @var string
     */
    protected $_template = 'Magento_Pbridge::checkout/payment/pbridge.phtml';

    /**
     * Whether to include billing parameters in Payment Bridge source URL
     *
     * @var bool
     */
    protected $_sendBilling = false;

    /**
     * Whether to include shipping parameters in Payment Bridge source URL
     *
     * @var bool
     */
    protected $_sendShipping = false;

    /**
     * Checkout session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

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
     * @param array $data
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
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($coreData, $context, $customerSession, $pbridgeSession, $regionFactory, $storeManager,
            $pbridgeData, $data);
    }

    /**
     * Return original payment method code
     *
     *  @return string
     */
    public function getOriginalCode()
    {
        return $this->getMethod()->getOriginalCode();
    }

    /**
     * Getter
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Getter.
     * Return Payment Bridge url with required parameters (such as merchant code, merchant key etc.)
     * Can include quote shipping and billing address if its required in payment processing
     *
     * @return string
     */
    public function getSourceUrl()
    {
        $requestParams = array(
            'redirect_url' => $this->getRedirectUrl(),
            'request_gateway_code' => $this->getOriginalCode(),
            'magento_payment_action' => $this->getMethod()->getConfigPaymentAction(),
            'css_url' => $this->getCssUrl(),
            'customer_id' => $this->getCustomerIdentifier()
        );
        if ($this->_sendBilling) {
            $billing = $this->getQuote()->getBillingAddress();
            $requestParams['billing'] = $this->getMethod()->getPbridgeMethodInstance()->getAddressInfo($billing);
        }
        if ($this->_sendShipping) {
            $shipping = $this->getQuote()->getShippingAddress();
            $requestParams['shipping'] = $this->getMethod()->getPbridgeMethodInstance()->getAddressInfo($shipping);
        }
        $sourceUrl = $this->_pbridgeData->getGatewayFormUrl($requestParams, $this->getQuote());
        return $sourceUrl;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setReloadAllowed($this->_allowReload);
        return parent::_toHtml();
    }
}
