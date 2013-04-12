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
