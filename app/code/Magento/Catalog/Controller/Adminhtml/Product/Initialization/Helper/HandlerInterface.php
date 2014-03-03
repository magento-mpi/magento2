<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper;

interface HandlerInterface
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function handle(\Magento\Catalog\Model\Product $product);
} 
