<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\App;

/**
 * Global configs
 */
class Config implements ConfigInterface
{
    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $coreStoreConfig;

    /**
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     */
    public function __construct(\Magento\App\Config\ScopeConfigInterface $coreStoreConfig)
    {
        $this->coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Check, whether permissions are enabled
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->coreStoreConfig->isSetFlag(ConfigInterface::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Return category browsing mode
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getCatalogCategoryViewMode($store = null)
    {
        return $this->coreStoreConfig->getValue(ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Return category browsing groups
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string[]
     */
    public function getCatalogCategoryViewGroups($store = null)
    {
        $groups = $this->coreStoreConfig->getValue(
            ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW . '_groups', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store
        );
        return $groups ? explode(',', $groups) : array();
    }

    /**
     * Return display products mode
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getCatalogProductPriceMode($store = null)
    {
        return $this->coreStoreConfig->getValue(ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Return display products groups
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string[]
     */
    public function getCatalogProductPriceGroups($store = null)
    {
        $groups = $this->coreStoreConfig->getValue(
            ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE . '_groups', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store
        );
        return $groups ? explode(',', $groups) : array();
    }

    /**
     * Return adding to cart mode
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getCheckoutItemsMode($store = null)
    {
        return $this->coreStoreConfig->getValue(ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Return adding to cart groups
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string[]
     */
    public function getCheckoutItemsGroups($store = null)
    {
        $groups = $this->coreStoreConfig->getValue(ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS . '_groups', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $groups ? explode(',', $groups) : [];
    }

    /**
     * Return catalog search prohibited groups
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string[]
     */
    public function getCatalogSearchDenyGroups($store = null)
    {
        $groups = $this->coreStoreConfig->getValue(ConfigInterface::XML_PATH_DENY_CATALOG_SEARCH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $groups ? explode(',', $groups) : [];
    }

    /**
     * Return restricted landing page
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getRestrictedLandingPage($store = null)
    {
        return $this->coreStoreConfig->getValue(ConfigInterface::XML_PATH_LANDING_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
}
