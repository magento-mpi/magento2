<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogPermissions\Model;

class Permission extends \Magento\Framework\Model\AbstractModel
{
    const PERMISSION_ALLOW = -1;

    const PERMISSION_DENY = -2;

    const PERMISSION_PARENT = 0;

    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CatalogPermissions\Model\Resource\Permission');
    }

    /**
     * Update permissions before save
     *
     * @return Permission
     */
    public function preparePermission()
    {
        $viewPermission = $this->getGrantCatalogCategoryView();
        if (self::PERMISSION_DENY == $viewPermission) {
            $this->setGrantCatalogProductPrice(self::PERMISSION_DENY);
        }

        $pricePermission = $this->getGrantCatalogProductPrice();
        if (self::PERMISSION_DENY == $pricePermission) {
            $this->setGrantCheckoutItems(self::PERMISSION_DENY);
        }

        return $this;
    }

    /**
     * Processing object before save data
     *
     * @return Permission
     */
    public function beforeSave()
    {
        $this->preparePermission();
        return parent::beforeSave();
    }
}
