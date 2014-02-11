<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Plugin on \Magento\Checkout\Model\Session
 */
namespace Magento\Paypal\Model\Checkout;

class SessionPlugin
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Reset Last Billing Agreement Id in checkout session
     *
     * @return array
     */
    public function beforeClearHelperData()
    {
        $this->_checkoutSession->setLastBillingAgreementId(null);
        return [];
    }
}
