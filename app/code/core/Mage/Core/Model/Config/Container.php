<?php
/**
 *
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_Container implements Mage_Core_Model_ConfigInterface
{
    /**
     * @var Mage_Core_Model_Config_Base
     */
    protected $_data;

    /**
     * @var Mage_Core_Model_Config_Cache
     */
    protected $_configCache;

    /**
     * Configuration sections
     *
     * @var Mage_Core_Model_Config_Sections
     */
    protected $_sections;

    /**
     * Loaded configuration sections
     *
     * @var Mage_Core_Model_Config_Base[]
     */
    protected $_loadedSections = array();

    /**
     * @param Mage_Core_Model_Config_Cache $configCache
     * @param Mage_Core_Model_Config_Sections $sections
     * @param Mage_Core_Model_Config_BaseFactory $factory
     * @param string $sourceData
     */
    public function __construct(
        Mage_Core_Model_Config_Cache $configCache,
        Mage_Core_Model_Config_Sections $sections,
        Mage_Core_Model_Config_BaseFactory $factory,
        $sourceData = ''
    ) {
        $this->_data = $factory->create($sourceData);
        $this->_sections = $sections;
        $this->_configCache = $configCache;
    }

    /**
     * Get configuration node
     *
     * @param string $path
     * @return Varien_Simplexml_Element
     * @throws Mage_Core_Model_Config_Cache_Exception
     */
    public function getNode($path = null)
    {
        /**
         * Check path cache loading
         */
        if ($path !== null) {
            $sectionKey = $this->_sections->getKey($path);
            if ($sectionKey !== false) {
                if (false == array_key_exists($sectionKey, $this->_loadedSections)) {
                    Magento_Profiler::start('init_config_section:' . $sectionKey);
                    $this->_loadedSections[$sectionKey] = $this->_configCache->getSection($sectionKey);
                    Magento_Profiler::stop('init_config_section:' . $sectionKey);
                }
                if ($this->_loadedSections[$sectionKey]) {
                    $path = substr($path, strlen($sectionKey) + 1);
                    return $this->_loadedSections[$sectionKey]->getNode($path ?: null);
                }
            }
        }
        return $this->_data->getNode($path);
    }

    /**
     * Create node by $path and set its value
     *
     * @param string $path separated by slashes
     * @param string $value
     * @param boolean $overwrite
     */
    public function setNode($path, $value, $overwrite = true)
    {
        $this->_data->setNode($path, $value, $overwrite);
    }

    /**
     * Returns nodes found by xpath expression
     *
     * @param string $xpath
     * @return array
     */
    public function getXpath($xpath)
    {
        return $this->_data->getXpath($xpath);
    }

    /**
     * Reinitialize config object
     */
    public function reinit()
    {

    }
}
