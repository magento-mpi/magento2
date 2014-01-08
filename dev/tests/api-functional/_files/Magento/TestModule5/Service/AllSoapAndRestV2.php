<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service;

use Magento\TestModule5\Service\Entity\V2\AllSoapAndRestBuilder;

class AllSoapAndRestV2 implements AllSoapAndRestV2Interface
{
    /**
     * @inheritdoc
     */
    public function item($itemId)
    {
        return (new AllSoapAndRestBuilder())->setPrice(1)->setId($itemId)->setName('testItemName')->create();
    }

    /**
     * @inheritdoc
     */
    public function items()
    {
        $allSoapAndRest1 = (new AllSoapAndRestBuilder())->setPrice(1)->setId(1)->setName('testProduct1')->create();
        $allSoapAndRest2 = (new AllSoapAndRestBuilder())->setPrice(1)->setId(2)->setName('testProduct2')->create();
        return [$allSoapAndRest1, $allSoapAndRest2];
    }

    /**
     * @inheritdoc
     */
    public function create(Entity\V2\AllSoapAndRest $item)
    {
        return (new AllSoapAndRestBuilder())->populate($item)->create();
    }

    /**
     * @inheritdoc
     */
    public function update(Entity\V2\AllSoapAndRest $item)
    {
        $item->setName('Updated' . $item->getName());
        return (new AllSoapAndRestBuilder())->populate($item)->create();
    }

    /**
     * @param string $itemId
     * @return Entity\V2\AllSoapAndRest
     * @throws \Magento\Webapi\Exception
     */
    public function delete($itemId)
    {
        return (new AllSoapAndRestBuilder())->setPrice(1)->setId($itemId)->setName('testItemName')->create();
    }
}
