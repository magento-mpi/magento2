<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Controller\Checkout;

class Plugin
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @param \Magento\Checkout\Model\Cart $cart
     */
    public function __construct(\Magento\Checkout\Model\Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @param \Magento\Framework\App\Action\Action $subject
     */
    public function beforeExecute(\Magento\Framework\App\Action\Action $subject)
    {
        $this->cart->getQuote()->setIsMultiShipping(0);
    }
}
