<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Cache_InstanceFactory
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
     * Get cache instance model
     *
     * @param string $instanceName
     * @return Magento_Cache_FrontendInterface
     * @throws UnexpectedValueException
     */
    public function get($instanceName)
    {
        $instance =  $this->_objectManager->get($instanceName);
        if (!($instance instanceof Magento_Cache_FrontendInterface)) {
            throw new UnexpectedValueException("Cache type class '$instanceName' has to be a cache frontend.");
        }

        return $instance;
    }
}
