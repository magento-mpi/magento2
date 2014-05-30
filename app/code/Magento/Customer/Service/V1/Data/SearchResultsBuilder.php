<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Builder for the SearchResults Service Data Object
 *
 * @method \Magento\Customer\Service\V1\Data\SearchResults create()
 */
class SearchResultsBuilder extends \Magento\Framework\Service\V1\Data\SearchResultsBuilder
{
    /**
     * Set items
     *
     * @param \Magento\Customer\Service\V1\Data\CustomerDetails[] $items
     * @return $this
     */
    public function setItems($items)
    {
        return parent::setItems($items);
    }
}
