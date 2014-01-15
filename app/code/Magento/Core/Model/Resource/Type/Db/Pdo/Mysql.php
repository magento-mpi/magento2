<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Type\Db\Pdo;

class Mysql extends \Magento\Core\Model\Resource\Type\Db
    implements \Magento\App\Resource\ConnectionAdapterInterface
{
    /**
     * Filesystem class
     *
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * @var \Magento\Stdlib\DateTime
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
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Stdlib\String $string
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $dbName
     * @param array $profiler
     * @param string $initStatements
     * @param string $type
     * @param bool $active
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\Stdlib\String $string,
        \Magento\Stdlib\DateTime $dateTime,
        $host,
        $username,
        $password,
        $dbName,
        array $profiler = array(),
        $initStatements = 'SET NAMES utf8',
        $type = 'pdo_mysql',
        $active = false
    ) {
        $this->_filesystem = $filesystem;
        $this->string = $string;
        $this->dateTime = $dateTime;
        $this->_connectionConfig = array(
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'dbname' => $dbName,
            'type' => $type,
            'profiler' => !empty($profiler) && $profiler !== 'false'
        );

        $this->_host = $host;
        $this->_type = $type;
        $this->_initStatements = $initStatements;
        $this->_isActive = !($active === 'false' || $active === '0');
        parent::__construct();
    }

    /**
     * Get connection
     *
     * @return \Magento\DB\Adapter\AdapterInterface|null
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
        if ($profiler instanceof \Magento\DB\Profiler) {
            $profiler->setType($this->_type);
            $profiler->setHost($this->_host);
        }

        return $connection;
    }

    /**
     * Create and return DB adapter object instance
     *
     * @return \Magento\DB\Adapter\Pdo\Mysql
     */
    protected function _getDbAdapterInstance()
    {
        $className = $this->_getDbAdapterClassName();
        $adapter = new $className(
            $this->_filesystem,
            $this->string,
            $this->dateTime,
            $this->_connectionConfig
        );
        return $adapter;
    }

    /**
     * Retrieve DB adapter class name
     *
     * @return string
     */
    protected function _getDbAdapterClassName()
    {
        return 'Magento\DB\Adapter\Pdo\Mysql';
    }
}
