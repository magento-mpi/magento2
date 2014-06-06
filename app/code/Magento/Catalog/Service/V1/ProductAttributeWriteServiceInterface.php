<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

/**
 * Class ProductAttributeWriteServiceInterface
 * @package Magento\Catalog\Service\V1
 */
interface ProductAttributeWriteServiceInterface
{
    /**
     * Delete Attribute
     *
     * @param int $id
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID is not found
     * @throws \Exception If something goes wrong during delete
     * @return bool True if the entity was deleted (always true)
     */
    public function remove($id);

    /**
     * Create attribute from data
     *
     * @param \Magento\Catalog\Service\V1\Data\Eav\Attribute $attribute
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Eav\Exception from validate()
     */
    public function create(\Magento\Catalog\Service\V1\Data\Eav\Attribute $attribute);
}
