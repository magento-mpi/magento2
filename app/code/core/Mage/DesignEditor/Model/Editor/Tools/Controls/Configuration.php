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
     * @var Mage_DesignEditor_Model_Config_Control_Abstract
     */
    protected $_configuration;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Controls data
     *
     * @var array
     */
    protected $_data;

    /**
     * Initialize dependencies
     */
    public function __construct(
        Mage_DesignEditor_Model_Config_Control_Abstract $configuration = null,
        Mage_Core_Model_Theme $theme = null,
        Mage_Core_Model_Design_Package $design
    ) {
        $this->_configuration = $configuration;
        $this->_theme = $theme;
        $this->_design = $design;
    }

    /**
     * Load control data
     *
     * @return array
     */
    public function getAllControlsData()
    {
        if ($this->_data !== null) {
            return $this->_data;
        }

        $configView = $this->_design->getViewConfig(array(
            'area'       => Mage_Core_Model_Design_Package::DEFAULT_AREA,
            'themeModel' => $this->_theme
        ));
        $configViewParent = $this->_design->getViewConfig(array(
            'area'       => Mage_Core_Model_Design_Package::DEFAULT_AREA,
            'themeModel' => $this->_theme->getParentTheme()
        ));

        $this->_data = $this->_configuration->getAllControlsData();
        foreach ($this->_data as &$control) {
            $this->_loadControlData($control, 'value', $configView);
            $this->_loadControlData($control, 'default', $configViewParent);
        }

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
        $data = $this->getAllControlsData();
        if (!isset($data[$controlName])) {
            throw new Mage_Core_Exception("Unknown control: \"{$controlName}\"");
        }
        return $data[$controlName];
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
            list($module, $varKey) = explode(self::SEPARATOR_MODULE, $control['var']);
            $control[$paramName] = $viewConfiguration->getVarValue($module, $varKey);
        }
        return $this;
    }
}
