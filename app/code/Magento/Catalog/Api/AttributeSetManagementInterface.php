<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api;

/**
 * Interface AttributeSetManagement must be implemented in new model AttributeSetManagement
 */
interface AttributeSetManagementInterface
{
    /**
     * @param int $attributeSetId
     * @param \Magento\Catalog\Api\Data\AttributeInterface $data
     * @return int
     */
    public function addAttribute($attributeSetId, \Magento\Catalog\Api\Data\AttributeInterface $data);

    /**
     * Remove attribute from attribute set
     *
     * @param string $attributeSetId
     * @param string $attributeId
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     */
    public function deleteAttribute($attributeSetId, $attributeId);

    /**
     * Retrieve related attributes based on given attribute set ID
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Api\Data\AttributeInterface[]
     */
    public function getAttributeList($attributeSetId);
}
