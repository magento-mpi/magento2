<?php
/**
 * Factory of web API action controllers (resources).
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Action_Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create front controller instance.
     *
     * @param string $className
     * @param Magento_Webapi_Controller_Request $request
     * @return Magento_Webapi_Controller_ActionAbstract
     * @throws InvalidArgumentException
     */
    public function createActionController($className, $request)
    {
        $actionController = $this->_objectManager->create($className, array('request' => $request));
        if (!$actionController instanceof Magento_Webapi_Controller_ActionAbstract) {
            throw new InvalidArgumentException(
                'The specified class is not a valid API action controller.');
        }
        return $actionController;
    }
}
