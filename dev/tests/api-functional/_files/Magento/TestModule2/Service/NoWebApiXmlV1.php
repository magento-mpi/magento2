<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule2\Service;

use Magento\TestModule2\Service\Entity\V1\Item;

class NoWebApiXmlV1 implements \Magento\TestModule2\Service\NoWebApiXmlV1Interface
{
    /**
     * {@inheritdoc}
     */
    public function item($id)
    {
        $result = new Item();
        $result->setId($id);
        $result->setName('testProduct1');
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

        $result2 = new Item();
        $result2->setId(2);
        $result2->setName('testProduct2');

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
        return $result;
    }
}
