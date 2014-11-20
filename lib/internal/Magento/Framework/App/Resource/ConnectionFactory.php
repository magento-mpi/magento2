<?php
/**
 * Connection adapter factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Resource;

class ConnectionFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Arguments
     */
    protected $_localConfig;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Arguments $localConfig
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Arguments $localConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_localConfig = $localConfig;
    }

    /**
     * Create connection adapter instance
     *
     * @param string $connectionName
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
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

        if (!$adapterInstance instanceof ConnectionAdapterInterface) {
            throw new \InvalidArgumentException('Trying to create wrong connection adapter');
        }

        return $adapterInstance->getConnection();
    }
}
