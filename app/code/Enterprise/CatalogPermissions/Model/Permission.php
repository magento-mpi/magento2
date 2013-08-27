<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Permission model
 *
 * @method Enterprise_CatalogPermissions_Model_Resource_Permission _getResource()
 * @method Enterprise_CatalogPermissions_Model_Resource_Permission getResource()
 * @method int getCategoryId()
 * @method Enterprise_CatalogPermissions_Model_Permission setCategoryId(int $value)
 * @method int getWebsiteId()
 * @method Enterprise_CatalogPermissions_Model_Permission setWebsiteId(int $value)
 * @method int getCustomerGroupId()
 * @method Enterprise_CatalogPermissions_Model_Permission setCustomerGroupId(int $value)
 * @method int getGrantCatalogCategoryView()
 * @method Enterprise_CatalogPermissions_Model_Permission setGrantCatalogCategoryView(int $value)
 * @method int getGrantCatalogProductPrice()
 * @method Enterprise_CatalogPermissions_Model_Permission setGrantCatalogProductPrice(int $value)
 * @method int getGrantCheckoutItems()
 * @method Enterprise_CatalogPermissions_Model_Permission setGrantCheckoutItems(int $value)
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CatalogPermissions_Model_Permission extends Magento_Core_Model_Abstract
{
    const PERMISSION_ALLOW = -1;
    const PERMISSION_DENY = -2;
    const PERMISSION_PARENT = 0;

    /**
     * Initialize model
     */
    protected function _construct()
    {
        $this->_init('Enterprise_CatalogPermissions_Model_Resource_Permission');
    }
}
