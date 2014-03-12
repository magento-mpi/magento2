<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\HandlerInterface;
use Magento\Catalog\Model\Product;

class RecurringPayment implements HandlerInterface
{
    /**
     * Handle recurring payment data (replace it with original)
     *
     * @param Product $product
     * @return void
     */
    public function handle(Product $product)
    {
        $originalRecurringPayment = $product->getOrigData('recurring_payment');
        $product->setRecurringPayment($originalRecurringPayment);
    }
} 
