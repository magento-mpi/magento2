<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
