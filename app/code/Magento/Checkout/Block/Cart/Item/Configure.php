<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Cart\Item;

/**
 * Cart Item Configure block
 * Updates templates and blocks to show 'Update Cart' button and set right form submit url
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @module     Checkout
 */
class Configure extends \Magento\View\Element\Template
{

    /**
     * Configure product view blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        // Set custom submit url route for form - to submit updated options to cart
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
             $block->setSubmitRouteData(array(
                'route' => 'checkout/cart/updateItemOptions',
                'params' => array('id' => $this->getRequest()->getParam('id'))
             ));
        }

        return parent::_prepareLayout();
    }
}
