<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Plugin;

class CheckoutSession
{
    /** @var  \Magento\Checkout\Model\Session */
    protected $session;

    /**
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(\Magento\Checkout\Model\Session $session)
    {
        $this->session = $session;
    }

    /**
     * Interceptor for ClearHelperData method
     *
     * @param \Magento\Checkout\Model\Session $result
     * @return \Magento\Checkout\Model\Session
     */
    public function afterClearHelperData(\Magento\Checkout\Model\Session $result)
    {
        $this->session->unsLastRecurringPaymentIds();
        return $result;
    }
}
