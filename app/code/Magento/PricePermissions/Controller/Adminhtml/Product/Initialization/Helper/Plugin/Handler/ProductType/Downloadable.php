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
    /**
     * Handle data received from Downloadable Links tab of downloadable products
     *
     * @param \Magento\Catalog\Model\Product $product
     */
    public function handle(\Magento\Catalog\Model\Product $product)
    {
        if ($product->getTypeId() != \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
            return;
        }

        $downloadableData = $product->getDownloadableData();
        if (is_array($downloadableData) && isset($downloadableData['link'])) {
            /** @var \Magento\Downloadable\Model\Product\Type $type */
            $type = $product->getTypeInstance();
            $originalLinks = $type->getLinks($product);
            foreach ($downloadableData['link'] as &$downloadableDataItem) {
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
