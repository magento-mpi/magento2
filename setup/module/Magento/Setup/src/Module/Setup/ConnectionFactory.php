<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Setup\Module\Setup;

use Zend\ServiceManager\ServiceLocatorInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;

class ConnectionFactory
{
    /**
     * Zend Framework's service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Create DB adapter object
     *
     * @param \ArrayObject|array $config
     * @return Mysql
     */
    public function create($config)
    {
        $config = [
            'driver' => 'Pdo',
            'dbname' => $config[Config::KEY_DB_NAME],
            'host' => $config[Config::KEY_DB_HOST],
            'username' => $config[Config::KEY_DB_USER],
            'password' => isset($config[Config::KEY_DB_PASS]) ? $config[Config::KEY_DB_PASS] : null,
            'driver_options' => [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]
        ];
        return new Mysql(
            $this->serviceLocator->get('Magento\Framework\Filesystem'),
            $this->serviceLocator->get('Magento\Framework\Stdlib\String'),
            $this->serviceLocator->get('Magento\Framework\Stdlib\DateTime'),
            $config
        );
    }
}
