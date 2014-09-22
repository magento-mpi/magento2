<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Service\V1;

interface WrappingWriteInterface
{
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
     * @param int $id
     * @param \Magento\GiftWrapping\Service\V1\Data\Wrapping $data
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     */
    public function update($id, Data\Wrapping $data);

    /**
     * Delete gift wrapping
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     */
    public function delete($id);
}
