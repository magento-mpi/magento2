<?php
/**
 * Cms Hierarchy Model for config processing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_VersionsCms_Model_Hierarchy_Config
{
    /**
     * Menu layouts configuration
     * @var array
     */
    protected $_contextMenuLayouts = null;

    /**
     * @var Magento_VersionsCms_Model_Hierarchy_Config_Data
     */
    protected $_configData;

    /**
     * @param Magento_VersionsCms_Model_Hierarchy_Config_Data $configData
     */
    public function __construct(Magento_VersionsCms_Model_Hierarchy_Config_Data $configData)
    {
        $this->_configData = $configData;
    }

    /**
     * Return available Context Menu layouts output
     *
     * @return array
     */
    public function getContextMenuLayouts()
    {
        return $this->_configData->get();
    }

    /**
     * Return Context Menu layout by its name
     *
     * @param string $layoutName
     * @return Magento_Object|boolean
     */
    public function getContextMenuLayout($layoutName)
    {
        $menuLayouts = $this->_configData->get();
        return isset($menuLayouts[$layoutName]) ? $menuLayouts[$layoutName] : false;
    }
}
