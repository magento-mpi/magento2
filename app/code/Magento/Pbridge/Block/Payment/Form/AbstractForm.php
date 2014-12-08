<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract payment block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Block\Payment\Form;

abstract class AbstractForm extends \Magento\Pbridge\Block\Iframe\AbstractIframe
{
    /**
     * Default template for payment form block
     *
     * @var string
     */
    protected $_template = 'Magento_Pbridge::checkout/payment/pbridge.phtml';

    /**
     * Whether to include shopping cart items parameters in Payment Bridge source URL
     *
     * @var bool
     */
    protected $_sendCart = false;

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
        $requestParams = [
            'redirect_url' => $this->getRedirectUrl(),
            'request_gateway_code' => $this->getOriginalCode(),
            'magento_payment_action' => $this->getMethod()->getConfigPaymentAction(),
            'css_url' => $this->getCssUrl(),
            'customer_id' => $this->getCustomerIdentifier(),
            'customer_name' => $this->getCustomerName(),
            'customer_email' => $this->getCustomerEmail(),
        ];
        $billing = $this->getQuote()->getBillingAddress();
        $requestParams['billing'] = $this->getMethod()->getPbridgeMethodInstance()->getAddressInfo($billing);
        $shipping = $this->getQuote()->getShippingAddress();
        $requestParams['shipping'] = $this->getMethod()->getPbridgeMethodInstance()->getAddressInfo($shipping);
        if ($this->_sendCart) {
            $requestParams['cart'] = $this->_pbridgeData->prepareCart($this->getQuote());
        }
        $sourceUrl = $this->_pbridgeData->getGatewayFormUrl($requestParams, $this->getQuote());
        return $sourceUrl;
    }

    /**
     * Whether 3D Secure validation enabled for payment
     *
     * @return bool
     */
    public function is3dSecureEnabled()
    {
        if ($this->hasMethod() && $this->getMethod()->is3dSecureEnabled()) {
            return true;
        }
        return false;
    }

    /**
     * Overwtite iframe element height if 3D validation enabled
     *
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
