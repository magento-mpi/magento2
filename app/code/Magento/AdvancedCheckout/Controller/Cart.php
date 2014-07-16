<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise checkout cart controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Controller;

class Cart extends \Magento\Framework\App\Action\Action implements \Magento\Catalog\Controller\Product\View\ViewInterface
{
    /**
     * Get failed items cart model instance
     *
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    protected function _getFailedItemsCart()
    {
        return $this->_objectManager->get(
            'Magento\AdvancedCheckout\Model\Cart'
        )->setContext(
            \Magento\AdvancedCheckout\Model\Cart::CONTEXT_FRONTEND
        );
    }
}
