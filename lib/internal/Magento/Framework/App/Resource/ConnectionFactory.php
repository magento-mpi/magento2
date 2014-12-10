<?php
/**
 * Connection adapter factory
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\App\Resource;

use Magento\Framework\Model\Resource\Type\Db\ConnectionFactory as ModelConnectionFactory;

class ConnectionFactory extends ModelConnectionFactory
{
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
        /** @var \Magento\Framework\App\CacheInterface $cache */
        $cache = $this->objectManager->get('Magento\Framework\App\CacheInterface');
        $connection->setCacheAdapter($cache->getFrontend());

        return $connection;
    }
}
