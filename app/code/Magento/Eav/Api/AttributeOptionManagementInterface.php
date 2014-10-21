<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

/**
 * Interface WriteServiceInterface
 * instead of
 * @see \Magento\Catalog\Service\V1\Product\Attribute\Option\ReadServiceInterface
 * @see \Magento\Catalog\Service\V1\Product\Attribute\Option\WriteServiceInterface
 */
interface AttributeOptionManagementInterface
{
    /**
     * Add option to attribute
     *
     * @see \Magento\Catalog\Service\V1\Product\Attribute\Option\WriteServiceInterface::addOption
     *
     * @param string $attributeCode
     * @param int $entityType
     * @param \Magento\Eav\Api\Data\AttributeOptionInterface $option
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     */
    public function add($attributeCode, $entityType, $option);

    /**
     * Delete option from attribute
     *
     * @see \Magento\Catalog\Service\V1\Product\Attribute\Option\WriteServiceInterface::removeOption
     *
     * @param int $entityType
     * @param string $attributeCode
     * @param \Magento\Eav\Api\Data\AttributeOptionInterface $option
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return bool
     */
    public function delete($entityType, $attributeCode, $option);

    /**
     * Retrieve list of attribute options
     *
     * @see \Magento\Catalog\Service\V1\Product\Attribute\Option\ReadService::options
     * @param int $entityType
     * @param string $attributeCode
     * @return \Magento\Eav\Api\Data\AttributeOptionInterface[]
     */
    public function getItems($entityType, $attributeCode);
}
