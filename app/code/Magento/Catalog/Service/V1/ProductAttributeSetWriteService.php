<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Framework\Exception\InputException;

class ProductAttributeSetWriteService implements ProductAttributeSetWriteServiceInterface
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $setFactory;

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->setFactory = $setFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Create attribute set from data
     *
     * @param \Magento\Catalog\Service\V1\Data\Eav\AttributeSet $setData
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function create(\Magento\Catalog\Service\V1\Data\Eav\AttributeSet $setData)
    {
        if ($setData->getId()) {
            throw InputException::invalidFieldValue('id', $setData->getId());
        }

        $updateData = array(
            'attribute_set_name' => $setData->getName(),
            'sort_order' => $setData->getSortOrder(),
            'entity_type_id' => $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId(),
        );

        /** @var \Magento\Eav\Model\Entity\Attribute\Set $set */
        $set = $this->setFactory->create();
        foreach($updateData as $key => $value) {
            $set->setData($key, $value);
        }
        if (!$set->validate()) {
            throw new \InvalidArgumentException('Invalid attribute set');
        }
        $set->save();

        return $set->getId();
    }
}
