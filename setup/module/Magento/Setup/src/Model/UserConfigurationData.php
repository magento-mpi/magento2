<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Setup\Module\Setup;
use Magento\Store\Model\Store;
use Magento\Core\Helper\Data;
use Magento\Directory\Model\Currency;

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
        Store::XML_PATH_USE_REWRITES => self::KEY_USE_SEF_URL,
        Store::XML_PATH_UNSECURE_BASE_URL => self::KEY_BASE_URL,
        Store::XML_PATH_SECURE_IN_FRONTEND => self::KEY_IS_SECURE,
        Store::XML_PATH_SECURE_BASE_URL => self::KEY_BASE_URL_SECURE,
        Store::XML_PATH_SECURE_IN_ADMINHTML => self::KEY_IS_SECURE_ADMIN,
        Data::XML_PATH_DEFAULT_LOCALE => self::KEY_LANGUAGE,
        Data::XML_PATH_DEFAULT_TIMEZONE => self::KEY_TIMEZONE,
        Currency::XML_PATH_CURRENCY_BASE => self::KEY_CURRENCY,
        Currency::XML_PATH_CURRENCY_DEFAULT => self::KEY_CURRENCY,
        Currency::XML_PATH_CURRENCY_ALLOW => self::KEY_CURRENCY,
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
