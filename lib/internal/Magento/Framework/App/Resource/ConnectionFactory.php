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
     * @var \Magento\Framework\DB\LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\DB\LoggerInterface $logger
     */
    public function __construct(\Magento\Framework\DB\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Create connection adapter instance
     *
     * @param array $connectionConfig
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \InvalidArgumentException
     */
    public function create(array $connectionConfig)
    {
        if (!$connectionConfig || !isset($connectionConfig['active']) || !$connectionConfig['active']) {
            return null;
        }

        if (!isset($connectionConfig['adapter'])) {
            throw new \InvalidArgumentException('Adapter is not set for connection');
        }

        $adapterClass = $connectionConfig['adapter'];

        $adapterInstance = new $adapterClass(
            $this->logger,
            new \Magento\Framework\Stdlib\String,
            new \Magento\Framework\Stdlib\DateTime,
            $connectionConfig
        );

        if (!$adapterInstance instanceof ConnectionAdapterInterface) {
            throw new \InvalidArgumentException("Trying to create wrong connection adapter '$adapterClass'");
        }

        return $adapterInstance->getConnection();
    }
}
