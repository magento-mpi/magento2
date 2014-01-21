<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Checkout\Block\Cart;

class Coupon extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        parent::__construct($context, $catalogData, $customerSession, $checkoutSession, $data);
        $this->_isScopePrivate = true;
    }

    public function getCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }


}
