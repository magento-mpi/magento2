<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Block\Item\Price\OrderItem;

/**
 * Order item price render block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Renderer extends \Magento\Tax\Block\Item\Price\Renderer
{
    /**
     * Format price using order currency
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getItem()->getOrder()->formatPrice($price);
    }
}
