<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Config model
 */
class Mage_Theme_Model_Config
{
    /**
     * @var Mage_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * @var Mage_Core_Model_Config_Data
     */
    protected $_configData;

    /**
     * @var Mage_Core_Model_Theme_Factory
     */
    protected $_themeFactory;

    /**
     * @var Mage_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Application event manager
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Magento_Cache_FrontendInterface
     */
    protected $_configCache;

    /**
     * @var Magento_Cache_FrontendInterface
     */
    protected $_layoutCache;

    /**
     * @var Mage_Core_Model_Design_PackageInterface
     */
    protected $_design;

    /**
     * Theme customizations which are assigned to store views or as default
     *
     * @see self::_prepareThemeCustomizations()
     * @var array
     */
    protected $_assignedThemeC;

    /**
     * Theme customizations which are not assigned to store views or as default
     *
     * @see self::_prepareThemeCustomizations()
     * @var array
     */
    protected $_unassignedThemeC;

    /**
     * @param Mage_Core_Model_Config_Data $configData
     * @param Mage_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param Mage_Core_Model_Theme_Factory $themeFactory
     * @param Mage_Core_Model_StoreManagerInterface $storeManager,
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Magento_Cache_FrontendInterface $configCache
     * @param Magento_Cache_FrontendInterface $layoutCache
     * @param Mage_Core_Model_Design_PackageInterface $design
     */
    public function __construct(
        Mage_Core_Model_Config_Data $configData,
        Mage_Core_Model_Config_Storage_WriterInterface $configWriter,
        Mage_Core_Model_Theme_Factory $themeFactory,
        Mage_Core_Model_StoreManagerInterface $storeManager,
        Mage_Core_Model_Event_Manager $eventManager,
        Magento_Cache_FrontendInterface $configCache,
        Magento_Cache_FrontendInterface $layoutCache,
        Mage_Core_Model_Design_PackageInterface $design
    ) {
        $this->_configData   = $configData;
        $this->_configWriter = $configWriter;
        $this->_themeFactory = $themeFactory;
        $this->_storeManager = $storeManager;
        $this->_eventManager = $eventManager;
        $this->_configCache  = $configCache;
        $this->_layoutCache  = $layoutCache;
        $this->_design       = $design;
    }

    /**
     * Assign theme to the stores
     *
     * @param int $themeId
     * @param array $stores
     * @param string $scope
     * @return Mage_Core_Model_Theme
     * @throws UnexpectedValueException
     */
    public function assignToStore(
        $themeId,
        array $stores = array(),
        $scope = Mage_Core_Model_Config::SCOPE_STORES
    )
    {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_themeFactory->create()->load($themeId);
        if (!$theme->getId()) {
            throw new UnexpectedValueException('Theme is not recognized. Requested id: ' . $themeId);
        }
        $themeCustomization = $this->_getThemeCustomization($theme);

        $isReassigned = false;
        $this->_unassignThemeFromStores($themeId, $stores, $scope, $isReassigned);
        if ($this->_storeManager->isSingleStoreMode()) {
            $this->_assignThemeToDefaultScope($themeCustomization->getId(), $isReassigned);
        } else {
            $this->_assignThemeToStores($themeCustomization->getId(), $stores, $scope, $isReassigned);
        }

        if ($isReassigned) {
            $this->_configCache->clean();
            $this->_layoutCache->clean();
        }

        $this->_eventManager->dispatch('assign_theme_to_stores_after',
            array(
                'themeService'       => $this,
                'themeId'            => $themeId,
                'stores'             => $stores,
                'scope'              => $scope,
                'theme'              => $theme,
                'themeCustomization' => $themeCustomization,
            )
        );

        return $themeCustomization;
    }

    /**
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Theme
     */
    protected function _getThemeCustomization($theme)
    {
        $themeCustomization = $theme->isVirtual() ? $theme : $this->_themeFactory->createThemeCustomization($theme);
        return $themeCustomization;
    }

    /**
     * Unassign given theme from stores that were unchecked
     *
     * @param int $themeId
     * @param array $stores
     * @param string $scope
     * @param bool $isReassigned
     * @return $this
     */
    protected function _unassignThemeFromStores($themeId, $stores, $scope, &$isReassigned)
    {
        $configPath = Mage_Core_Model_Design_Package::XML_PATH_THEME_ID;
        /** @var $config Mage_Core_Model_Config_Data */
        foreach ($this->_getAssignedScopesCollection($scope, $configPath) as $config) {
            if ($config->getValue() == $themeId && !in_array($config->getScopeId(), $stores)) {
                $this->_configWriter->delete($configPath, $scope, $config->getScopeId());
                $isReassigned = true;
            }
        }
        return $this;
    }

    /**
     * Get assigned scopes collection of a theme
     *
     * @param string $scope
     * @param string $configPath
     * @return Mage_Core_Model_Resource_Config_Data_Collection
     */
    protected function _getAssignedScopesCollection($scope, $configPath)
    {
        return $this->_configData->getCollection()
            ->addFieldToFilter('scope', $scope)
            ->addFieldToFilter('path', $configPath);
    }

    /**
     * Assign given theme to stores
     *
     * @param int $themeId
     * @param array $stores
     * @param string $scope
     * @param bool $isReassigned
     * @return $this
     */
    protected function _assignThemeToStores($themeId, $stores, $scope, &$isReassigned)
    {
        $configPath = Mage_Core_Model_Design_Package::XML_PATH_THEME_ID;
        if (count($stores) > 0) {
            foreach ($stores as $storeId) {
                $this->_configWriter->save($configPath, $themeId, $scope, $storeId);
                $isReassigned = true;
            }
        }
        return $this;
    }

    /**
     * @param int $themeId
     * @param bool $isReassigned
     * @return $this
     */
    protected function _assignThemeToDefaultScope($themeId, &$isReassigned)
    {
        $configPath = Mage_Core_Model_Design_Package::XML_PATH_THEME_ID;
        $this->_configWriter->save($configPath, $themeId, Mage_Core_Model_Config::SCOPE_DEFAULT);
        $isReassigned = true;
        return $this;
    }


    /**
     * Check if current theme has assigned to any store
     *
     * @param Mage_Core_Model_Theme $theme
     * @return bool
     */
    public function isThemeAssignedToStore(Mage_Core_Model_Theme $theme)
    {
        $assignedThemes = $this->getAssignedThemeCustomizations();
        return isset($assignedThemes[$theme->getId()]);
    }

    /**
     * Return theme customizations which are assigned to store views
     *
     * @see self::_prepareThemeCustomizations()
     * @return array
     */
    public function getAssignedThemeCustomizations()
    {
        if (is_null($this->_assignedThemeC)) {
            $this->_prepareThemeCustomizations();
        }
        return $this->_assignedThemeC;
    }

    /**
     * Return theme customizations which are not assigned to store views.
     *
     * @see self::_prepareThemeCustomizations()
     * @return array
     */
    public function getUnassignedThemeCustomizations()
    {
        if (is_null($this->_unassignedThemeC)) {
            $this->_prepareThemeCustomizations();
        }
        return $this->_unassignedThemeC;
    }

    /**
     * Fetch theme customization and sort them out to arrays:
     * self::_assignedThemeCustomizations and self::_unassignedThemeCustomizations.
     *
     * NOTE: To get into "assigned" list theme customization not necessary should be assigned to store-view directly.
     * It can be set to website or as default theme and be used by store-view via config fallback mechanism.
     *
     * @return $this
     */
    protected function _prepareThemeCustomizations()
    {
        /** @var $themeCustomizations Mage_Core_Model_Resource_Theme_Collection */
        $themeCustomizations = $this->_getThemeCustomizations();
        $assignedThemes = $this->getStoresByThemes();

        $this->_assignedThemeC = array();
        $this->_unassignedThemeC = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($themeCustomizations as $theme) {
            if (isset($assignedThemes[$theme->getId()])) {
                $theme->setAssignedStores($assignedThemes[$theme->getId()]);
                $this->_assignedThemeC[$theme->getId()] = $theme;
            } else {
                $this->_unassignedThemeC[$theme->getId()] = $theme;
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
        $collection = $this->_themeFactory->create()->getCollection();
        $collection->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)
            ->addTypeFilter(Mage_Core_Model_Theme::TYPE_VIRTUAL);
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
        $stores = $this->_storeManager->getStores();
        /** @var $store Mage_Core_Model_Store */
        foreach ($stores as $store) {
            $themeId = $this->_design->getConfigurationDesignTheme(
                Mage_Core_Model_App_Area::AREA_FRONTEND,
                array('store' => $store)
            );
            if (!isset($storesByThemes[$themeId])) {
                $storesByThemes[$themeId] = array();
            }
            $storesByThemes[$themeId][] = $store;
        }

        return $storesByThemes;
    }

    /**
     * Is theme assigned to specific store
     *
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Store $store
     * @return bool
     */
    public function isThemeAssignedToSpecificStore($theme, $store)
    {
        $themeId = $this->_design->getConfigurationDesignTheme(
            Mage_Core_Model_App_Area::AREA_FRONTEND,
            array('store' => $store)
        );

        return $theme->getId() == $themeId;
    }
}
