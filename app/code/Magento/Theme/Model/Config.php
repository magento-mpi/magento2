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
namespace Magento\Theme\Model;

class Config
{
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $_configWriter;

    /**
     * @var \Magento\Framework\App\Config\ValueInterface
     */
    protected $_configData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Application event manager
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_configCache;

    /**
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_layoutCache;

    /**
     * @param \Magento\Framework\App\Config\ValueInterface $configData
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Cache\FrontendInterface $configCache
     * @param \Magento\Cache\FrontendInterface $layoutCache
     */
    public function __construct(
        \Magento\Framework\App\Config\ValueInterface $configData,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Cache\FrontendInterface $configCache,
        \Magento\Cache\FrontendInterface $layoutCache
    ) {
        $this->_configData = $configData;
        $this->_configWriter = $configWriter;
        $this->_storeManager = $storeManager;
        $this->_eventManager = $eventManager;
        $this->_configCache = $configCache;
        $this->_layoutCache = $layoutCache;
    }

    /**
     * Assign theme to the stores
     *
     * @param \Magento\Framework\View\Design\ThemeInterface $theme
     * @param array $stores
     * @param string $scope
     * @return $this
     */
    public function assignToStore(
        $theme,
        array $stores = array(),
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES
    ) {
        $isReassigned = false;

        $this->_unassignThemeFromStores($theme->getId(), $stores, $scope, $isReassigned);

        if ($this->_storeManager->isSingleStoreMode()) {
            $this->_assignThemeToDefaultScope($theme->getId(), $isReassigned);
        } else {
            $this->_assignThemeToStores($theme->getId(), $stores, $scope, $isReassigned);
        }

        if ($isReassigned) {
            $this->_configCache->clean();
            $this->_layoutCache->clean();
        }

        $this->_eventManager->dispatch(
            'assign_theme_to_stores_after',
            array('stores' => $stores, 'scope' => $scope, 'theme' => $theme)
        );

        return $this;
    }

    /**
     * Get assigned scopes collection of a theme
     *
     * @param string $scope
     * @param string $configPath
     * @return \Magento\Core\Model\Resource\Config\Data\Collection
     */
    protected function _getAssignedScopesCollection($scope, $configPath)
    {
        return $this->_configData->getCollection()->addFieldToFilter(
            'scope',
            $scope
        )->addFieldToFilter(
            'path',
            $configPath
        );
    }

    /**
     * Unassign given theme from stores that were unchecked
     *
     * @param string $themeId
     * @param array $stores
     * @param string $scope
     * @param bool &$isReassigned
     * @return $this
     */
    protected function _unassignThemeFromStores($themeId, $stores, $scope, &$isReassigned)
    {
        $configPath = \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID;
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
     * @param string $themeId
     * @param array $stores
     * @param string $scope
     * @param bool &$isReassigned
     * @return $this
     */
    protected function _assignThemeToStores($themeId, $stores, $scope, &$isReassigned)
    {
        $configPath = \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID;
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
     * @param string $themeId
     * @param bool &$isReassigned
     * @return $this
     */
    protected function _assignThemeToDefaultScope($themeId, &$isReassigned)
    {
        $configPath = \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID;
        $this->_configWriter->save($configPath, $themeId, \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT);
        $isReassigned = true;
        return $this;
    }
}
