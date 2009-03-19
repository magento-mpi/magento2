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
 * Permission model
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_CatalogPermissions_Model_Observer
{
    const XML_PATH_GRANT_CATALOG_CATEGORY_VIEW = 'enterprise_catalogpermissions/general/grant_catalog_category_view';
    const XML_PATH_GRANT_CATALOG_PRODUCT_PRICE = 'enterprise_catalogpermissions/general/grant_catalog_product_price';
    const XML_PATH_GRANT_CHECKOUT_ITEMS = 'enterprise_catalogpermissions/general/grant_checkout_items';

    /**
     * Permissions collection
     *
     * @var Enterprise_CatalogPermissions_Model_Mysql4_Permission_Collection
     */
    protected $_permissionCollection = null;

    /**
     * Inheritance of grant appling in categories tree
     *
     * @return array
     */
    protected $_grantsInheritance = array(
        'grant_catalog_category_view' => 'deny',
        'grant_catalog_product_price' => 'allow',
        'grant_checkout_items' => 'allow'
    );

    public function applyCategoryPermissionOnLoadCollection(Varien_Event_Observer $observer)
    {
        $categoryCollection = $observer->getEvent()->getCategoryCollection();
        $categoryIds = $categoryCollection->getColumnValues('entity_id');
        $permissions = $this->_getIndexModel()->getIndexForCategory($categoryIds, $this->_getCustomerGroupId(), $this->_getWebsiteId());

        foreach ($permissions as $categoryId => $permission) {
            $categoryCollection->getItemById($categoryId)->setPermissions($permission);
        }

        foreach ($categoryCollection as $category) {
            $this->_applyPermissionsOnCategory($category);
        }

        return $this;
    }

    public function applyCategoryPermissionOnLoadNodes(Varien_Event_Observer $observer)
    {
        $nodes = $observer->getEvent()->getNodes();
        $categoryIds = array_keys($nodes);
        $permissions = $this->_getIndexModel()->getIndexForCategory($categoryIds, $this->_getCustomerGroupId(), $this->_getWebsiteId());

        foreach ($permissions as $categoryId => $permission) {
            $nodes[$categoryId]->setPermissions($permission);
        }

        foreach ($nodes as $category) {
            $this->_applyPermissionsOnCategory($category);
        }

        return $this;
    }


    public function applyCategoryPermissionOnProductCount(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $this->_getIndexModel()->addIndexToProductCount($collection, $this->_getCustomerGroupId());
        return $this;
    }

    public function applyCategoryPermissionOnLoadModel(Varien_Event_Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();

        $permissions = $this->_getIndexModel()->getIndexForCategory($category->getId(), $this->_getCustomerGroupId(), $this->_getWebsiteId());

        if (isset($permissions[$category->getId()])) {
            $category->setPermissions($permissions[$category->getId()]);
        }

        $this->_applyPermissionsOnCategory($category);

        return $this;
    }

    public function applyProductPermissionOnCollection(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $this->_getIndexModel()->addIndexToProductCollection($collection, $this->_getCustomerGroupId());
        return $this;
    }

    public function applyProductPermissionOnCollectionAfterLoad(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        foreach ($collection as $product) {
            $this->_applyPermissionsOnProduct($product);
        }
        return $this;
    }

    public function applyProductPermissionOnModelAfterLoad(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        $this->_getIndexModel()->addIndexToProduct($product, $this->_getCustomerGroupId());

        $this->_applyPermissionsOnProduct($product);

        return $this;
    }


    /**
     * Apply category related permissions on category
     *
     * @param Varien_Data_Tree_Node|Mage_Catalog_Model_Category
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    protected function _applyPermissionsOnCategory($category)
    {
        if ($category->getData('permissions/grant_catalog_category_view') == -2 ||
            ($category->getData('permissions/grant_catalog_category_view')!= -1 &&
                !Mage::helper('enterprise_catalogpermissions')->isAllowedCategoryView())) {
            $category->setIsActive(0);
        }

        return $this;
    }

    /**
     * Apply category related permissions on product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Enterprise_CatalogPermissions_Model_Observer
     */
    protected function _applyPermissionsOnProduct($product)
    {
        if ($product->getData('grant_catalog_product_price') == -2 ||
            ($product->getData('grant_catalog_product_price')!= -1 &&
                !Mage::helper('enterprise_catalogpermissions')->isAllowedProductPrice())) {
            $product->setCanShowPrice(false);
            if ($product->isSalable()) {
                $product->setIsSalable(false);
            }
        }

        if ($product->getData('grant_checkout_items') == -2 ||
            ($product->getData('grant_checkout_items')!= -1 &&
                !Mage::helper('enterprise_catalogpermissions')->isAllowedCheckoutItems())) {
            if ($product->isSalable()) {
                $product->setIsSalable(false);
            }
        }

        return $this;
    }

    /**
     * Retreive current customer group id
     *
     * @return int
     */
    protected function _getCustomerGroupId()
    {
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    /**
     * Retreive permission index model
     *
     * @return Enterprise_CatalogPermissions_Model_Permission_Index
     */
    protected function _getIndexModel()
    {
        return Mage::getSingleton('enterprise_catalogpermissions/permission_index');
    }

    /**
     * Retreive current website id
     *
     * @return int
     */
    protected function _getWebsiteId()
    {
        return Mage::app()->getStore()->getWebsiteId();
    }
}