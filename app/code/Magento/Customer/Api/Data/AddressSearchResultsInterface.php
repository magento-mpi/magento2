<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api\Data;

/**
 * Interface for customer address search results.
 */
interface AddressSearchResultsInterface extends \Magento\Framework\Api\Data\SearchResultsInterface
{
    /**
     * Get customer addresses list.
     *
     * @return \Magento\Customer\Api\Data\AddressInterface[]
     */
    public function getItems();
}
