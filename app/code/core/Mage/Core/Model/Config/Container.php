<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_Container implements Mage_Core_Model_ConfigInterface
{
    /**
     * Configuration data
     *
     * @var Mage_Core_Model_Config_Base
     */
    protected $_data;

    /**
     * Configuration cache model
     *
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
     * Get section path
     *
     * @param string $path
     * @param string $sectionKey
     * @return string|null
     */
    protected function _getSectionPath($path, $sectionKey)
    {
        $sectionPath = substr($path, strlen($sectionKey) + 1);
        return $sectionPath ?: null;
    }

    /**
     * Get config section
     *
     * @param string $sectionKey
     * @return Mage_Core_Model_Config_Base|null
     */
    protected function _getSection($sectionKey)
    {
        if (false === $sectionKey) {
            return null;
        }

        if (false == array_key_exists($sectionKey, $this->_loadedSections)) {
            Magento_Profiler::start('init_config_section:' . $sectionKey);
            $this->_loadedSections[$sectionKey] = $this->_configCache->getSection($sectionKey);
            Magento_Profiler::stop('init_config_section:' . $sectionKey);
        }

        return $this->_loadedSections[$sectionKey] ?: null;
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
        if ($path !== null) {
            $sectionKey = $this->_sections->getKey($path);
            $section = $this->_getSection($sectionKey);
            if ($section) {
                $res = $section->getNode($this->_getSectionPath($path, $sectionKey));
                if ($res !== false) {
                    return $res;
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
     * @throws Mage_Core_Model_Config_Cache_Exception
     */
    public function setNode($path, $value, $overwrite = true)
    {
        if ($path !== null) {
            $sectionKey = $this->_sections->getKey($path);
            $section = $this->_getSection($sectionKey);
            if ($section) {
                $section->setNode($this->_getSectionPath($path, $sectionKey), $value, $overwrite);
            }
        }
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
