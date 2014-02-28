<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\Helper;

use Magento\App\Helper\Context;
use Magento\Core\Model\Store\ConfigInterface;
use Magento\Customer\Model\Session;

/**
 * Base helper
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    const XML_PATH_GRANT_BASE = 'catalog/magento_catalogpermissions/';
    const XML_PATH_GRANT_CATALOG_CATEGORY_VIEW = 'catalog/magento_catalogpermissions/grant_catalog_category_view';
    const XML_PATH_GRANT_CATALOG_PRODUCT_PRICE = 'catalog/magento_catalogpermissions/grant_catalog_product_price';
    const XML_PATH_GRANT_CHECKOUT_ITEMS = 'catalog/magento_catalogpermissions/grant_checkout_items';
    const XML_PATH_DENY_CATALOG_SEARCH = 'catalog/magento_catalogpermissions/deny_catalog_search';
    const XML_PATH_LANDING_PAGE = 'catalog/magento_catalogpermissions/restricted_landing_page';

    const GRANT_ALL             = 1;
    const GRANT_CUSTOMER_GROUP  = 2;
    const GRANT_NONE            = 0;

    /**
     * Core store config
     *
     * @var ConfigInterface
     */
    protected $_coreStoreConfig;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param ConfigInterface $coreStoreConfig
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ConfigInterface $coreStoreConfig
    ) {
        $this->_customerSession = $customerSession;
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Retrieve config value for category access permission
     *
     * @param int $customerGroupId
     * @param int $storeId
     * @return bool
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
     * @return bool
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
     * @return bool
     */
    public function isAllowedCheckoutItems($storeId = null, $customerGroupId = null)
    {
        return $this->_getIsAllowedGrant(self::XML_PATH_GRANT_CHECKOUT_ITEMS, $storeId, $customerGroupId);
    }


    /**
     * Retrieve config value for catalog search availability
     *
     * @return bool
     */
    public function isAllowedCatalogSearch()
    {
        $groups = trim($this->_coreStoreConfig->getConfig(self::XML_PATH_DENY_CATALOG_SEARCH));

        if ($groups === '') {
            return true;
        }

        $groups = explode(',', $groups);

        return !in_array($this->_customerSession->getCustomerGroupId(), $groups);
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
     * @param int|null $storeId
     * @param int|null $customerGroupId
     * @return bool
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
                $customerGroupId = $this->_customerSession->getCustomerGroupId();
            }

            return in_array(
                $customerGroupId,
                $groups
            );
        }

        return $this->_coreStoreConfig->getConfig($configPath) == self::GRANT_ALL;
    }

    /**
     * Return permission grant config value
     *
     * @param string $node
     * @return mixed
     */
    public function getGrantConfig($node)
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_GRANT_BASE . $node);
    }
}
