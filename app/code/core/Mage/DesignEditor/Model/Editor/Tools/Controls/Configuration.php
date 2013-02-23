<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration of controls
 */
class Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration
{
    /**
     * Module name used for saving data to the view configuration
     */
    const SEPARATOR_MODULE = '::';

    /**
     * Application Event Dispatcher
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventDispatcher;

    /**
     * @var Mage_DesignEditor_Model_Config_Control_Abstract
     */
    protected $_configuration;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Magento_Config_View
     */
    protected $_viewConfig;

    /**
     * @var Magento_Config_View
     */
    protected $_viewConfigParent;

    /**
     * Controls data
     *
     * @var array
     */
    protected $_data;

    /**
     * Initialize dependencies
     *
     * @param Mage_Core_Model_Design_Package $design
     * @param Magento_Filesystem $filesystem
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_DesignEditor_Model_Config_Control_Abstract|null $configuration
     * @param Mage_Core_Model_Theme|null $theme
     */
    public function __construct(
        Mage_Core_Model_Design_Package $design,
        Magento_Filesystem $filesystem,
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_DesignEditor_Model_Config_Control_Abstract $configuration = null,
        Mage_Core_Model_Theme $theme = null
    ) {
        $this->_configuration = $configuration;
        $this->_theme = $theme;
        $this->_design = $design;
        $this->_filesystem = $filesystem;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_initViewConfigs()->_loadControlsData();
    }

    /**
     * Load all control values
     *
     * @return Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    protected function _loadControlsData()
    {
        $this->_data = $this->_configuration->getAllControlsData();
        foreach ($this->_data as &$control) {
            $this->_loadControlData($control, 'value', $this->_viewConfig);
            $this->_loadControlData($control, 'default', $this->_viewConfigParent);
        }
        return $this;
    }

    /**
     * Initialize view configurations
     *
     * @return Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    protected function _initViewConfigs()
    {
        $this->_viewConfig = $this->_design->getViewConfig(array(
            'area'       => Mage_Core_Model_Design_Package::DEFAULT_AREA,
            'themeModel' => $this->_theme
        ));
        $this->_viewConfigParent = $this->_design->getViewConfig(array(
            'area'       => Mage_Core_Model_Design_Package::DEFAULT_AREA,
            'themeModel' => $this->_theme->getParentTheme()
        ));
        return $this;
    }

    /**
     * Load control data
     *
     * @return array
     */
    public function getAllControlsData()
    {
        return $this->_data;
    }

    /**
     * Get control data
     *
     * @param string $controlName
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getControlData($controlName)
    {
        if (!isset($this->_data[$controlName])) {
            throw new Mage_Core_Exception("Unknown control: \"{$controlName}\"");
        }
        return $this->_data[$controlName];
    }

    /**
     * Load data item values and default values from the view configuration
     *
     * @param array $control
     * @param string $paramName
     * @param Magento_Config_View $viewConfiguration
     * @return Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    protected function _loadControlData(array &$control, $paramName, Magento_Config_View $viewConfiguration)
    {
        if (!empty($control['components'])) {
            foreach ($control['components'] as &$control) {
                $this->_loadControlData($control, $paramName, $viewConfiguration);
            }
        } elseif (!empty($control['var'])) {
            list($module, $varKey) = $this->_extractModuleKey($control['var']);
            $control[$paramName] = $viewConfiguration->getVarValue($module, $varKey);
        }
        return $this;
    }

    /**
     * Extract module and key name
     *
     * @param string $value
     * @return array
     */
    protected function _extractModuleKey($value)
    {
        return explode(self::SEPARATOR_MODULE, $value);
    }

    /**
     * Save control values data
     *
     * @param array $controlsData
     * @return Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    public function saveData(array $controlsData)
    {
        $configDom = $this->_viewConfig->getConfigDomCopy()->getDom();
        $varData = $this->_prepareVarData($controlsData, $this->_data);

        /** @var $varsNode DOMElement */
        foreach ($configDom->childNodes->item(0)->childNodes as $varsNode) {
            $moduleName = $varsNode->getAttribute('module');
            if (!isset($varData[$moduleName])) {
                continue;
            }
            /** @var $varNode DOMElement */
            foreach ($varsNode->getElementsByTagName('var') as $varNode) {
                $varName = $varNode->getAttribute('name');
                if (isset($varData[$moduleName][$varName])) {
                    $varNode->nodeValue = $varData[$moduleName][$varName];
                }
            }
        }
        $this->_saveViewConfiguration($configDom);
        $this->_eventDispatcher->dispatch('save_xml_configuration', array('configuration' => $this));
        return $this;
    }

    /**
     * Get control configuration
     *
     * @return Mage_DesignEditor_Model_Config_Control_Abstract
     */
    public function getControlConfig()
    {
        return $this->_configuration;
    }

    /**
     * Get theme
     *
     * @return Mage_Core_Model_Theme
     */
    public function getTheme()
    {
        return $this->_theme;
    }

    /**
     * Extract var data keys for current controls configuration
     *
     * @param array $controlsData
     * @param array $controls
     * @param array $result
     * @return array
     */
    protected function _prepareVarData(array $controlsData, array $controls, array &$result = array())
    {
        foreach ($controls as $controlName => $control) {
            if (!empty($control['components'])) {
                $this->_prepareVarData($controlsData, $control['components'], $result);
            } elseif (isset($controlsData[$controlName])) {
                list($module, $varKey) = $this->_extractModuleKey($control['var']);
                $result[$module][$varKey] = $controlsData[$controlName];
            }
        }
        return $result;
    }

    /**
     * Return path to view.xml in customization
     *
     * @return string
     */
    public function getCustomViewConfigPath()
    {
        return $this->_theme->getCustomizationPath() . DIRECTORY_SEPARATOR
            . Mage_Core_Model_Design_Package::FILENAME_VIEW_CONFIG;
    }

    /**
     * Save customized DOM of view configuration
     *
     * @param DOMDocument $config
     * @return Mage_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    protected function _saveViewConfiguration(DOMDocument $config)
    {
        $this->_filesystem->setIsAllowCreateDirectories(true)
            ->write($this->getCustomViewConfigPath(), $config->saveXML());
        return $this;
    }
}
