<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Model\Resource\Type\Db\Pdo;

use Magento\Framework\App\Resource\ConnectionAdapterInterface;
use Magento\Framework\Model\Resource\Type\Db;

class Mysql extends Db implements ConnectionAdapterInterface
{
    /**
     * @var \Magento\Framework\DB\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Stdlib\String
     */
    protected $string;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var array
     */
    protected $_connectionConfig;

    /**
     * @var string
     */
    protected $_initStatements;

    /**
     * @var boolean
     */
    protected $_isActive;

    /**
     * @param \Magento\Framework\DB\LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\String $string
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param array $config
     */
    public function __construct(
        \Magento\Framework\DB\LoggerInterface $logger,
        \Magento\Framework\Stdlib\String $string,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        array $config
    ) {
        $this->logger = $logger;
        $this->string = $string;
        $this->dateTime = $dateTime;
        $this->_connectionConfig = $this->getValidConfig($config);

        $this->_initStatements = $this->_connectionConfig['initStatements'];
        $this->_isActive = !($this->_connectionConfig['active'] === 'false'
            || $this->_connectionConfig['active'] === '0' || $this->_connectionConfig['active'] === false);
        parent::__construct();
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface|null
     */
    public function getConnection()
    {
        if (!$this->_isActive) {
            return null;
        }

        $connection = $this->_getDbAdapterInstance();
        if (!empty($this->_initStatements) && $connection) {
            $connection->query($this->_initStatements);
        }

        $profiler = $connection->getProfiler();
        if ($profiler instanceof \Magento\Framework\DB\Profiler) {
            $profiler->setType($this->_connectionConfig['host']);
            $profiler->setHost($this->_connectionConfig['type']);
        }

        return $connection;
    }

    /**
     * Create and return DB adapter object instance
     *
     * @return \Magento\Framework\DB\Adapter\Pdo\Mysql
     */
    protected function _getDbAdapterInstance()
    {
        $className = $this->_getDbAdapterClassName();
        $adapter = new $className($this->logger, $this->string, $this->dateTime, $this->_connectionConfig);
        return $adapter;
    }

    /**
     * Retrieve DB adapter class name
     *
     * @return string
     */
    protected function _getDbAdapterClassName()
    {
        return 'Magento\Framework\DB\Adapter\Pdo\Mysql';
    }

    /**
     * Validates the config and adds default options, if any is missing
     *
     * @param array $config
     * @return array
     */
    private function getValidConfig(array $config)
    {
        $default = ['initStatements' => 'SET NAMES utf8', 'type' => 'pdo_mysql', 'active' => false];
        foreach ($default as $key => $value) {
            if (!isset($config[$key])) {
                $config[$key] = $value;
            }
        }
        $required = ['host'];
        foreach ($required as $name) {
            if (!isset($config[$name])) {
                throw new \InvalidArgumentException("MySQL adapter: Missing required configuration option '$name'");
            }
        }
        return $config;
    }
}
