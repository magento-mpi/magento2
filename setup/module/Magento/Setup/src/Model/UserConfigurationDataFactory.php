<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Module\Setup;

/**
 * User Configuration Data Factory
 *
 * @package Magento\Setup\Model
 */
class UserConfigurationDataFactory
{
    /**
     * Configuration Data
     *
     * @var array
     */
    protected $configuration = [];

    /**
     * Sets Configurations
     *
     * @param array $config
     * @return void
     */
    public function setConfig(array $config)
    {
        $this->configuration = $config;
    }

    /**
     * Creates Setup Instance
     *
     * @param Setup $setup
     * @return UserConfigurationData
     */
    public function create(Setup $setup)
    {
        return new UserConfigurationData($setup);
    }
}