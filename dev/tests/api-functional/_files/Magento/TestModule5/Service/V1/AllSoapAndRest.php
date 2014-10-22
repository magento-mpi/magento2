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
     * @var AllSoapAndRestBuilder
     */
    protected $builder;

    /**
     * @param AllSoapAndRestBuilder $builder
     */
    public function __construct(AllSoapAndRestBuilder $builder)
    {
        $this->builder = $builder;
    }
    
    /**
     * @inheritdoc
     */
    public function item($id)
    {
        return $this->builder
            ->setId($id)
            ->setName('testItemName')
            ->setIsEnabled(true)
            ->setHasOrders(true)
            ->create();
    }

    /**
     * @inheritdoc
     */
    public function items()
    {
        $allSoapAndRest1 = $this->builder->setId(1)->setName('testProduct1')->create();
        $allSoapAndRest2 = $this->builder->setId(2)->setName('testProduct2')->create();
        return [$allSoapAndRest1, $allSoapAndRest2];
    }

    /**
     * @inheritdoc
     */
    public function create(\Magento\TestModule5\Service\V1\Entity\AllSoapAndRest $item)
    {
        return $this->builder->populate($item)->create();
    }

    /**
     * @inheritdoc
     */
    public function update(\Magento\TestModule5\Service\V1\Entity\AllSoapAndRest $entityItem)
    {
        return $entityItem;
    }

    /**
     * @inheritdoc
     */
    public function nestedUpdate($firstId, $secondId, \Magento\TestModule5\Service\V1\Entity\AllSoapAndRest $entityItem)
    {
        return $entityItem;
    }
}
