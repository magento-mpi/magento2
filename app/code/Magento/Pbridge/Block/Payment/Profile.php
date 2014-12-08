<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Pbridge\Block\Payment;

class Profile extends \Magento\Pbridge\Block\Iframe\AbstractIframe
{
    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '600';

    /**
     * Getter for Payment Profiles Iframe source URL.
     * Return Payment Bridge url with required parameters (such as merchant code, merchant key etc.)
     * Can include quote shipping and billing address if its required in payment processing
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->_pbridgeData->getPaymentProfileUrl(
            [
                'billing_address' => $this->_getAddressInfo(),
                'css_url' => $this->getCssUrl(),
                'customer_id' => $this->getCustomerIdentifier(),
                'customer_name' => $this->getCustomerName(),
                'customer_email' => $this->getCustomerEmail(),
            ]
        );
    }
}
