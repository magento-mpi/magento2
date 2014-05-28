<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Service\V1\Data\Eav\AttributeSet;

/**
 * Interface ProductAttributeSetWriteServiceInterface
 * Service interface to create/update/remove product attribute sets
 */
interface ProductAttributeSetWriteServiceInterface
{
    /**
     * Create attribute set from data
     *
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeSetExtended $attributeSet
     * @return int
     * @throws \Exception
     */
    public function create(\Magento\Catalog\Service\V1\Data\Eav\AttributeSetExtended $attributeSet);

    /**
     * Update attribute set data
     *
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeSet $attributeSetData
     * @return int attribute set ID
     * @throws \Magento\Framework\Model\Exception If attribute set is not found
     */
    public function update(AttributeSet $attributeSetData);

    /**
     * Remove attribute set by id
     *
     * @param int $id
     * @throws \InvalidArgumentException
     */
    public function remove($id);
}
