<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cart Item Configure block
 * Updates templates and blocks to show 'Update Cart' button and set right form submit url
 *
 */
namespace Magento\AdvancedCheckout\Block\Cart\Item;

class Configure extends \Magento\Framework\View\Element\Template
{
    /**
     * Configure product view blocks
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        // Set custom submit url route for form - to submit updated options to cart
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
            $block->setSubmitRouteData(
                array(
                    'route' => 'checkout/cart/updateFailedItemOptions',
                    'params' => array(
                        'id' => $this->getRequest()->getParam('id'),
                        'sku' => $this->getRequest()->getParam('sku')
                    )
                )
            );
        }

        return parent::_prepareLayout();
    }
}
