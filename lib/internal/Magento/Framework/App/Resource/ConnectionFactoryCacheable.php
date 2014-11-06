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
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\DB\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\DB\LoggerInterface $logger
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
