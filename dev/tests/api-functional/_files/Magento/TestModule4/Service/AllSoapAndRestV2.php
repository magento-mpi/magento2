<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service;

class AllSoapAndRestV2 /**  extends AllSoapAndRestV1 */ implements AllSoapAndRestV2Interface
{
    /**
     * @inheritdoc
     */
    public function item($itemId)
    {
        //TODO: Fix exception
        if ($itemId == null) {
            throw new \Magento\Webapi\Exception("Invalid Id");
        }
        $allSoapAndRest = new Entity\V2\AllSoapAndRest();
        $allSoapAndRest->setId($itemId);
        $allSoapAndRest->setName('testItemName');
        $allSoapAndRest->setPrice(1);
        return $allSoapAndRest;
    }

    /**
     * @inheritdoc
     */
    public function items()
    {
        $result = array();
        $allSoapAndRest1 = new Entity\V2\AllSoapAndRest();
        $allSoapAndRest1->setId(1);
        $allSoapAndRest1->setName('testProduct1');
        $allSoapAndRest1->setPrice(1);
        $allSoapAndRest2 = new Entity\V2\AllSoapAndRest();
        $allSoapAndRest2->setId(2);
        $allSoapAndRest2->setPrice(1);
        $allSoapAndRest2->setName('testProduct2');
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function create(Entity\V2\AllSoapAndRest $item)
    {
        $item->setId(rand());
        return $item;
    }

    /**
     * @inheritdoc
     */
    public function update(Entity\V2\AllSoapAndRest $item)
    {
        $item->setName('Updated' + $item->getName());
        return $item;
    }
    /**
     * @param string $itemId
     * @return Entity\V2\AllSoapAndRest
     * @throws \Magento\Webapi\Exception
     */
    public function delete($itemId)
    {
        if ($itemId == null) {
            throw new \Magento\Webapi\Exception("Invalid Id");
        }

        $allSoapAndRest = new Entity\V2\AllSoapAndRest();
        $allSoapAndRest->setId($itemId);
        $allSoapAndRest->setName('testProduct');
        $allSoapAndRest->setPrice(1);
        return $allSoapAndRest;
    }
}
