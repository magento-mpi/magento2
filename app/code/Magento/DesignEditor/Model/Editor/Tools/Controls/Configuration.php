<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration of controls
 */
class Magento_DesignEditor_Model_Editor_Tools_Controls_Configuration
{
    /**
     * Module name used for saving data to the view configuration
     */
    const SEPARATOR_MODULE = '::';

    /**
     * Application Event Dispatcher
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventDispatcher;

    /**
     * @var Magento_DesignEditor_Model_Config_Control_Abstract
     */
    protected $_configuration;

    /**
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_design;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * @var Magento_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Magento_Core_Model_Theme
     */
    protected $_parentTheme;

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
     * List of controls
     *
     * @var array
     */
    protected $_controlList = array();

    /**
     * View config model
     *
     * @var Magento_Core_Model_View_Config
     */
    protected $_viewConfigLoader;

    /**
     * Initialize dependencies
     *
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Filesystem $filesystem
     * @param Magento_Core_Model_Event_Manager $eventDispatcher
     * @param Magento_Core_Model_View_Config $viewConfig
     * @param Magento_DesignEditor_Model_Config_Control_Abstract|null $configuration
     * @param Magento_Core_Model_Theme|null $theme
     * @param Magento_Core_Model_Theme $parentTheme
     */
    public function __construct(
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Filesystem $filesystem,
        Magento_Core_Model_Event_Manager $eventDispatcher,
        Magento_Core_Model_View_Config $viewConfig,
        Magento_DesignEditor_Model_Config_Control_Abstract $configuration = null,
        Magento_Core_Model_Theme $theme = null,
        Magento_Core_Model_Theme $parentTheme = null
    ) {
        $this->_configuration = $configuration;
        $this->_theme = $theme;
        $this->_parentTheme = $parentTheme ?: $theme->getParentTheme();
        $this->_design = $design;
        $this->_filesystem = $filesystem;
        $this->_eventDispatcher = $eventDispatcher;
        $this->_viewConfigLoader = $viewConfig;
        $this->_initViewConfigs()->_loadControlsData();
    }

    /**
     * Initialize view configurations
     *
     * @return Magento_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    protected function _initViewConfigs()
    {
        $this->_viewConfig = $this->_viewConfigLoader->getViewConfig(array(
            'area'       => Magento_Core_Model_View_DesignInterface::DEFAULT_AREA,
            'themeModel' => $this->_theme
        ));
        $this->_viewConfigParent = $this->_viewConfigLoader->getViewConfig(array(
            'area'       => Magento_Core_Model_View_DesignInterface::DEFAULT_AREA,
            'themeModel' => $this->_parentTheme
        ));
        return $this;
    }

    /**
     * Load all control values
     *
     * @return Magento_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    protected function _loadControlsData()
    {
        $this->_data = $this->_configuration->getAllControlsData();
        $this->_prepareControlList($this->_data);
        foreach ($this->_controlList as &$control) {
            $this->_loadControlData($control, 'value', $this->_viewConfig);
            $this->_loadControlData($control, 'default', $this->_viewConfigParent);
        }
        return $this;
    }

    /**
     * Prepare list of control links
     *
     * @param array $controls
     * @return Magento_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    protected function _prepareControlList(array &$controls)
    {
        foreach ($controls as $controlName => &$control) {
            if (!empty($control['components'])) {
                $this->_prepareControlList($control['components']);
            }
            $this->_controlList[$controlName] = &$control;
        }
        return $this;
    }

    /**
     * Load data item values and default values from the view configuration
     *
     * @param array $control
     * @param string $paramName
     * @param Magento_Config_View $viewConfiguration
     * @return Magento_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    protected function _loadControlData(array &$control, $paramName, Magento_Config_View $viewConfiguration)
    {
        if (!empty($control['var'])) {
            list($module, $varKey) = $this->_extractModuleKey($control['var']);
            $control[$paramName] = $viewConfiguration->getVarValue($module, $varKey);
        }
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
     * @throws Magento_Core_Exception
     */
    public function getControlData($controlName)
    {
        if (!isset($this->_controlList[$controlName])) {
            throw new Magento_Core_Exception("Unknown control: \"{$controlName}\"");
        }
        return $this->_controlList[$controlName];
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
     * Extract var data keys for current controls configuration
     * array(module => array(varKey => array(controlName, controlValue)))
     *
     * @param array $controlsData
     * @param array $controls
     * @return array
     */
    protected function _prepareVarData(array $controlsData, array $controls)
    {
        $result = array();
        foreach ($controlsData as $controlName => $controlValue) {
            if (isset($controls[$controlName])) {
                list($module, $varKey) = $this->_extractModuleKey($controls[$controlName]['var']);
                $result[$module][$varKey] = array($controlName, $controlValue);
            }
        }
        return $result;
    }

    /**
     * Save control values data
     *
     * @param array $controlsData
     * @return Magento_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    public function saveData(array $controlsData)
    {
        $configDom = $this->_viewConfig->getDomConfigCopy()->getDom();
        $varData = $this->_prepareVarData($controlsData, $this->_controlList);

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
                    list($controlName, $controlValue) = $varData[$moduleName][$varName];
                    $varNode->nodeValue = $controlValue;
                    $this->_controlList[$controlName]['value'] = $controlValue;
                }
            }
        }
        $this->_saveViewConfiguration($configDom);
        $this->_eventDispatcher->dispatch('save_view_configuration', array(
            'configuration' => $this, 'theme' => $this->_theme
        ));
        return $this;
    }

    /**
     * Get control configuration
     *
     * @return Magento_DesignEditor_Model_Config_Control_Abstract
     */
    public function getControlConfig()
    {
        return $this->_configuration;
    }

    /**
     * Save customized DOM of view configuration
     *
     * @param DOMDocument $config
     * @return Magento_DesignEditor_Model_Editor_Tools_Controls_Configuration
     */
    protected function _saveViewConfiguration(DOMDocument $config)
    {
        $targetPath = $this->_theme->getCustomization()->getCustomViewConfigPath();
        $this->_filesystem->setIsAllowCreateDirectories(true)->write($targetPath, $config->saveXML());
        return $this;
    }
}
