<?php
/**
 * Connection adapter factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Model\Resource\Type\Db;

use Magento\Framework\App\Resource\ConnectionAdapterInterface;
use Magento\Framework\DB\LoggerInterface;

class ConnectionFactory
{
    /**
     * @var string
     */
    private $adapterClass = 'Magento\Framework\Model\Resource\Type\Db\Pdo\Mysql';
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
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
        $adapterClass = isset($connectionConfig['adapter']) ? $connectionConfig['adapter'] : $this->adapterClass;
        $adapterInstance = new $adapterClass(
            new \Magento\Framework\Stdlib\String,
            new \Magento\Framework\Stdlib\DateTime,
            $connectionConfig
        );

        if (!$adapterInstance instanceof ConnectionAdapterInterface) {
            throw new \InvalidArgumentException("Trying to create wrong connection adapter '$this->adapterClass'");
        }

        return $adapterInstance->getConnection($this->logger);
    }
}
