<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

use Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerInterface;
use Magento\Catalog\Model\Product;

class RecurringProfile implements HandlerInterface
{
    /**
     * Handle recurring profile data (replace it with original)
     *
     * @param Product $product
     * @return void
     */
    public function handle(Product $product)
    {
        $originalRecurringProfile = $product->getOrigData('recurring_profile');
        $product->setRecurringProfile($originalRecurringProfile);
    }
} 
