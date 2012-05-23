<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache helper
 *
 * @package     lib
 * @subpackage  Mage_Selenium
 * @license     {license_link}
 */
class Mage_Selenium_Helper_Cache extends Mage_Selenium_Helper_Abstract
{
    /**
     * Path to cash config
     */
    const XPATH_CACHE = 'default/cache';

    /**
     * Default dir fo cash files
     */
    const DEFAULT_CACHE_DIR = 'tmp/cache';

    /**
     * Instance of cache
     * @var Zend_Cache_Core|Zend_Cache_Frontend
     */
    protected $_cache;

    /**
     * Returns path to cash dir
     *
     * @param array $options
     *
     * @return string
     */
    protected function _getCacheDir($options)
    {
        $cacheDir = isset($options['cache_dir']) ? $options['cache_dir'] : self::DEFAULT_CACHE_DIR;
        $cacheDir = SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . $cacheDir;
        if (!is_dir($cacheDir)) {
            $io = new Varien_Io_File();
            if (!$io->mkdir($cacheDir, 0777, true)) {
                throw new Exception('Cache dir is not defined');
            }
        }
        return $cacheDir;
    }

    /**
     * Retrieve cache instance
     * @return Zend_Cache_Core|Zend_Cache_Frontend
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $config = $this->getConfig()->getConfigValue(self::XPATH_CACHE);
            if (isset($config)) {
                $frontend = $config['frontend']['name'];
                $backend = $config['backend']['name'];

                $frontendOption = $config['frontend']['options'];
                $backendOption = $config['backend']['options'];

                $backendOption['cache_dir'] = $this->_getCacheDir($backendOption);

                $this->_cache = Zend_Cache::factory($frontend, $backend, $frontendOption, $backendOption);
            }
        }
        return $this->_cache;
    }
}
