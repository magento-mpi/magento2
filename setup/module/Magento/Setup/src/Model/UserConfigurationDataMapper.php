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
use Magento\Backend\Model\Url;

/**
 * Model Class to Install User Configuration Data
 *
 * @package Magento\Setup\Model
 */
class UserConfigurationDataMapper
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
    const KEY_ADMIN_USE_SECURITY_KEY = 'admin_use_security_key';
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
        Url::XML_PATH_USE_SECURE_KEY => self::KEY_ADMIN_USE_SECURITY_KEY,
    ];

    /**
     * Gets All Configuration Data
     *
     * @param array $data
     * @return array
     */
    public function getConfigData($data)
    {
        $configData = [];
        if ($data[self::KEY_BASE_URL_SECURE] === '') {
            unset($data[self::KEY_BASE_URL_SECURE] );
        }
        foreach (self::$pathDataMap as $path => $key) {
            if (isset($data[$key])) {
                if ((($key === self::KEY_IS_SECURE) || ($key === self::KEY_IS_SECURE_ADMIN))
                    && (!isset($data[self::KEY_BASE_URL_SECURE]))) {
                    continue;
                }
                if (($key === self::KEY_BASE_URL) || ($key === self::KEY_BASE_URL_SECURE)) {
                    $data[$key] = rtrim($data[$key], '/') . '/';
                }
                $configData[$path] = $data[$key];
            }
        }

        return $configData;
    }

    /**
     * Validate parameter values of user configuration tool
     *
     * @param array $data
     * @return string
     */
    public static function validateUserConfig(array $data)
    {
        $validationMessages = '';
        // check URL
        if (isset($data[self::KEY_BASE_URL]) && !self::validateUrl($data[self::KEY_BASE_URL])) {
            $validationMessages .= self::KEY_BASE_URL .
                ": Please enter a valid base url. Current: {$data[self::KEY_BASE_URL]}" . PHP_EOL;
        }
        if (isset($data[self::KEY_BASE_URL_SECURE]) && !self::validateUrl($data[self::KEY_BASE_URL_SECURE], true)) {
            $validationMessages .= self::KEY_BASE_URL_SECURE .
                ": Please enter a valid secure base url. Current: {$data[self::KEY_BASE_URL_SECURE]}" . PHP_EOL;
        }

        // check 0/1 options
        $flags = [];
        if (isset($data[self::KEY_USE_SEF_URL])) {
            $flags[self::KEY_USE_SEF_URL] = $data[self::KEY_USE_SEF_URL];
        }
        if (isset($data[self::KEY_IS_SECURE])) {
            $flags[self::KEY_IS_SECURE] = $data[self::KEY_IS_SECURE];
        }
        if (isset($data[self::KEY_IS_SECURE_ADMIN])) {
            $flags[self::KEY_IS_SECURE_ADMIN] = $data[self::KEY_IS_SECURE_ADMIN];
        }
        if (isset($data[self::KEY_ADMIN_USE_SECURITY_KEY])) {
            $flags[self::KEY_ADMIN_USE_SECURITY_KEY] = $data[self::KEY_ADMIN_USE_SECURITY_KEY];
        }

        $validationMessages .= self::validateOneZeroFlags($flags);

        // check language, currency and timezone
        $options = new Lists(new \Zend_Locale());
        if (isset($data[self::KEY_LANGUAGE])) {
            if (!isset($options->getLocaleList()[$data[self::KEY_LANGUAGE]])) {
                $validationMessages .= self::KEY_LANGUAGE . ': Please use a valid language. ' .
                    "Current: {$data[self::KEY_LANGUAGE]}" . PHP_EOL;
            }
        }

        if (isset($data[self::KEY_CURRENCY])) {
            if (!isset($options->getCurrencyList()[$data[self::KEY_CURRENCY]])) {
                $validationMessages .= self::KEY_CURRENCY . ': Please use a valid currency. ' .
                    "Current: {$data[self::KEY_CURRENCY]}" . PHP_EOL;
            }
        }

        if (isset($data[self::KEY_TIMEZONE])) {
            if (!isset($options->getTimezoneList()[$data[self::KEY_TIMEZONE]])) {
                $validationMessages .= self::KEY_TIMEZONE . ': Please use a valid timezone. ' .
                    "Current: {$data[self::KEY_TIMEZONE]}" . PHP_EOL;
            }
        }

        return $validationMessages;
    }

    /**
     * Validate if all flags are of 0/1 option
     *
     * @param array $flags
     * @return string
     */
    private static function validateOneZeroFlags(array $flags = [])
    {
        $validationMessages = '';
        $wrongOptionMessage = 'Please enter a valid option (0/1). ';
        foreach ($flags as $key => $flag) {
            if ($flag !== '0' && $flag !== '1') {
                $validationMessages .= "{$key}: {$wrongOptionMessage} Current: {$flag}" . PHP_EOL;
            }
        }
        return $validationMessages;
    }

    /**
     * Validate URL
     *
     * @param string $url
     * @param bool $secure
     * @return bool
     */
    private function validateUrl($url, $secure = false)
    {
        $validator = new \Zend\Validator\Uri();
        if ($validator->isValid($url)) {
            if ($secure) {
                return strpos($url, 'https://') !== false;
            }
            else {
                return strpos($url, 'http://') !== false;
            }
        }
        return false;
    }
}
