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
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Whether is present customized themes
     *
     * @var bool
     */
    protected $_hasCustomizedThemes;

    /**
     * Customized themes which are assigned to store views or as default
     *
     * @var array
     */
    protected $_assignedThemes;

    /**
     * Customized themes which are not assigned to store views or as default
     *
     * @var array
     */
    protected $_unassignedThemes;

    /**
     * Initialize service model
     *
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Design_Package $design
     * @param Mage_Core_Model_App $app
     */
    public function __construct(
        Mage_Core_Model_Theme $theme,
        Mage_Core_Model_Design_Package $design,
        Mage_Core_Model_App $app
    ) {
        $this->_theme = $theme;
        $this->_design = $design;
        $this->_app = $app;
    }

    /**
     * Assign theme to the stores
     *
     * @param int $themeId
     * @param array $stores
     * @param string $scope
     * @param string $area
     * @return Mage_Core_Model_Theme_Service
     * @throws UnexpectedValueException
     */
    public function assignThemeToStores($themeId, $stores = array(), $scope = Mage_Core_Model_Config::SCOPE_STORES,
        $area = Mage_Core_Model_Design_Package::DEFAULT_AREA
    ) {
        if (!$this->_theme->load($themeId)->getId()) {
            throw new UnexpectedValueException('Theme doesn\'t recognized. Requested id: ' . $themeId);
        }
        foreach ($stores as $storeId) {
            $this->_app->getConfig()->saveConfig(
                $this->_design->getConfigPathByArea($area), $this->_theme->getId(), $scope, $storeId
            );
        }
        return $this;
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
     * @param int $pageSize
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function getNotCustomizedFrontThemes($page, $pageSize)
    {
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->_theme->getCollection()->addAreaFilter();
        $collection->getSelect()->where('theme_path IS NOT NULL');
        return $collection->setPageSize($pageSize)
            ->setCurPage($page);
    }

    /**
     * Return customized themes which are assigned to store views or as default
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
     * Return customized themes which are not assigned to store views or as default
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
        $assignedThemes = $this->_getStoresByThemes();

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
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->_theme->getCollection()->addAreaFilter();
        $collection->getSelect()->where('theme_path IS NULL');
        return $collection;
    }

    /**
     * Return stores grouped by assigned themes
     *
     * @return array
     */
    protected function _getStoresByThemes()
    {
        $assignedTheme = array();
        $stores = $this->_app->getStores();
        foreach ($stores as $store) {
            $themeId = $store->getConfig(Mage_Core_Model_Design_Package::XML_PATH_THEME_ID, $store);
            if (!isset($assignedTheme[$themeId])) {
                $assignedTheme[$themeId] = array();
            }
            $assignedTheme[$themeId][] = $store;
        }
        return $assignedTheme;
    }
}
