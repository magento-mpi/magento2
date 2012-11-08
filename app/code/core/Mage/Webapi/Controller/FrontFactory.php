<?php
/**
 * Front controller factory.
 *
 * @copyright {copyright}
 */
class Mage_Webapi_Controller_FrontFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create front controller instance.
     *
     * @param string $className
     * @return Mage_Webapi_Controller_FrontAbstract
     * @throws InvalidArgumentException
     */
    public function createFrontController($className)
    {
        $frontController = $this->_objectManager->create($className);
        if (!$frontController instanceof Mage_Core_Controller_FrontInterface) {
            throw new InvalidArgumentException(
                'The specified class does not implement front controller interface.');
        }
        return $frontController;
    }
}
