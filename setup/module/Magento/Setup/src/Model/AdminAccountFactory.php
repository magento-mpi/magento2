<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Module\Setup\Connection\AdapterInterface;

class AdminAccountFactory
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @param AdapterInterface $connection
     */
    public function __construct(
        AdapterInterface $connection
    ) {
        $this->adapter = $connection;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->configuration = $config;
    }

    /**
     * @return AdminAccount
     */
    public function create()
    {
        return new AdminAccount(
            $this->adapter,
            $this->configuration
        );
    }
}
