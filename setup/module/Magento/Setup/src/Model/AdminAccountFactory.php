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
use Magento\Module\Setup;

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
     * @param Random $random
     */
    public function __construct(
        Random $random
    ) {
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
     * @param Setup $setup
     * @return AdminAccount
     */
    public function create(Setup $setup)
    {
        return new AdminAccount(
            $setup,
            $this->random,
            $this->configuration
        );
    }
}
