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
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory
     */
    protected $setCollectionFactory;

    /**
     * @var Data\Eav\AttributeSetBuilder
     */
    protected $attributeSetBuilder;

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
    ) {
        $this->setFactory = $setFactory;
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

        $constructorData = array(
            'attribute_set_name' => $setData->getName(),
            'sort_order' => $setData->getSortOrder(),
            'entity_type_id' => 4,
        );

        /** @var \Magento\Eav\Model\Entity\Attribute\Set $set */
        $set = $this->setFactory->create();
        foreach($constructorData as $key => $value) {
            $set->setData($key, $value);
        }
        if (!$set->validate()) {
            throw new \InvalidArgumentException('Invalid attribute set');
        }
        $set->save();

        return $set->getId();
    }



}
