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

class ConnectionFactoryCacheable extends ConnectionFactory
{
    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * @param \Magento\Framework\DB\LoggerInterface $logger
     * @param \Magento\Framework\App\CacheInterface $cache
     */
    public function __construct(
        \Magento\Framework\DB\LoggerInterface $logger,
        \Magento\Framework\App\CacheInterface $cache
    ) {
        parent::__construct($logger);
        $this->cache = $cache;
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
        $connection = parent::create($connectionConfig);
        $connection->setCacheAdapter($this->cache->getFrontend());

        return $connection;
    }
}
