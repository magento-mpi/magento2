<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service;

class AllSoapAndRestV1 implements \Magento\TestModule5\Service\AllSoapAndRestV1Interface
{
    /**
     * @inheritdoc
     */
    public function item($itemId)
    {
        $allSoapAndRest = new Entity\V1\AllSoapAndRest();
        $allSoapAndRest->setId($itemId);
        $allSoapAndRest->setName('testItemName');
        return $allSoapAndRest;
    }

    /**
     * @inheritdoc
     */
    public function items()
    {
        $allSoapAndRest1 = new Entity\V1\AllSoapAndRest();
        $allSoapAndRest1->setId(1);
        $allSoapAndRest1->setName('testProduct1');
        $allSoapAndRest2 = new Entity\V1\AllSoapAndRest();
        $allSoapAndRest2->setId(2);
        $allSoapAndRest2->setName('testProduct2');
        return [$allSoapAndRest1, $allSoapAndRest2];
    }

    /**
     * @inheritdoc
     */
    public function create(Entity\V1\AllSoapAndRest $item)
    {
        $item->setId(rand());
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function update(Entity\V1\AllSoapAndRest $item)
    {
        $item->setName('Updated' . $item->getName());
        return $item;
    }
}
