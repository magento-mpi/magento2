<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler;

use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\HandlerInterface;
use Magento\Catalog\Model\Product;

class DefaultHandler implements HandlerInterface
{
    /**
     * Handle loading of specific product data
     *
     * @param Product $product
     * @return void
     */
    public function handle(Product $product)
    {
        //Extension point for other product types
    }
} 
