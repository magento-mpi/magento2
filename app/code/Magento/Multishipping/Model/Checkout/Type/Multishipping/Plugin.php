<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Model\Checkout\Type\Multishipping;

class Plugin
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var string
     */
    protected $checkoutStateBegin;


    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(\Magento\Checkout\Model\Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
        $this->checkoutStateBegin = $checkoutSession::CHECKOUT_STATE_BEGIN;
    }

    /**
     * Map STEP_SELECT_ADDRESSES to Cart::CHECKOUT_STATE_BEGIN
     * @param \Magento\Checkout\Model\Cart
     * @return void
     */
    public function beforeInit(\Magento\Checkout\Model\Cart $subject)
    {
        if ($this->checkoutSession->getCheckoutState() === State::STEP_SELECT_ADDRESSES) {
            $this->checkoutSession->setCheckoutState($this->checkoutStateBegin);
        }
    }
}
