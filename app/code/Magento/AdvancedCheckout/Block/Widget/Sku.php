<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Order by SKU Widget Block
 *
 */
namespace Magento\AdvancedCheckout\Block\Widget;

class Sku extends \Magento\AdvancedCheckout\Block\Sku\AbstractSku implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Retrieve form action URL
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('checkout/cart/advancedAdd');
    }
}
