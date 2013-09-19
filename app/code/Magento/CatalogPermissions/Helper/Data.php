<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base helper
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */

namespace Magento\CatalogPermissions\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'catalog/magento_catalogpermissions/enabled';
    const XML_PATH_GRANT_CATALOG_CATEGORY_VIEW = 'catalog/magento_catalogpermissions/grant_catalog_category_view';
    const XML_PATH_GRANT_CATALOG_PRODUCT_PRICE = 'catalog/magento_catalogpermissions/grant_catalog_product_price';
    const XML_PATH_GRANT_CHECKOUT_ITEMS = 'catalog/magento_catalogpermissions/grant_checkout_items';
    const XML_PATH_DENY_CATALOG_SEARCH = 'catalog/magento_catalogpermissions/deny_catalog_search';
    const XML_PATH_LANDING_PAGE = 'catalog/magento_catalogpermissions/restricted_landing_page';

    const GRANT_ALL             = 1;
    const GRANT_CUSTOMER_GROUP  = 2;
    const GRANT_NONE            = 0;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager = null;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_eventManager = $eventManager;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Retrieve config value for permission enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Retrieve config value for category access permission
     *
     * @param int $customerGroupId
     * @param int $storeId
     * @return boolean
     */
    public function isAllowedCategoryView($storeId = null, $customerGroupId = null)
    {
        return $this->_getIsAllowedGrant(self::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW, $storeId, $customerGroupId);
    }

    /**
     * Retrieve config value for product price permission
     *
     * @param int $customerGroupId
     * @param int $storeId
     * @return boolean
     */
    public function isAllowedProductPrice($storeId = null, $customerGroupId = null)
    {
        return $this->_getIsAllowedGrant(self::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE, $storeId, $customerGroupId);
    }

    /**
     * Retrieve config value for checkout items permission
     *
     * @param int $customerGroupId
     * @param int $storeId
     * @return boolean
     */
    public function isAllowedCheckoutItems($storeId = null, $customerGroupId = null)
    {
        return $this->_getIsAllowedGrant(self::XML_PATH_GRANT_CHECKOUT_ITEMS, $storeId, $customerGroupId);
    }


    /**
     * Retrieve config value for catalog search availability
     *
     * @return boolean
     */
    public function isAllowedCatalogSearch()
    {
        $groups = trim($this->_coreStoreConfig->getConfig(self::XML_PATH_DENY_CATALOG_SEARCH));

        if ($groups === '') {
            return true;
        }

        $groups = explode(',', $groups);

        return !in_array(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerGroupId(), $groups);
    }

    /**
     * Retrieve landing page url
     *
     * @return string
     */
    public function getLandingPageUrl()
    {
        return $this->_getUrl('', array('_direct' => $this->_coreStoreConfig->getConfig(self::XML_PATH_LANDING_PAGE)));
    }

    /**
     * Retrieve is allowed grant from configuration
     *
     * @param string $configPath
     * @return boolean
     */
    protected function _getIsAllowedGrant($configPath, $storeId = null, $customerGroupId = null)
    {
        if ($this->_coreStoreConfig->getConfig($configPath, $storeId) == self::GRANT_CUSTOMER_GROUP) {
            $groups = trim($this->_coreStoreConfig->getConfig($configPath . '_groups', $storeId));

            if ($groups === '') {
                return false;
            }

            $groups = explode(',', $groups);

            if ($customerGroupId === null) {
                $customerGroupId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerGroupId();
            }

            return in_array(
                $customerGroupId,
                $groups
            );
        }

        return $this->_coreStoreConfig->getConfig($configPath) == self::GRANT_ALL;
    }
}
