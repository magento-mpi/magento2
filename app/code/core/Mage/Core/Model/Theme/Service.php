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
 * Theme Service model
 */
class Mage_Core_Model_Theme_Service
{
    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @var Mage_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * Whether is present customized themes
     *
     * @var bool
     */
    protected $_hasCustomizedThemes;

    /**
     * Customized themes which are assigned to storeviews or as default
     *
     * @var array
     */
    protected $_assignedThemes;

    /**
     * Customized themes which are not assigned to storeviews or as default
     *
     * @var array
     */
    protected $_unassignedThemes;

    /**
     * Initialize service model
     *
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_App $app
     * @param Mage_Core_Model_Store_Config $storeConfig
     */
    public function __construct(Mage_Core_Model_Theme $theme,
        Mage_Core_Model_App $app,
        Mage_Core_Model_Store_Config $storeConfig
    ) {
        $this->_theme = $theme;
        $this->_app = $app;
        $this->_storeConfig = $storeConfig;
    }

    /**
     * Check whether is present customized themes
     *
     * @return bool
     */
    public function isPresentCustomizedThemes()
    {
        if (is_null($this->_hasCustomizedThemes)) {
            $this->_hasCustomizedThemes = false;
            /** @var $theme Mage_Core_Model_Theme */
            foreach ($this->_theme->getCollection() as $theme) {
                if ($theme->isVirtual()) {
                    $this->_hasCustomizedThemes = true;
                    break;
                }
            }
        }
        return $this->_hasCustomizedThemes;
    }

    /**
     * Return not customized theme collection by page
     *
     * @param int $page
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function getNotCustomizedFrontThemes($page)
    {
        return $this->_theme->getCollection()
            ->addAreaFilter()
            ->addFilter('theme_path', "NOT ISNULL(theme_path)", 'string')
            ->setPageSize()
            ->setCurPage($page);
    }

    /**
     * Return customized themes which are assigned to storeviews or as default
     *
     * @return array
     */
    public function getAssignedThemes()
    {
        if (is_null($this->_assignedThemes)) {
            $this->_prepareCustomizedThemes();
        }
        return $this->_assignedThemes;
    }

    /**
     * Return customized themes which are not assigned to storeviews or as default
     *
     * @return array
     */
    public function getUnassignedThemes()
    {
        if (is_null($this->_unassignedThemes)) {
            $this->_prepareCustomizedThemes();
        }
        return $this->_unassignedThemes;
    }

    /**
     * Check customized themes and select assigned and unassigned
     *
     * @return Mage_Core_Model_Theme_Service
     */
    protected function _prepareCustomizedThemes()
    {
        /** @var $customizedThemes Mage_Core_Model_Resource_Theme_Collection */
        $customizedThemes = $this->_getCustomizedFrontThemes();
        $assignedThemes = $this->_getAssignedThemes();

        $this->_assignedThemes = array();
        $this->_unassignedThemes = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($customizedThemes as $theme) {
            if (isset($assignedThemes[$theme->getId()])) {
                $theme->setAssignedStores($assignedThemes[$theme->getId()]);
                $this->_assignedThemes[] = $theme;
            } else {
                $this->_unassignedThemes[] = $theme;
            }
        }
        return $this;
    }

    /**
     * Return  customized theme collection
     *
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    protected function _getCustomizedFrontThemes()
    {
        return $this->_theme->getCollection()
            ->addAreaFilter()
            ->addFilter('theme_path', 'ISNULL(theme_path)', 'string');
    }

    /**
     * Return theme ids which are assigned to storeviews or as default
     *
     * @return array
     */
    protected function _getAssignedThemes()
    {
        $assignedTheme = array();
        $stores = $this->_app->getStores();
        foreach ($stores as $store) {
            $themeId = $this->_storeConfig->getConfig(Mage_Core_Model_Design_Package::XML_PATH_THEME_ID, $store);
            if (!isset($assignedTheme[$themeId])) {
                $assignedTheme[$themeId] = array();
            }
            $assignedTheme[$themeId][] = $store;
        }
        return $assignedTheme;
    }
}
