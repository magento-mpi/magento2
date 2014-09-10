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
    /**#@+
     * Model data keys
     */
    const KEY_USE_SEF_URL = 'use_rewrites';
    const KEY_BASE_URL = 'base_url';
    const KEY_BASE_URL_SECURE = 'base_url_secure';
    const KEY_IS_SECURE = 'use_secure';
    const KEY_IS_SECURE_ADMIN = 'use_secure_admin';
    const KEY_LANGUAGE = 'language';
    const KEY_TIMEZONE = 'timezone';
    const KEY_CURRENCY = 'currency';
    /**#@- */

    /**
     * Map of configuration paths to data keys
     *
     * @var array
     */
    private static $pathDataMap = [
        'web/seo/use_rewrites' => self::KEY_USE_SEF_URL,
        'web/unsecure/base_url' => self::KEY_BASE_URL,
        'web/secure/use_in_frontend' => self::KEY_IS_SECURE,
        'web/secure/base_url' => self::KEY_BASE_URL_SECURE,
        'web/secure/use_in_adminhtml' => self::KEY_IS_SECURE_ADMIN,
        'general/locale/code' => self::KEY_LANGUAGE,
        'general/locale/timezone' => self::KEY_TIMEZONE,
        'currency/options/base' => self::KEY_CURRENCY,
        'currency/options/default' => self::KEY_CURRENCY,
        'currency/options/allow' => self::KEY_CURRENCY,
    ];

    /**
     * Default data values
     *
     * @var array
     */
    private static $defaults = [
        self::KEY_USE_SEF_URL => 0,
        self::KEY_BASE_URL => '{{unsecure_base_url}}',
        self::KEY_IS_SECURE => 0,
        self::KEY_BASE_URL_SECURE => '{{unsecure_base_url}}',
        self::KEY_IS_SECURE_ADMIN => 0,
        self::KEY_LANGUAGE => 'en_US',
        self::KEY_TIMEZONE => 'America/Los_Angeles',
        self::KEY_CURRENCY => 'USD',
    ];

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
        foreach (self::$defaults as $key => $value) {
            if (isset($data[$key])) {
                $value = $data[$key];
            }
            foreach (array_keys(self::$pathDataMap, $key) as $path) {
                $this->installData($path, $value);
            }
        }
    }

    /**
     * Installs Configuration Data
     *
     * @param string $key
     * @param mixed $value
     * @return void
     * @throws \Exception
     */
    public function installData($key, $value)
    {
        $this->setup->addConfigData($key, $value);
    }
}
