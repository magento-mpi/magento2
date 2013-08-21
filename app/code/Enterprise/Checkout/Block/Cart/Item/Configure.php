<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cart Item Configure block
 * Updates templates and blocks to show 'Update Cart' button and set right form submit url
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Block_Cart_Item_Configure extends Magento_Core_Block_Template
{
    /**
     * Configure product view blocks
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        // Set custom submit url route for form - to submit updated options to cart
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
             $block->setSubmitRouteData(array(
                'route' => 'checkout/cart/updateFailedItemOptions',
                'params' => array(
                    'id' => $this->getRequest()->getParam('id'),
                    'sku' => $this->getRequest()->getParam('sku')
                )
             ));
        }

        return parent::_prepareLayout();
    }
}
