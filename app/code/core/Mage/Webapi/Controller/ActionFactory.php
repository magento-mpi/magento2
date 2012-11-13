<?php
/**
 * Factory of web API action controllers (resources).
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_ActionFactory
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
     * @param Mage_Webapi_Controller_Request $request
     * @return Mage_Webapi_Controller_ActionAbstract
     * @throws InvalidArgumentException
     */
    public function createActionController($className, $request)
    {
        $actionController = $this->_objectManager->create($className, array('request' => $request));
        if (!$actionController instanceof Mage_Webapi_Controller_ActionAbstract) {
            throw new InvalidArgumentException(
                'The specified class is not valid API action controller.');
        }
        return $actionController;
    }
}
