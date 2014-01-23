<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin;

interface HandlerInterface
{
    public function handle(\Magento\Catalog\Model\Product $product);
} 
