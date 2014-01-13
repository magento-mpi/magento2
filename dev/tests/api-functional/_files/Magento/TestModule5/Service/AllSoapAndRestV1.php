<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service;

use Magento\TestModule5\Service\Entity\V1\AllSoapAndRestBuilder;

class AllSoapAndRestV1 implements \Magento\TestModule5\Service\AllSoapAndRestV1Interface
{
    /**
     * @inheritdoc
     */
    public function item($itemId)
    {
        return (new AllSoapAndRestBuilder())
            ->setId($itemId)
            ->setName('testItemName')
            ->setIsEnabled(true)
            ->setHasName(true)
            ->create();
    }

    /**
     * @inheritdoc
     */
    public function items()
    {
        $allSoapAndRest1 = (new AllSoapAndRestBuilder())->setId(1)->setName('testProduct1')->create();
        $allSoapAndRest2 = (new AllSoapAndRestBuilder())->setId(2)->setName('testProduct2')->create();
        return [$allSoapAndRest1, $allSoapAndRest2];
    }

    /**
     * @inheritdoc
     */
    public function create(Entity\V1\AllSoapAndRest $item)
    {
        return (new AllSoapAndRestBuilder())->populate($item)->create();
    }

    /**
     * @inheritdoc
     */
    public function update(Entity\V1\AllSoapAndRest $item)
    {
        $item->setName('Updated' . $item->getName());
        return (new AllSoapAndRestBuilder())->populate($item)->create();
    }
}
