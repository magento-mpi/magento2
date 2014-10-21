<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

class ProductLinkSearchResults extends \Magento\Framework\Service\V1\Data\SearchResults
{
    /**
     * Get items
     *
     * @return \Magento\Catalog\Api\Data\ProductLinkInterface[]
     */
    public function getItems()
    {
        return parent::getItems();
    }
}
