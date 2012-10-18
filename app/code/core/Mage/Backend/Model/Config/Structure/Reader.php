<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend System Configuration reader.
 * Retrieves system configuration form layout from system.xml files. Merges configuration and caches it.
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Structure_Reader
{
    const CACHE_SYSTEM_CONFIGURATION_STRUCTURE = 'backend_system_configuration_structure';

    /**
     * @var Mage_Core_Model_Cache
     */
    protected $_cache;


    /**
     * @var Mage_Core_Model_Config
     */
    protected $_appConfig;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_appConfig = isset($data['config']) ? $data['config'] : Mage::getConfig();
        $this->_cache = isset($data['cache']) ? $data['cache'] : Mage::app()->getCacheInstance();
    }

    /**
     * Load system configuration
     *
     * @return Mage_Backend_Model_Config_Structure
     */
    public function getConfiguration()
    {
        if ($this->_cache->canUse('config')) {
            $cache = $this->_cache->load(self::CACHE_SYSTEM_CONFIGURATION_STRUCTURE);
            if ($cache) {
                return unserialize($cache);
            }
        }

        $fileNames = $this->_appConfig->getModuleConfigurationFiles('adminhtml' . DIRECTORY_SEPARATOR . 'system.xml');
        $config = $this->_appConfig->getModelInstance('Mage_Backend_Model_Config_Structure', $fileNames);

        if ($this->_cache->canUse('config')) {
            $this->_cache->save(
                serialize($config),
                self::CACHE_SYSTEM_CONFIGURATION_STRUCTURE,
                array(Mage_Core_Model_Config::CACHE_TAG)
            );
        }

        return $config;
    }
}
