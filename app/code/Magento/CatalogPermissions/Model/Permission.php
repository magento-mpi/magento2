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
 * Permission model
 *
 * @method \Magento\CatalogPermissions\Model\Resource\Permission _getResource()
 * @method \Magento\CatalogPermissions\Model\Resource\Permission getResource()
 * @method int getCategoryId()
 * @method \Magento\CatalogPermissions\Model\Permission setCategoryId(int $value)
 * @method int getWebsiteId()
 * @method \Magento\CatalogPermissions\Model\Permission setWebsiteId(int $value)
 * @method int getCustomerGroupId()
 * @method \Magento\CatalogPermissions\Model\Permission setCustomerGroupId(int $value)
 * @method int getGrantCatalogCategoryView()
 * @method \Magento\CatalogPermissions\Model\Permission setGrantCatalogCategoryView(int $value)
 * @method int getGrantCatalogProductPrice()
 * @method \Magento\CatalogPermissions\Model\Permission setGrantCatalogProductPrice(int $value)
 * @method int getGrantCheckoutItems()
 * @method \Magento\CatalogPermissions\Model\Permission setGrantCheckoutItems(int $value)
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogPermissions\Model;

class Permission extends \Magento\Core\Model\AbstractModel
{
    const PERMISSION_ALLOW = -1;
    const PERMISSION_DENY = -2;
    const PERMISSION_PARENT = 0;

    /**
     * Initialize model
     */
    protected function _construct()
    {
        $this->_init('Magento\CatalogPermissions\Model\Resource\Permission');
    }
}
