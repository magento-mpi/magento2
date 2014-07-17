<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\DB\Adapter\Pdo\Mysql;

class DatabaseCheck
{
    /**
     * @var Mysql
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $config;
    /**
     * @param array $config
     */
    public function __construct(
        array $config = array()
    ) {
        $this->config = $config;
    }

    public function checkConnection()
    {
        $this->adapter = new Mysql($this->config);
        $this->adapter->connect();
        return $this->adapter->getDriver()->getConnection()->isConnected();
    }
}
