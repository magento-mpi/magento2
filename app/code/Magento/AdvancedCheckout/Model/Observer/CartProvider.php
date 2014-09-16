<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model\Observer;

use \Magento\AdvancedCheckout\Model\Cart;

class CartProvider
{
    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->_cart = $cart;
    }

    /**
     * Returns cart model for backend
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function get(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $observer->getRequestModel()->getParam('storeId');
        if (is_null($storeId) || $storeId === '') {
            $storeId = $observer->getRequestModel()->getParam('store_id');

            if (is_null($storeId) || $storeId === '') {
                $storeId = $observer->getSession()->getStoreId();
            }
        }
        return $this->_cart->setSession(
            $observer->getSession()
        )->setContext(
            Cart::CONTEXT_ADMIN_ORDER
        )->setCurrentStore(
            (int)$storeId
        );
    }
}
