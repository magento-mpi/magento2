<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data\Product\Attribute;

/**
 * SearchResults Service Data Object used for the search service requests
 *
 * @codeCoverageIgnore
 */
class SearchResults extends \Magento\Framework\Api\SearchResults
{
    /**
     * Get items
     *
     * @return \Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata[]
     */
    public function getItems()
    {
        return parent::getItems();
    }
}
