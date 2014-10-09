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
 */
interface OptionManagementInterface
{
    /**
     * Add option to attribute
     *
     * instead of \Magento\Catalog\Service\V1\Product\Attribute\Option\WriteServiceInterface::addOption
     * @param string $attributeId
     * @param \Magento\Catalog\Api\Data\Product\Attribute\OptionInterface $option
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     */
    public function addOption($attributeId, \Magento\Catalog\Api\Data\Product\Attribute\OptionInterface $option);

    /**
     * Delete option from attribute
     *
     * instead of \Magento\Catalog\Service\V1\Product\Attribute\Option\WriteServiceInterface::removeOption
     * @param string $attributeId
     * @param int $optionId
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return bool
     */
    public function removeOption($attributeId, $optionId);

    /**
     * Retrieve list of attribute options
     *
     * instead of \Magento\Catalog\Service\V1\Product\Attribute\Option\ReadService::options
     * @param string $id
     * @return \Magento\Catalog\Service\V1\Data\Eav\Option[]
     */
    public function getList($id);
}
