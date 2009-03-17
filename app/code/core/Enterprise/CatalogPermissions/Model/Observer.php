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

    /**
     * Applies permission grants by category name
     *
     * @return
     */
    public function applyCategoryPermissionOnCategoryLoad(Varien_Event_Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();
        /* @var $category Mage_Catalog_Model_Category */


    }

    /**
     * Get permission grants by categories path ids
     *
     * @param string|array $pathIds
     * @return Varien_Object
     */
    protected function _getGrantsByPathIds($pathIds)
    {
        if (is_string($pathIds)) {
            $pathIds = explode('/', $pathIds);
        }

        $grants = new Varien_Object();

        foreach ($pathIds as $categoryId) {
            $permission = $this->_getPermissionCollection()->getItemByColumnValue('category_id', $categoryId);
            if (!$permission) {
                continue;
            }

            foreach ($this->_grantsInheritance as $grantName => $inheritance) {
                if ($permission->getData($grantName) == 0) {
                    continue;
                }

                if (!$grants->hasData($grantName)) {
                    $grants->setData($grantName, $permission->getData($grantName));
                }

                $permissionGrant = $permission->getData($grantName);
                $currentGrant = $grants->getData($grantName);

                if ($inheritance == 'allow') {
                    $currentGrant = max($permissionGrant, $currentGrant);
                }

                $grants->setData($grantName, min($permissionGrant, $currentGrant));
            }
        }

        foreach ($this->_grantsInheritance as $grantName => $inheritance) {
            if (!$grants->hasData($grantName)) {
                $grants->setData($grantName, Mage::getStoreConfigFlag(constant('self::XML_PATH_' . strtoupper($grantName))));
            } else {
                $grants->setData($grantName, $grants->getData($grantName) == -1);
            }
        }

        return $grants;
    }

    /**
     * Retrieve permissions collection
     *
     * @return Enterprise_CatalogPermissions_Model_Mysql4_Permission_Collection
     */
    protected function _getPermissionCollection()
    {
        if ($this->_permissionCollection = null) {
            $this->_permissionCollection = Mage::getModel('enterprise_catalogpermissions/permission')
                ->getCollection();

            $this->_permissionCollection->addCategoryLevel()
                ->addCategoryIsActiveFilter()
                ->setScopeFilter(Mage::getSingleton('customer/session')->getCutomerGroupId());
        }

        return $this->_permissionCollection;
    }

}