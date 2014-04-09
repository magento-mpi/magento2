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
     * {@inheritdoc}
     */
    public function item($itemId)
    {
        return (new ItemBuilder())->setItemId($itemId)->setName('testProduct1')->create();
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        $result1 = (new ItemBuilder())->setItemId(1)->setName('testProduct1')->create();
        $result2 = (new ItemBuilder())->setItemId(2)->setName('testProduct2')->create();

        return [$result1, $result2];
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        return (new ItemBuilder())->setItemId(rand())->setName($name)->create();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Item $item)
    {
        return (new ItemBuilder())
            ->setItemId($item->getItemId())->setName('Updated'.$item->getName())->create();
    }

    public function testOptionalParam($name = null)
    {
        if (is_null($name)) {
            return (new ItemBuilder())->setId(3)->setName('No Name')->create();
        } else {
            return (new ItemBuilder())->setId(3)->setName($name)->create();
        }
    }
}
