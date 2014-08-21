<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule1\Service\V1;

use Magento\TestModule1\Service\V1\Entity\Item;
use Magento\TestModule1\Service\V1\Entity\ItemBuilder;

class AllSoapAndRest implements \Magento\TestModule1\Service\V1\AllSoapAndRestInterface
{
    /**
     * @var ItemBuilder
     */
    protected $itemBuilder;

    /**
     * @param ItemBuilder $itemBuilder
     */
    public function __construct(ItemBuilder $itemBuilder)
    {
        $this->itemBuilder = $itemBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function item($itemId)
    {
        return $this->itemBuilder->setItemId($itemId)->setName('testProduct1')->create();
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        $result1 = $this->itemBuilder->setItemId(1)->setName('testProduct1')->create();
        $result2 = $this->itemBuilder->setItemId(2)->setName('testProduct2')->create();

        return [$result1, $result2];
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        return $this->itemBuilder->setItemId(rand())->setName($name)->create();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Item $item)
    {
        return $this->itemBuilder->setItemId($item->getItemId())->setName('Updated'.$item->getName())->create();
    }

    public function testOptionalParam($name = null)
    {
        if (is_null($name)) {
            return $this->itemBuilder->setItemId(3)->setName('No Name')->create();
        } else {
            return $this->itemBuilder->setItemId(3)->setName($name)->create();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function itemAnyType($item)
    {
        return $item;
    }
}
