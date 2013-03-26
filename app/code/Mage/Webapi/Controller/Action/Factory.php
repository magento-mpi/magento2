<?php
/**
 * Factory of web API action controllers (resources).
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Action_Factory
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
     * @return object
     */
    public function createServiceInstance($className, $request)
    {
        return $this->_objectManager->create($className, array('request' => $request));
    }
}
