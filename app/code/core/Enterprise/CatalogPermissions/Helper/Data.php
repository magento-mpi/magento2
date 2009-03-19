<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base helper
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */

class Enterprise_CatalogPermissions_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_GRANT_CATALOG_CATEGORY_VIEW = 'enterprise_catalogpermissions/general/grant_catalog_category_view';
    const XML_PATH_GRANT_CATALOG_PRODUCT_PRICE = 'enterprise_catalogpermissions/general/grant_catalog_product_price';
    const XML_PATH_GRANT_CHECKOUT_ITEMS = 'enterprise_catalogpermissions/general/grant_checkout_items';

    /**
     * Retrieve config value for category access permission
     *
     * @return boolean
     */
    public function isAllowedCategoryView()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW);
    }

    /**
     * Retrieve config value for product price permission
     *
     * @return boolean
     */
    public function isAllowedProductPrice()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE);
    }

    /**
     * Retrieve config value for checkout items permission
     *
     * @return boolean
     */
    public function isAllowedCheckoutItems()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_GRANT_CHECKOUT_ITEMS);
    }
}
