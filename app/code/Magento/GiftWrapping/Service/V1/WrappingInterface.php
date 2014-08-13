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
     * @param int $id
     * @param int $storeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @return \Magento\GiftWrapping\Service\V1\Data\Wrapping
     */
    public function get($id, $storeId = null);

    /**
     * @param \Magento\GiftWrapping\Service\V1\Data\Wrapping $data
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return int
     */
    public function create(\Magento\GiftWrapping\Service\V1\Data\Wrapping $data);

    /**
     * @param Data\Wrapping $data
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     */
    public function update(Data\Wrapping $data);

    /**
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Service\V1\Data\SearchResults
     */
    public function search(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @throws \Exception If something goes wrong during delete
     * @return bool
     */
    public function delete($id);
}
