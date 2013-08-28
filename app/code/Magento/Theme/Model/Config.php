<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Config model
 */
class Magento_Theme_Model_Config
{
    /**
     * @var Magento_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * @var Magento_Core_Model_Config_Data
     */
    protected $_configData;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Application event manager
     *
     * @var Magento_Core_Model_Event_Manager
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
     * @param Magento_Core_Model_Config_Data $configData
     * @param Magento_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Cache_FrontendInterface $configCache
     * @param Magento_Cache_FrontendInterface $layoutCache
     */
    public function __construct(
        Magento_Core_Model_Config_Data $configData,
        Magento_Core_Model_Config_Storage_WriterInterface $configWriter,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Cache_FrontendInterface $configCache,
        Magento_Cache_FrontendInterface $layoutCache
    ) {
        $this->_configData   = $configData;
        $this->_configWriter = $configWriter;
        $this->_storeManager = $storeManager;
        $this->_eventManager = $eventManager;
        $this->_configCache  = $configCache;
        $this->_layoutCache  = $layoutCache;
    }

    /**
     * Assign theme to the stores
     *
     * @param Magento_Core_Model_Theme $theme
     * @param array $stores
     * @param string $scope
     * @return $this
     */
    public function assignToStore($theme, array $stores = array(), $scope = Magento_Core_Model_Config::SCOPE_STORES)
    {
        $isReassigned = false;

        $this->_unassignThemeFromStores(
            $theme->getId(), $stores, $scope, $isReassigned
        );

        if ($this->_storeManager->isSingleStoreMode()) {
            $this->_assignThemeToDefaultScope($theme->getId(), $isReassigned);
        } else {
            $this->_assignThemeToStores($theme->getId(), $stores, $scope, $isReassigned);
        }

        if ($isReassigned) {
            $this->_configCache->clean();
            $this->_layoutCache->clean();
        }

        $this->_eventManager->dispatch('assign_theme_to_stores_after',
            array(
                'stores' => $stores,
                'scope'  => $scope,
                'theme'  => $theme,
            )
        );

        return $this;
    }

    /**
     * Get assigned scopes collection of a theme
     *
     * @param string $scope
     * @param string $configPath
     * @return Magento_Core_Model_Resource_Config_Data_Collection
     */
    protected function _getAssignedScopesCollection($scope, $configPath)
    {
        return $this->_configData->getCollection()
            ->addFieldToFilter('scope', $scope)
            ->addFieldToFilter('path', $configPath);
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
        $configPath = Magento_Core_Model_View_Design::XML_PATH_THEME_ID;
        /** @var $config Magento_Core_Model_Config_Data */
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
     * @return $this
     */
    protected function _assignThemeToStores($themeId, $stores, $scope, &$isReassigned)
    {
        $configPath = Magento_Core_Model_View_Design::XML_PATH_THEME_ID;
        if (count($stores) > 0) {
            foreach ($stores as $storeId) {
                $this->_configWriter->save($configPath, $themeId, $scope, $storeId);
                $isReassigned = true;
            }
        }
        return $this;
    }

    /**
     * Assign theme to default scope
     *
     * @param int $themeId
     * @param bool $isReassigned
     * @return $this
     */
    protected function _assignThemeToDefaultScope($themeId, &$isReassigned)
    {
        $configPath = Magento_Core_Model_View_Design::XML_PATH_THEME_ID;
        $this->_configWriter->save($configPath, $themeId, Magento_Core_Model_Config::SCOPE_DEFAULT);
        $isReassigned = true;
        return $this;
    }
}
