<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule2\Service\V1;

use Magento\TestModule2\Service\V1\Entity\ItemBuilder;
use Magento\TestModule2\Service\V1\Entity\Item;

class SubsetRest implements \Magento\TestModule2\Service\V1\SubsetRestInterface
{
    /**
     * {@inheritdoc}
     */
    public function item($id)
    {
        return (new ItemBuilder())->setId($id)->setName('testItem' . $id)->create();
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        $result1 = (new ItemBuilder())->setId(1)->setName('testItem1')->create();

        $result2 = (new ItemBuilder())->setId(2)->setName('testItem2')->create();

        return [$result1, $result2];
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        return (new ItemBuilder())->setId(rand())->setName($name)->create();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Item $item)
    {
        return (new ItemBuilder())->setId($item->getId())->setName('Updated'.$item->getName())->create();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        return (new ItemBuilder())->setId(1)->create();
    }
}
