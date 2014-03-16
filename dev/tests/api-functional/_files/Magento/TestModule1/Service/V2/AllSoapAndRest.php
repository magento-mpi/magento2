<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service\V2;

use Magento\TestModule1\Service\V2\Entity\ItemBuilder;
use Magento\TestModule1\Service\V2\Entity\Item;

class AllSoapAndRest implements \Magento\TestModule1\Service\V2\AllSoapAndRestInterface
{
    /**
     * {@inheritdoc}
     */
    public function item($id)
    {
        return (new ItemBuilder())->setId($id)->setName('testProduct1')->setPrice('1')->create();
    }

    /**
     * {@inheritdoc}
     */
    public function items($filters = array(), $sortOrder = 'ASC')
    {
        $result = [];
        $firstItem = (new ItemBuilder())->setId(1)->setName('testProduct1')->setPrice('1')->create();
        $secondItem = (new ItemBuilder())->setId(2)->setName('testProduct2')->setPrice('2')->create();

        /** Simple filtration implementation */
        if (!empty($filters)) {
            /** @var \Magento\Service\V1\Data\Filter $filter */
            foreach ($filters as $filter) {
                if ('id' == $filter->getField() && $filter->getValue() == 1) {
                    $result[] = $firstItem;
                } elseif ('id' == $filter->getField() && $filter->getValue() == 2) {
                    $result[] = $secondItem;
                }
            }
        } else {
            /** No filter is specified. */
            $result = [$firstItem, $secondItem];
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name)
    {
        return (new ItemBuilder())->setId(rand())->setName($name)->setPrice('10')->create();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Item $item)
    {
        return (new ItemBuilder())->setId($item->getId())->setName('Updated'.$item->getName())->setPrice('5')->create();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return (new ItemBuilder())->setId($id)->setName('testProduct1')->setPrice('1')->create();
    }
}
