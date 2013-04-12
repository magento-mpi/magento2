<?php
/**
 * Abstract API service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Service_Abstract
{
    /** @var Mage_Core_Service_Manager */
    protected $_serviceManager;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    public function __construct(Mage_Core_Service_Manager $manager, Magento_ObjectManager $objectManager)
    {
        $this->_serviceManager = $manager;
        $this->_objectManager  = $objectManager;
    }

    /**
     * Call service method
     *
     * @param string $serviceClass
     * @param string $serviceMethod
     * @param mixed $context [optional]
     * @param mixed $responseSchema [optional]
     * @return mixed (service execution response)
     */
    final public function call($serviceMethod, $context = null, $responseSchema = null)
    {
        // using get_class() will force to use custom definitions in case if the service class was rewritten which is make sense
        return $this->_serviceManager->call(get_class($this), $serviceMethod, $context, $responseSchema);
    }

    /**
     * Returns unique service identifier.
     *
     * @return string
     */
    abstract protected function _getServiceId();

    /**
     * Returns unique service method identifier.
     *
     * @param string $methodName
     * @return string
     */
    public function getMethodId($methodName)
    {
        return sprintf('%s/%s', $this->_getServiceId(), $methodName);
    }
}
