<?php
/**
 * Connection adapter factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ConnectionAdapterFactory
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
     * Create connection adapter instance
     *
     * @param string $connectionName
     * @return Magento_Core_Model_Resource_ConnectionAdapterInterface
     * @throws InvalidArgumentException
     */
    public function create($connectionName)
    {
        $adapterInstance = $this->_objectManager->create($connectionName);

        if (!($adapterInstance instanceof Magento_Core_Model_Resource_ConnectionAdapterInterface)) {
            throw new InvalidArgumentException('Trying to create wrong connection adapter');
        }

        return $adapterInstance;
    }
}
