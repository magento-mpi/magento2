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

class NoWebApiXml implements \Magento\TestModule2\Service\V1\NoWebApiXmlInterface
{
    /**
     * {@inheritdoc}
     */
    public function item($id)
    {
        return (new ItemBuilder())->setId($id)->setName('testProduct1')->create();
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        $result1 = (new ItemBuilder())->setId(1)->setName('testProduct1')->create();

        $result2 = (new ItemBuilder())->setId(2)->setName('testProduct2')->create();

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
}
