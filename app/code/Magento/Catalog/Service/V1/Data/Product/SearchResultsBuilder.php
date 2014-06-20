<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data\Product;

/**
 * Builder for the SearchResults Service Data Object
 */
class SearchResultsBuilder extends \Magento\Framework\Service\V1\Data\SearchResultsBuilder
{
    /**
     * Set items
     *
     * @param \Magento\Catalog\Service\V1\Data\Product[] $items
     * @return $this
     */
    public function setItems($items)
    {
        return parent::setItems($items);
    }
}
