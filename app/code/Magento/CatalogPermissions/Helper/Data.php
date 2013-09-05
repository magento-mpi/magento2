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

class Magento_CatalogPermissions_Helper_Data extends Magento_Core_Helper_Abstract
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
     * Retrieve config value for permission enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED);
    }

    /**
     * Check category permission is allowed
     *
     * @param Magento_Catalog_Model_Category $category
     * @return boolean
     */
    public function isAllowedCategory($category)
    {
        $options = new \Magento\Object();
        $options->setCategory($category);
        $options->setIsAllowed(true);

        Mage::dispatchEvent('magento_catalog_permissions_is_allowed_category', array('options' => $options));

        return $options->getIsAllowed();
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
        $groups = trim(Mage::getStoreConfig(self::XML_PATH_DENY_CATALOG_SEARCH));

        if ($groups === '') {
            return true;
        }

        $groups = explode(',', $groups);

        return !in_array(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId(), $groups);
    }

    /**
     * Retrieve landing page url
     *
     * @return string
     */
    public function getLandingPageUrl()
    {
        return $this->_getUrl('', array('_direct' => Mage::getStoreConfig(self::XML_PATH_LANDING_PAGE)));
    }

    /**
     * Retrieve is allowed grant from configuration
     *
     * @param string $configPath
     * @return boolean
     */
    protected function _getIsAllowedGrant($configPath, $storeId = null, $customerGroupId = null)
    {
        if (Mage::getStoreConfig($configPath, $storeId) == self::GRANT_CUSTOMER_GROUP) {
            $groups = trim(Mage::getStoreConfig($configPath . '_groups', $storeId));

            if ($groups === '') {
                return false;
            }

            $groups = explode(',', $groups);

            if ($customerGroupId === null) {
                $customerGroupId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId();
            }

            return in_array(
                $customerGroupId,
                $groups
            );
        }

        return Mage::getStoreConfig($configPath) == self::GRANT_ALL;
    }
}
