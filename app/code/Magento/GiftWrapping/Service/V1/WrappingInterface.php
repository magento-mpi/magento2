<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1;

interface WrappingInterface
{
    /**
     * Return data object for specified wrapping ID and store.
     *
     * @param int $id
     * @param int $storeId
     * @return \Magento\GiftWrapping\Service\V1\Data\Wrapping
     */
    public function get($id, $storeId = null);

    /**
     * Create new gift wrapping with data object values
     *
     * @param \Magento\GiftWrapping\Service\V1\Data\Wrapping $data
     * @return int
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     */
    public function create(Data\Wrapping $data);

    /**
     * Update existing gift wrapping with data object values
     *
     * @param \Magento\GiftWrapping\Service\V1\Data\Wrapping $data
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     */
    public function update(Data\Wrapping $data);

    /**
     * Return list of gift wrapping data objects based on search criteria
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Service\V1\Data\SearchResults
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Delete gift wrapping
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     */
    public function delete($id);
}
