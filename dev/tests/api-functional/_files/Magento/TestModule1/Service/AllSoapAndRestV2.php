<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service;

use Magento\TestModule1\Service\Entity\V2\Item;

class AllSoapAndRestV2 implements \Magento\TestModule1\Service\AllSoapAndRestV2Interface
{
    /**
     * {@inheritdoc}
     */
    public function item($id)
    {
        $result = new Item();
        $result->setId($id);
        $result->setName('testProduct1');
        $result->setPrice('1');
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        $result1 = new Item();
        $result1->setId(1);
        $result1->setName('testProduct1');
        $result1->setPrice('1');

        $result2 = new Item();
        $result2->setId(2);
        $result2->setName('testProduct2');
        $result2->setPrice('2');

        return [$result1, $result2];
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        $result = new Item();
        $result->setId(rand());
        $result->setName($name);
        $result->setPrice('10');
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Item $item)
    {
        $result = new Item();
        $result->setId($item->getId());
        $result->setName('Updated'.$item->getName());
        $result->setPrice('5');
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $result = new Item();
        $result->setId($id);
        $result->setName('testProduct1');
        $result->setPrice('1');
        return $result;
    }
}
