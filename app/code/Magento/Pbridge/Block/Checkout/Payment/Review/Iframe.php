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
 * Dibs payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
class Magento_Pbridge_Block_Checkout_Payment_Review_Iframe extends Magento_Pbridge_Block_Iframe_Abstract
{
    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '400';

    /**
     * Pbridge session
     *
     * @var Magento_Pbridge_Model_Session
     */
    protected $_pbridgeSession;

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
     * @param Magento_Pbridge_Model_Session $pbridgeSession
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
        array $data = array()
    ) {
        $this->_pbridgeSession = $pbridgeSession;
        parent::__construct($coreData, $context, $customerSession, $pbridgeSession, $regionFactory, $storeManager,
            $pbridgeData, $data);
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrlSuccess()
    {
        return $this->getUrl('magento_pbridge/pbridge/success', array('_current' => true, '_secure' => true));
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrlError()
    {
        return $this->getUrl('magento_pbridge/pbridge/error', array('_current' => true, '_secure' => true));
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
            'redirect_url_success' => $this->getRedirectUrlSuccess(),
            'redirect_url_error' => $this->getRedirectUrlError(),
            'request_gateway_code' => $this->getMethod()->getOriginalCode(),
            'token' => $this->_pbridgeSession->getToken(),
            'already_entered' => '1',
            'magento_payment_action' => $this->getMethod()->getConfigPaymentAction(),
            'css_url' => $this->getCssUrl(),
            'customer_id' => $this->getCustomerIdentifier(),
            'customer_name' => $this->getCustomerName(),
            'customer_email' => $this->getCustomerEmail()
        );

        $sourceUrl = $this->_pbridgeData->getGatewayFormUrl($requestParams, $this->getQuote());
        return $sourceUrl;
    }
}
