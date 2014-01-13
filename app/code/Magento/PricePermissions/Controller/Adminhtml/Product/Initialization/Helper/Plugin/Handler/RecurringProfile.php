<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

use Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerInterface;

class RecurringProfile implements HandlerInterface
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     */
    public function handle(\Magento\Catalog\Model\Product $product)
    {
        // Handle recurring profile data (replace it with original)
        $originalRecurringProfile = $product->getOrigData('recurring_profile');
        $product->setRecurringProfile($originalRecurringProfile);
    }
} 
