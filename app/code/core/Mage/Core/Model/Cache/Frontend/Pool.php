<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * In-memory pool of cache front-end instances, specified in the configuration
 */
class Mage_Core_Model_Cache_Frontend_Pool
{
    /**#@+
     * XPaths where cache frontend settings reside
     */
    const XML_PATH_SETTINGS_DEFAULT = 'global/cache';
    const XML_PATH_SETTINGS_CUSTOM  = 'global/cache_advanced/%s';
    /**#@-*/

    /**
     * Frontend identifier associated with the default settings
     */
    const DEFAULT_FRONTEND_ID = 'generic';

    /**
     * @var Mage_Core_Model_ConfigInterface
     */
    private $_config;

    /**
     * @var Mage_Core_Model_Cache_Frontend_Factory
     */
    private $_frontendFactory;

    /**
     * @var Magento_Cache_FrontendInterface[]
     */
    private $_instances = array();

    /**
     * @param Mage_Core_Model_Config_Primary $cacheConfig
     * @param Mage_Core_Model_Cache_Frontend_Factory $frontendFactory
     */
    public function __construct(
        Mage_Core_Model_Config_Primary $cacheConfig,
        Mage_Core_Model_Cache_Frontend_Factory $frontendFactory
    ) {
        $this->_config = $cacheConfig;
        $this->_frontendFactory = $frontendFactory;
    }

    /**
     * Retrieve frontend instance by its unique identifier, creating missing ones based on the configuration
     *
     * @param string $identifier Cache frontend identifier
     * @return Magento_Cache_FrontendInterface Cache frontend instance
     */
    public function get($identifier)
    {
        // resolve frontend identifier to a config path
        if ($identifier == self::DEFAULT_FRONTEND_ID) {
            $configPath = self::XML_PATH_SETTINGS_DEFAULT;
        } else {
            $configPath = sprintf(self::XML_PATH_SETTINGS_CUSTOM, $identifier);
        }

        // attempt to use custom settings
        $settings = $this->_config->getNode($configPath);

        // fall back to the default settings
        if (!$settings && $configPath != self::XML_PATH_SETTINGS_DEFAULT) {
            $configPath = self::XML_PATH_SETTINGS_DEFAULT;
            $settings = $this->_config->getNode($configPath);
        }

        // use config path as a cache identifier to associate the same frontend instance with different identifiers
        if (!isset($this->_instances[$configPath])) {
            if ($settings) {
                $settings = $settings->asArray();
            } else {
                $settings = array();
            }
            $this->_instances[$configPath] = $this->_frontendFactory->create($settings);
        }
        return $this->_instances[$configPath];
    }
}
