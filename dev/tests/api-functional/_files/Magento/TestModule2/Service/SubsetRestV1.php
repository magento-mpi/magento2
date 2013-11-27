<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule2\Service;

use Magento\TestModule2\Service\Entity\V1\Item;

class SubsetRestV1 implements \Magento\TestModule2\Service\SubsetRestV1Interface
{
    /**
     * {@inheritdoc}
     */
    public function item($id)
    {
        $item = new Item();
        $item->setId($id);
        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        $itemA = new Item();
        $itemA->setId('1');
        $itemA->setName('testItem1');
        $itemB = new Item();
        $itemB->setId('2');
        $itemB->setName('testItem2');
        return [$itemA, $itemB];
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        $item = new Item();
        return $item->setId(rand())->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Item $item)
    {
        return $item->setName($item->getName() . ' Updated');
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        $item = new Item();
        return $item->setId('1');
    }
}
