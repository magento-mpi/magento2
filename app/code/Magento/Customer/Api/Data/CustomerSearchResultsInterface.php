<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api\Data;

/**
 * Interface for customer search results.
 */
interface CustomerSearchResultsInterface extends \Magento\Framework\Api\Data\SearchResultsInterface
{
    /**
     * Get customers list.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     */
    public function getItems();
}
