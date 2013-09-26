<?php
/**
 * Connection adapter factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Resource_ConnectionFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Config_Local
     */
    protected $_localConfig;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Config_Local $localConfig
     */
    public function __construct(Magento_ObjectManager $objectManager, Magento_Core_Model_Config_Local $localConfig)
    {
        $this->_objectManager = $objectManager;
        $this->_localConfig = $localConfig;
    }

    /**
     * Create connection adapter instance
     *
     * @param string $connectionName
     * @return Magento_DB_Adapter_Interface
     * @throws InvalidArgumentException
     */
    public function create($connectionName)
    {
        $connectionConfig = $this->_localConfig->getConnection($connectionName);
        if (!$connectionConfig || !isset($connectionConfig['active']) || !$connectionConfig['active']) {
            return null;
        }

        if (!isset($connectionConfig['adapter'])) {
            throw new InvalidArgumentException('Adapter is not set for connection "' . $connectionName . '"');
        }

        $adapterInstance = $this->_objectManager->create($connectionConfig['adapter'], $connectionConfig);

        if (!($adapterInstance instanceof Magento_Core_Model_Resource_ConnectionAdapterInterface)) {
            throw new InvalidArgumentException('Trying to create wrong connection adapter');
        }

        return $adapterInstance->getConnection();
    }
}
