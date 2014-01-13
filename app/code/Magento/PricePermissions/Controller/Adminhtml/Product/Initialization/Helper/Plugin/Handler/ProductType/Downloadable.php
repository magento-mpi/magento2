<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\Handler\ProductType;

use Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin\HandlerInterface;

class Downloadable implements HandlerInterface
{
    public function handle(\Magento\Catalog\Model\Product $product)
    {
        // Handle data received from Downloadable Links tab of downloadable products
        if ($product->getTypeId() == \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {

            $downloadableData = $product->getDownloadableData();
            if (is_array($downloadableData) && isset($downloadableData['link'])) {
                $originalLinks = $product->getTypeInstance()->getLinks($product);
                foreach ($downloadableData['link'] as $id => &$downloadableDataItem) {
                    $linkId = $downloadableDataItem['link_id'];
                    if (isset($originalLinks[$linkId]) && !$downloadableDataItem['is_delete']) {
                        $originalLink = $originalLinks[$linkId];
                        $downloadableDataItem['price'] = $originalLink->getPrice();
                    } else {
                        // Set zero price for new links
                        $downloadableDataItem['price'] = 0;
                    }
                }
                $product->setDownloadableData($downloadableData);
            }
        }
    }
} 
