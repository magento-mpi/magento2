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
     * @var \Magento\Store\Model\Store\ConfigInterface
     */
    protected $coreStoreConfig;

    /**
     * @param \Magento\Store\Model\Store\ConfigInterface $coreStoreConfig
     */
    public function __construct(\Magento\Store\Model\Store\ConfigInterface $coreStoreConfig)
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
        return $this->coreStoreConfig->getConfigFlag(ConfigInterface::XML_PATH_ENABLED, $store);
    }

    /**
     * Return category browsing mode
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getCatalogCategoryViewMode($store = null)
    {
        return $this->coreStoreConfig->getConfig(ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW, $store);
    }

    /**
     * Return category browsing groups
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string[]
     */
    public function getCatalogCategoryViewGroups($store = null)
    {
        $groups = $this->coreStoreConfig->getConfig(
            ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW . '_groups', $store
        );
        return $groups ? explode(',', $groups) : [];
    }

    /**
     * Return display products mode
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getCatalogProductPriceMode($store = null)
    {
        return $this->coreStoreConfig->getConfig(ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE, $store);
    }

    /**
     * Return display products groups
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string[]
     */
    public function getCatalogProductPriceGroups($store = null)
    {
        $groups = $this->coreStoreConfig->getConfig(
            ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE . '_groups', $store
        );
        return $groups ? explode(',', $groups) : [];
    }

    /**
     * Return adding to cart mode
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getCheckoutItemsMode($store = null)
    {
        return $this->coreStoreConfig->getConfig(ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS, $store);
    }

    /**
     * Return adding to cart groups
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string[]
     */
    public function getCheckoutItemsGroups($store = null)
    {
        $groups = $this->coreStoreConfig->getConfig(ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS . '_groups', $store);
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
        $groups = $this->coreStoreConfig->getConfig(ConfigInterface::XML_PATH_DENY_CATALOG_SEARCH, $store);
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
        return $this->coreStoreConfig->getConfig(ConfigInterface::XML_PATH_LANDING_PAGE, $store);
    }
}
