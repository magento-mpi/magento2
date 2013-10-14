<?php

class Magento_View_DataProviderFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $objectManager;

    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @param array $arguments
     * @return Magento_View_DataProvider
     */
    public function create($className, array $arguments = array())
    {
        return $this->objectManager->create($className, $arguments);
    }
}
