<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dibs payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento
 */
class Enterprise_Pbridge_Block_Checkout_Payment_Review_Iframe extends Enterprise_Pbridge_Block_Iframe_Abstract
{
    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '400';

    public function __construct(Enterprise_Pbridge_Helper_Data $pbridgeData, Magento_Core_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($pbridgeData, $context, $data);
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrlSuccess()
    {
        return $this->getUrl('enterprise_pbridge/pbridge/success', array('_current' => true, '_secure' => true));
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrlError()
    {
        return $this->getUrl('enterprise_pbridge/pbridge/error', array('_current' => true, '_secure' => true));
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
            'token' => Mage::getSingleton('Enterprise_Pbridge_Model_Session')->getToken(),
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
