<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Model\Config;

/**
 * Theme customization config model
 */
class Customization
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\View\DesignInterface
     */
    protected $_design;

    /**
     * @var \Magento\View\Design\Theme\ThemeProviderInterface
     */
    protected $themeProvider;

    /**
     * Theme customizations which are assigned to store views or as default
     *
     * @var array
     * @see self::_prepareThemeCustomizations()
     */
    protected $_assignedTheme;

    /**
     * Theme customizations which are not assigned to store views or as default
     *
     * @var array
     * @see self::_prepareThemeCustomizations()
     */
    protected $_unassignedTheme;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\View\DesignInterface $design
     * @param \Magento\View\Design\Theme\ThemeProviderInterface $themeProvider
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\View\DesignInterface $design,
        \Magento\View\Design\Theme\ThemeProviderInterface $themeProvider
    ) {
        $this->_storeManager = $storeManager;
        $this->_design = $design;
        $this->themeProvider = $themeProvider;
    }

    /**
     * Return theme customizations which are assigned to store views
     *
     * @see self::_prepareThemeCustomizations()
     * @return array
     */
    public function getAssignedThemeCustomizations()
    {
        if (is_null($this->_assignedTheme)) {
            $this->_prepareThemeCustomizations();
        }
        return $this->_assignedTheme;
    }

    /**
     * Return theme customizations which are not assigned to store views.
     *
     * @see self::_prepareThemeCustomizations()
     * @return array
     */
    public function getUnassignedThemeCustomizations()
    {
        if (is_null($this->_unassignedTheme)) {
            $this->_prepareThemeCustomizations();
        }
        return $this->_unassignedTheme;
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
        /** @var $store \Magento\Store\Model\Store */
        foreach ($stores as $store) {
            $themeId = $this->_getConfigurationThemeId($store);
            if (!isset($storesByThemes[$themeId])) {
                $storesByThemes[$themeId] = array();
            }
            $storesByThemes[$themeId][] = $store;
        }
        return $storesByThemes;
    }

    /**
     * Check if current theme has assigned to any store
     *
     * @param \Magento\View\Design\ThemeInterface $theme
     * @param null|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isThemeAssignedToStore($theme, $store = null)
    {
        if (null === $store) {
            $assignedThemes = $this->getAssignedThemeCustomizations();
            return isset($assignedThemes[$theme->getId()]);
        }
        return $this->_isThemeAssignedToSpecificStore($theme, $store);
    }

    /**
     * Check if there are any themes assigned
     *
     * @return bool
     */
    public function hasThemeAssigned()
    {
        return count($this->getAssignedThemeCustomizations()) > 0;
    }

    /**
     * Is theme assigned to specific store
     *
     * @param \Magento\View\Design\ThemeInterface $theme
     * @param \Magento\Store\Model\Store $store
     * @return bool
     */
    protected function _isThemeAssignedToSpecificStore($theme, $store)
    {
        return $theme->getId() == $this->_getConfigurationThemeId($store);
    }

    /**
     * Get configuration theme id
     *
     * @param \Magento\Store\Model\Store $store
     * @return int
     */
    protected function _getConfigurationThemeId($store)
    {
        return $this->_design->getConfigurationDesignTheme(
            \Magento\Core\Model\App\Area::AREA_FRONTEND,
            array('store' => $store)
        );
    }

    /**
     * Fetch theme customization and sort them out to arrays:
     * self::_assignedTheme and self::_unassignedTheme.
     *
     * NOTE: To get into "assigned" list theme customization not necessary should be assigned to store-view directly.
     * It can be set to website or as default theme and be used by store-view via config fallback mechanism.
     *
     * @return $this
     */
    protected function _prepareThemeCustomizations()
    {
        /** @var \Magento\Core\Model\Resource\Theme\Collection $themeCollection */
        $themeCollection = $this->themeProvider->getThemeCustomizations(\Magento\Core\Model\App\Area::AREA_FRONTEND);

        $assignedThemes = $this->getStoresByThemes();

        $this->_assignedTheme = array();
        $this->_unassignedTheme = array();

        /** @var $theme \Magento\View\Design\ThemeInterface */
        foreach ($themeCollection as $theme) {
            if (isset($assignedThemes[$theme->getId()])) {
                $theme->setAssignedStores($assignedThemes[$theme->getId()]);
                $this->_assignedTheme[$theme->getId()] = $theme;
            } else {
                $this->_unassignedTheme[$theme->getId()] = $theme;
            }
        }

        return $this;
    }
}
