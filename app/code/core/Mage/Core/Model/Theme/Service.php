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
     * Flag that shows if theme customizations exist in Magento
     *
     * @var bool
     */
    protected $_isCustomizationsExist;

    /**
     * Theme customizations which are assigned to store views or as default
     *
     * @see self::_prepareThemeCustomizations()
     * @var array
     */
    protected $_assignedThemeCustomizations;

    /**
     * Theme customizations which are not assigned to store views or as default
     *
     * @see self::_prepareThemeCustomizations()
     * @var array
     */
    protected $_unassignedThemeCustomizations;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * Initialize service model
     *
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Design_Package $design
     * @param Mage_Core_Model_App $app
     * @param Mage_Core_Helper_Data $helper
     */
    public function __construct(
        Mage_Core_Model_Theme $theme,
        Mage_Core_Model_Design_Package $design,
        Mage_Core_Model_App $app,
        Mage_Core_Helper_Data $helper
    ) {
        $this->_theme = $theme;
        $this->_design = $design;
        $this->_app = $app;
        $this->_helper = $helper;
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
        $area = Mage_Core_Model_App_Area::AREA_FRONTEND
    ) {
        if (!$this->_theme->load($themeId)->getId()) {
            throw new UnexpectedValueException('Theme is not recognized. Requested id: ' . $themeId);
        }
        $this->_theme = $this->_createThemeCustomization();
        $configPath = $this->_design->getConfigPathByArea($area);

        foreach ($this->_getAssignedScopesCollection($themeId, $scope) as $config) {
            if (!in_array($config->getScopeId(), $stores)) {
                $this->_app->getConfig()->deleteConfig($configPath, $scope, $config->getScopeId());
            }
        }

        foreach ($stores as $storeId) {
            $this->_app->getConfig()->saveConfig($configPath, $this->_theme->getId(), $scope, $storeId);
        }
        return $this;
    }

    /**
     * Create theme customization
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _createThemeCustomization()
    {
        if ($this->_theme->isVirtual()) {
            return $this->_theme;
        }

        $themeCopyCount = $this->_getCustomizedFrontThemes()->addFilter('parent_id', $this->_theme->getId())->count();
        $this->_theme->setParentId($this->_theme->getId())->setThemePath(null)
            ->setThemeTitle(
            $this->_theme->getThemeTitle() . ' - ' . $this->_helper->__('Copy') . ' #' . ++$themeCopyCount
        )->createPreviewImageCopy();

        $originalData = $this->_theme->getData();
        unset($originalData[$this->_theme->getIdFieldName()]);
        $this->_theme = clone $this->_theme;
        $this->_theme->addData($originalData);
        return $this->_theme->save();
    }

    /**
     * Get assigned scopes collection of a theme
     *
     * @param int $themeId
     * @param string $scope
     * @return Mage_Core_Model_Resource_Config_Data_Collection
     */
    protected function _getAssignedScopesCollection($themeId, $scope)
    {
        return $this->_app->getConfig()->getConfigDataModel()->getCollection()
            ->addFieldToSelect(array('scope_id'))
            ->addFieldToFilter('scope', $scope)
            ->addFieldToFilter('value', $themeId);
    }

    /**
     * Check whether theme customizations exist in Magento
     *
     * @return bool
     */
    public function isCustomizationsExist()
    {
        if (is_null($this->_isCustomizationsExist)) {
            $this->_isCustomizationsExist = false;
            /** @var $theme Mage_Core_Model_Theme */
            foreach ($this->_theme->getCollection() as $theme) {
                if ($theme->isVirtual()) {
                    $this->_isCustomizationsExist = true;
                    break;
                }
            }
        }
        return $this->_isCustomizationsExist;
    }

    /**
     * Return frontend theme collection by page. Theme customizations are not included, only phisical themes.
     *
     * @param int $page
     * @param int $pageSize
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function getThemes($page, $pageSize)
    {
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->_theme->getCollection();
        $collection->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)
            ->addFilter('theme_path', 'theme_path IS NOT NULL', 'string')
            ->setPageSize($pageSize);
        return $collection->setCurPage($page);
    }

    /**
     * Return theme customizations which are assigned to store views
     *
     * @see self::_prepareThemeCustomizations()
     * @return array
     */
    public function getAssignedThemeCustomizations()
    {
        if (is_null($this->_assignedThemeCustomizations)) {
            $this->_prepareThemeCustomizations();
        }
        return $this->_assignedThemeCustomizations;
    }

    /**
     * Return theme customizations which are not assigned to store views.
     *
     * @see self::_prepareThemeCustomizations()
     * @return array
     */
    public function getUnassignedThemeCustomizations()
    {
        if (is_null($this->_unassignedThemeCustomizations)) {
            $this->_prepareThemeCustomizations();
        }
        return $this->_unassignedThemeCustomizations;
    }

    /**
     * Fetch theme customization and sort them out to arrays "_assignedThemeCustomizations" and "_unassignedThemeCustomizations".
     *
     * NOTE: To get into "assigned" list theme customization not necessary should be assigned to store-view directly.
     * It can be set to website or as default theme and be used by store-view via config fallback mechanism.
     *
     * @return Mage_Core_Model_Theme_Service
     */
    protected function _prepareThemeCustomizations()
    {
        /** @var $themeCustomizations Mage_Core_Model_Resource_Theme_Collection */
        $themeCustomizations = $this->_getThemeCustomizations();
        $assignedThemes = $this->getStoresByThemes();

        $this->_assignedThemeCustomizations = array();
        $this->_unassignedThemeCustomizations = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($themeCustomizations as $theme) {
            if (isset($assignedThemes[$theme->getId()])) {
                $theme->setAssignedStores($assignedThemes[$theme->getId()]);
                $this->_assignedThemeCustomizations[] = $theme;
            } else {
                $this->_unassignedThemeCustomizations[] = $theme;
            }
        }
        return $this;
    }

    /**
     * Return theme customizations collection
     *
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    protected function _getThemeCustomizations()
    {
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->_theme->getCollection();
        $collection->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)
            ->addFilter('theme_path', 'theme_path IS NULL', 'string');
        return $collection;
    }

    /**
     * Return stores grouped by assigned themes
     *
     * @return array
     */
    public function getStoresByThemes()
    {
        $storesByThemes = array();
        $stores = $this->_app->getStores();
        /** @var $store Mage_Core_Model_Store */
        foreach ($stores as $store) {
            $themeId = (int)$store->getConfig(Mage_Core_Model_Design_Package::XML_PATH_THEME_ID);

            if (!isset($storesByThemes[$themeId])) {
                $storesByThemes[$themeId] = array();
            }
            $storesByThemes[$themeId][] = $store;
        }

        return $storesByThemes;
    }
}
