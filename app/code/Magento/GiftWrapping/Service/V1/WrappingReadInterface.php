<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Service\V1;

interface WrappingReadInterface
{
    /**
     * Return data object for specified wrapping ID and store.
     *
     * @param int $id
     * @return \Magento\GiftWrapping\Service\V1\Data\Wrapping
     */
    public function get($id);

    /**
     * Return list of gift wrapping data objects based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\GiftWrapping\Service\V1\Data\WrappingSearchResults
     */
    public function search(\Magento\Framework\Api\SearchCriteria $searchCriteria);
}
