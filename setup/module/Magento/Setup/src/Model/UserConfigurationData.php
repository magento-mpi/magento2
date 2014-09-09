<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Module\Setup;

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
     * @param Setup $setup
     */
    public function __construct(Setup $setup)
    {
        $this->setup = $setup;
    }

    /**
     * Installs All Configuration Data
     *
     * @param array $data
     * @return void
     */
    public function install($data)
    {
        $this->installData('web/seo/use_rewrites', $data['config']['rewrites']['allowed'], 0);
        $this->installData('web/unsecure/base_url', $data['config']['address']['web'], '{{unsecure_base_url}}');
        $this->installData('web/secure/use_in_frontend', $data['config']['https']['front'], 0);
        $this->installData('web/secure/base_url', $data['config']['address']['web'], '{{secure_base_url}}');
        $this->installData('web/secure/use_in_adminhtml', $data['config']['https']['admin'], 0);
        $this->installData('general/locale/code', $data['store']['language'], 'en_US');
        $this->installData('general/locale/timezone', $data['store']['timezone'], 'America/Los_Angeles');
        $this->installData('currency/options/base', $data['store']['currency'], 'USD');
        $this->installData('currency/options/default', $data['store']['currency'], 'USD');
        $this->installData('currency/options/allow', $data['store']['currency'], 'USD');
    }

    /**
     * Installs Configuration Data
     *
     * @param string $key
     * @param mixed $value
     * @param mixed $default
     * @return void
     * @throws \Exception
     */
    public function installData($key, $value, $default)
    {
        $this->setup->addConfigData($key, isset($value) ? $value : $default);
    }


}