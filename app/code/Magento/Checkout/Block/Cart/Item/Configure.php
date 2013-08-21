<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cart Item Configure block
 * Updates templates and blocks to show 'Update Cart' button and set right form submit url
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @module     Checkout
 */
class Magento_Checkout_Block_Cart_Item_Configure extends Magento_Core_Block_Template
{

    /**
     * Configure product view blocks
     *
     * @return Magento_Checkout_Block_Cart_Item_Configure
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
