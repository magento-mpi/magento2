<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Framework\Math\Random;
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
     * @var Random
     */
    protected $random;

    /**
     * @param AdapterInterface $connection
     * @param Random $random
     */
    public function __construct(
        AdapterInterface $connection,
        Random $random
    ) {
        $this->adapter = $connection;
        $this->random = $random;
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
            $this->random,
            $this->configuration
        );
    }
}
