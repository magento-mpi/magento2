<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Module\SetupFactory;
use Magento\Module\Setup;
use Magento\Module\Setup\Config;

/**
 * Model Class to Install User Configuration Data
 *
 * @package Magento\Setup\Model
 */
class UserConfigurationData
{
    /**
     * Setup Instance
     *
     * @var Setup $setup
     */
    protected $setup;

    /**
     * Default Constructor
     *
     * @param SetupFactory $setupFactory
     * @param Config $config
     */
    public function __construct(
        Setup $setup
    )
    {
        $this->setup = $setup;
    }

    /**
     * Installs All Configuration Data
     *
     * @param array $data
     * @returns void
     */
    public function install($data) {
        $this->initSetup($data);
        $this->installData('web/seo/use_rewrites', $data['config']['rewrites']['allowed'], 0);
        $this->installData('web/unsecure/base_url', $data['config']['address']['front'], '{{unsecure_base_url}}');
        $this->installData('web/secure/use_in_frontend', $data['config']['https']['web'], 0);
        $this->installData('web/secure/base_url', $data['config']['address']['front'], '{{secure_base_url}}');
        $this->installData('web/secure/use_in_adminhtml', $data['config']['https']['admin'], 0);
        $this->installData('general/locale/code', $data['store']['language'], 'en_US');
        $this->installData('general/locale/timezone', $data['store']['timezone'], 'America/Los_Angeles');
        $this->installData('currency/options/base', $data['store']['currency'], 'USD');
        $this->installData('currency/options/default', $data['store']['currency'], 'USD');
        $this->installData('currency/options/allow', $data['store']['currency'], 'USD');
    }

    /**
     * Installs Coniguration Data
     * @param string $key
     * @param mixed $value
     * @param mixed $default
     * @returns void
     * @throws \Exception
     */
    public function installData($key, $value, $default) {
        $this->setup->addConfigData($key, isset($value) ? $value : $default);
    }


}