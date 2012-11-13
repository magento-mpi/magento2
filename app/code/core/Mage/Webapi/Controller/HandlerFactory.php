<?php
/**
 * Factory of web API handlers.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_HandlerFactory
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
     * @return Mage_Webapi_Controller_HandlerAbstract
     * @throws InvalidArgumentException
     */
    public function create($className)
    {
        $frontController = $this->_objectManager->create($className);
        if (!$frontController instanceof Mage_Core_Controller_FrontInterface) {
            throw new InvalidArgumentException(
                'The specified class does not implement front controller interface.');
        }
        return $frontController;
    }
}
