<?php
/**
 * Connection adapter factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource;

class ConnectionFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\Config\Local
     */
    protected $_localConfig;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Config\Local $localConfig
     */
    public function __construct(\Magento\ObjectManager $objectManager, \Magento\Core\Model\Config\Local $localConfig)
    {
        $this->_objectManager = $objectManager;
        $this->_localConfig = $localConfig;
    }

    /**
     * Create connection adapter instance
     *
     * @param string $connectionName
     * @return \Magento\DB\Adapter\AdapterInterface
     * @throws \InvalidArgumentException
     */
    public function create($connectionName)
    {
        $connectionConfig = $this->_localConfig->getConnection($connectionName);
        if (!$connectionConfig || !isset($connectionConfig['active']) || !$connectionConfig['active']) {
            return null;
        }

        if (!isset($connectionConfig['adapter'])) {
            throw new \InvalidArgumentException('Adapter is not set for connection "' . $connectionName . '"');
        }

        $adapterInstance = $this->_objectManager->create($connectionConfig['adapter'], $connectionConfig);

        if (!($adapterInstance instanceof \Magento\Core\Model\Resource\ConnectionAdapterInterface)) {
            throw new \InvalidArgumentException('Trying to create wrong connection adapter');
        }

        return $adapterInstance->getConnection();
    }
}
