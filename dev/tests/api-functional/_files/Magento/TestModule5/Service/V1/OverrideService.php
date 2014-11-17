<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service\V1;

use Magento\TestModule5\Service\V1\Entity\AllSoapAndRestBuilder;

class OverrideService implements OverrideServiceInterface
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
     * {@inheritdoc}
     */
    public function scalarUpdate($entityId, $name, $hasOrders)
    {
        return $this->builder
            ->setEntityId($entityId)
            ->setName($name)
            ->setHasOrders($hasOrders)
            ->create();
    }
}

