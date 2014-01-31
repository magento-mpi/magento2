<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service\V1;

use Magento\TestModule5\Service\V1\Entity\AllSoapAndRestBuilder;

class AllSoapAndRest implements \Magento\TestModule5\Service\V1\AllSoapAndRestInterface
{
    /**
     * @inheritdoc
     */
    public function item($id)
    {
        return (new AllSoapAndRestBuilder())
            ->setId($id)
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
    public function create(\Magento\TestModule5\Service\V1\Entity\AllSoapAndRest $item)
    {
        return (new AllSoapAndRestBuilder())->populate($item)->create();
    }

    /**
     * @inheritdoc
     */
    public function update(\Magento\TestModule5\Service\V1\Entity\AllSoapAndRest $item)
    {
        $item->setName('Updated' . $item->getName());
        return (new AllSoapAndRestBuilder())->populate($item)->create();
    }
}
