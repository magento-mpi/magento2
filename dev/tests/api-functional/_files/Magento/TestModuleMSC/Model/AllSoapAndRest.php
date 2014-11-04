<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModuleMSC\Model;

use Magento\TestModuleMSC\Api\Data\CustomAttributeDataObjectDataBuilder;
use Magento\TestModuleMSC\Api\Data\ItemDataBuilder;
use Magento\Framework\Api\AttributeValueBuilder;

class AllSoapAndRest implements \Magento\TestModuleMSC\Api\AllSoapAndRestInterface
{
    /**
     * @var ItemDataBuilder
     */
    protected $itemDataBuilder;

    /**
     * @var CustomAttributeDataObjectDataBuilder
     */
    protected $customAttributeDataObjectDataBuilder;

    /**
     * @var AttributeValueBuilder
     */
    protected $valueBuilder;

    /**
     * @param ItemDataBuilder $itemDataBuilder
     * @param CustomAttributeDataObjectDataBuilder $customAttributeNestedDataObjectBuilder
     * @param AttributeValueBuilder $valueBuilder
     */
    public function __construct(
        ItemDataBuilder $itemDataBuilder,
        CustomAttributeDataObjectDataBuilder $customAttributeNestedDataObjectBuilder,
        AttributeValueBuilder $valueBuilder
    ) {
        $this->itemDataBuilder = $itemDataBuilder;
        $this->customAttributeDataObjectDataBuilder = $customAttributeNestedDataObjectBuilder;
        $this->valueBuilder = $valueBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function item($itemId)
    {
        return $this->itemDataBuilder->setItemId($itemId)->setName('testProduct1')->create();
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        $result1 = $this->itemDataBuilder->setItemId(1)->setName('testProduct1')->create();
        $result2 = $this->itemDataBuilder->setItemId(2)->setName('testProduct2')->create();

        return [$result1, $result2];
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        return $this->itemDataBuilder->setItemId(rand())->setName($name)->create();
    }

    /**
     * {@inheritdoc}
     */
    public function update(\Magento\TestModuleMSC\Api\Data\ItemInterface $entityItem)
    {
        return $this->itemDataBuilder->setItemId($entityItem->getItemId())
            ->setName('Updated' . $entityItem->getName())
            ->create();
    }

    public function testOptionalParam($name = null)
    {
        if (is_null($name)) {
            return $this->itemDataBuilder->setItemId(3)->setName('No Name')->create();
        } else {
            return $this->itemDataBuilder->setItemId(3)->setName($name)->create();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function itemAnyType(\Magento\TestModuleMSC\Api\Data\ItemInterface $entityItem)
    {
        return $entityItem;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreconfiguredItem()
    {
        $customAttributeInt = $this->valueBuilder
            ->setAttributeCode('custom_attribute_int')
            ->setValue(1)
            ->create();

        $customAttributeDataObject = $this->customAttributeDataObjectDataBuilder
            ->setName('nameValue')
            ->setCustomAttribute($customAttributeInt)
            ->create();

        $customAttributeDataObjectAttributeValue = $this->valueBuilder
            ->setAttributeCode('custom_attribute_data_object')
            ->setValue($customAttributeDataObject)
            ->create();

        $customAttributeStringAttributeValue = $this->valueBuilder
            ->setAttributeCode('custom_attribute_string')
            ->setValue('someStringValue')
            ->create();

        $item = $this->itemDataBuilder
            ->setItemId(1)
            ->setName('testProductAnyType')
            ->setCustomAttribute($customAttributeDataObjectAttributeValue)
            ->setCustomAttribute($customAttributeStringAttributeValue)
            ->create();

        return $item;
    }
}
