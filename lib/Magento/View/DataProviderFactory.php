<?php

namespace Magento\View;

use Magento\ObjectManager;

class DataProviderFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @param array $arguments
     * @return DataProvider
     */
    public function create($className, array $arguments = array())
    {
        return $this->objectManager->create($className, $arguments);
    }
}
