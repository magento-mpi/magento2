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
 * Abstract payment block
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Pbridge_Block_Payment_Form_Abstract extends Enterprise_Pbridge_Block_Iframe_Abstract
{
    /**
     * Default template for payment form block
     *
     * @var string
     */
    protected $_template = 'Enterprise_Pbridge::checkout/payment/pbridge.phtml';

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
            'customer_id' => $this->getCustomerIdentifier(),
            'customer_name' => $this->getCustomerName(),
            'customer_email' => $this->getCustomerEmail()
        );

        $billing = $this->getQuote()->getBillingAddress();
        $requestParams['billing'] = $this->getMethod()->getPbridgeMethodInstance()->getAddressInfo($billing);
        $shipping = $this->getQuote()->getShippingAddress();
        $requestParams['shipping'] = $this->getMethod()->getPbridgeMethodInstance()->getAddressInfo($shipping);

        $sourceUrl = Mage::helper('Enterprise_Pbridge_Helper_Data')->getGatewayFormUrl($requestParams, $this->getQuote());
        return $sourceUrl;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        return parent::_toHtml();
    }

    /**
     * Whether 3D Secure validation enabled for payment
     * @return bool
     */
    public function is3dSecureEnabled()
    {
        return false;
    }

    /**
     * Overwtite iframe element height if 3D validation enabled
     * @return string
     */
    public function getIframeHeight()
    {
        if ($this->is3dSecureEnabled()) {
            return $this->_iframeHeight3dSecure;
        }
        return $this->_iframeHeight;
    }
}
