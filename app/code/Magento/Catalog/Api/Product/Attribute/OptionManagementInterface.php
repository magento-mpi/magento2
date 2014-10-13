<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Product\Attribute;

/**
 * Interface WriteServiceInterface
 * @todo move to Eav\Api\AttributeOptionManagementInterface
 */
interface OptionManagementInterface
{
    /**
     * Add option to attribute
     *
     * @see \Magento\Catalog\Service\V1\Product\Attribute\Option\WriteServiceInterface::addOption
     *
     * @param string $attributeId
     * @param \Magento\Catalog\Api\Data\Eav\AttributeOptionInterface $option
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     * @todo add($entityType, $attributeCode)
     */
    public function add($attributeId, \Magento\Eav\Api\Data\AttributeOptionInterface $option);

    /**
     * Delete option from attribute
     *
     * @see \Magento\Catalog\Service\V1\Product\Attribute\Option\WriteServiceInterface::removeOption
     *
     * @param string $attributeId
     * @param int $optionId
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return bool
     * @todo remove($entityType, $attributeCode, $optionId)
     */
    public function remove($attributeId, $optionId);

    /**
     * Retrieve list of attribute options
     *
     * instead of \Magento\Catalog\Service\V1\Product\Attribute\Option\ReadService::options
     * @param string $id
     * @return \Magento\Eav\Api\Data\AttributeOptionInterface[]
     * @todo getItems($entityType, $attributeCode)
     */
    public function getItems($id);
}
