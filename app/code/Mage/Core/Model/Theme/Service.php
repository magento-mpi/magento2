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
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Core_Model_Theme_Service
{
    /**
     * @var Mage_Core_Model_Theme_Factory
     */
    protected $_themeFactory;

    /**
     * @var Mage_Core_Model_Theme_CopyService
     */
    protected $_themeCopyService;

    /**
     * @var Mage_Core_Model_View_DesignInterface
     */
    protected $_design;

    /**
     * @var Mage_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Flag that shows if theme customizations exist in Magento
     *
     * @var bool
     */
    protected $_isCustomized;

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
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * Application event manager
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Configuration writer
     *
     * @var Mage_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * @var Magento_Cache_FrontendInterface
     */
    protected $_configCache;

    /**
     * @var Magento_Cache_FrontendInterface
     */
    protected $_layoutCache;

    /**
     * @param Mage_Core_Model_Theme_Factory $themeFactory
     * @param Mage_Core_Model_Theme_CopyService $themeCopyService
     * @param Mage_Core_Model_View_DesignInterface $design
     * @param Mage_Core_Model_StoreManagerInterface $storeManager
     * @param Mage_Core_Helper_Data $helper
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param Magento_Cache_FrontendInterface $configCache
     * @param Magento_Cache_FrontendInterface $layoutCache
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Mage_Core_Model_Theme_Factory $themeFactory,
        Mage_Core_Model_Theme_CopyService $themeCopyService,
        Mage_Core_Model_View_DesignInterface $design,
        Mage_Core_Model_StoreManagerInterface $storeManager,
        Mage_Core_Helper_Data $helper,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_Config_Storage_WriterInterface $configWriter,
        Magento_Cache_FrontendInterface $configCache,
        Magento_Cache_FrontendInterface $layoutCache
    ) {
        $this->_themeFactory = $themeFactory;
        $this->_themeCopyService = $themeCopyService;
        $this->_design       = $design;
        $this->_storeManager = $storeManager;
        $this->_helper       = $helper;
        $this->_eventManager = $eventManager;
        $this->_configWriter = $configWriter;
        $this->_configCache  = $configCache;
        $this->_layoutCache  = $layoutCache;
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
    public function reassignThemeToStores(
        $themeId,
        array $stores = array(),
        $scope = Mage_Core_Model_Config::SCOPE_STORES
    ) {
        /** @var $theme Mage_Core_Model_Theme */
        $theme = $this->_themeFactory->create()->load($themeId);
        if (!$theme->getId()) {
            throw new UnexpectedValueException('Theme is not recognized. Requested id: ' . $themeId);
        }

        $themeCustomization = $theme->isVirtual() ? $theme : $this->createThemeCustomization($theme);

        $isReassigned = false;
        $this->_unassignThemeFromStores($themeId, $stores, $scope, $isReassigned);
        $this->_assignThemeToStores($themeCustomization->getId(), $stores, $scope, $isReassigned);

        if ($isReassigned) {
            $this->_configCache->clean();
        }

        $this->_layoutCache->clean();

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
     * Unassign given theme from stores that were unchecked
     *
     * @param int $themeId
     * @param array $stores
     * @param string $scope
     * @param bool $isReassigned
     * @return Mage_Core_Model_Theme_Service
     */
    protected function _unassignThemeFromStores($themeId, $stores, $scope, &$isReassigned)
    {
        $configPath = Mage_Core_Model_View_Design::XML_PATH_THEME_ID;
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
     * Assign given theme to stores
     *
     * @param int $themeId
     * @param array $stores
     * @param string $scope
     * @param bool $isReassigned
     * @return Mage_Core_Model_Theme_Service
     */
    protected function _assignThemeToStores($themeId, $stores, $scope, &$isReassigned)
    {
        $configPath = Mage_Core_Model_View_Design::XML_PATH_THEME_ID;
        if (count($stores) > 0) {
            foreach ($stores as $storeId) {
                $this->_configWriter->save($configPath, $themeId, $scope, $storeId);
                $isReassigned = true;
            }
        }
        return $this;
    }

    /**
     * Assign given theme in default scope
     *
     * @param int $themeId
     * @return $this
     */
    public function assignThemeToDefaultScope($themeId)
    {
        $scope = Mage_Core_Model_Config::SCOPE_DEFAULT;

        $configPath = Mage_Core_Model_View_Design::XML_PATH_THEME_ID;
        $this->_configWriter->save($configPath, $themeId, $scope);
        $this->_configCache->clean();

        return $this;
    }

    /**
     * Create theme customization
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Theme
     */
    public function createThemeCustomization($theme)
    {
        $themeCopyCount = $this->_getThemeCustomizations()->addFilter('parent_id', $theme->getId())->count();

        $themeData = $theme->getData();
        $themeData['parent_id'] = $theme->getId();
        $themeData['theme_id'] = null;
        $themeData['theme_path'] = null;
        $themeData['theme_title'] = $theme->getThemeTitle() . ' - ' . $this->_helper->__('Copy') . ' #'
            . ($themeCopyCount + 1);
        $themeData['type'] = Mage_Core_Model_Theme::TYPE_VIRTUAL;

        /** @var $themeCustomization Mage_Core_Model_Theme */
        $themeCustomization = $this->_themeFactory->create()->setData($themeData);
        $themeCustomization->getThemeImage()->createPreviewImageCopy();
        $themeCustomization->save();

        $this->_themeCopyService->copy($theme, $themeCustomization);

        return $themeCustomization;
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
        return Mage::getSingleton('Mage_Core_Model_Config_Data')->getCollection()
            ->addFieldToFilter('scope', $scope)
            ->addFieldToFilter('path', $configPath);
    }

    /**
     * Check whether theme customizations exist in Magento
     *
     * @return bool
     */
    public function isCustomizationsExist()
    {
        if ($this->_isCustomized === null) {
            $this->_isCustomized = (bool)$this->_themeFactory->create()->getCollection()
                ->addTypeFilter(Mage_Core_Model_Theme::TYPE_VIRTUAL)
                ->getSize();
        }
        return $this->_isCustomized;
    }

    /**
     * Return frontend physical theme collection.
     * All themes or per page if set page and page size (page size is optional)
     *
     * @param int $page
     * @param int $pageSize
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function getPhysicalThemes(
        $page = null,
        $pageSize = Mage_Core_Model_Resource_Theme_Collection::DEFAULT_PAGE_SIZE
    ) {
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->_themeFactory->create()->getCollection();
        $collection->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)
            ->addTypeFilter(Mage_Core_Model_Theme::TYPE_PHYSICAL);
        if ($page) {
            $collection->setPageSize($pageSize)->setCurPage($page);
        }
        return $collection;
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
     * Get theme by id
     *
     * @param int $themeId
     * @return Mage_Core_Model_Theme
     */
    public function getThemeById($themeId)
    {
        return $this->_themeFactory->create()->load($themeId);
    }

    /**
     * Fetch theme customization and sort them out to arrays:
     * self::_assignedThemeCustomizations and self::_unassignedThemeCustomizations.
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
