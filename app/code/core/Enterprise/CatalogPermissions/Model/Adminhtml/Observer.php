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
 * Adminhtml observer
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_CatalogPermissions_Model_Adminhtml_Observer
{
    /**
     * Save category permissions on category after save event
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function saveCategoryPermissions(Varien_Event_Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();

        /* @var $category Mage_Catalog_Model_Category */
        if ($category->hasPermissions() && is_array($category->getPermissions())) {
            foreach ($category->getPermissions() as $data) {
                $permission = Mage::getModel('enterprise_catalogpermissions/permission');
                if (!empty($data['id'])) {
                    $permission->load($data['id']);
                }

                if (!empty($data['_deleted'])) {
                    if ($permission->getId()) {
                        $permission->delete();
                    }
                    continue;
                }

                $permission->addData($data);
                if ($permission->getGrantCatalogProductPrice() == -2) {
                    $permission->setGrantCheckoutItems(-2);
                }
                $permission->setCategoryId($category->getId());
                $permission->save();
            }
        }

        return $this;
    }

    /**
     * Add permission tab on category edit page
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function addCategoryPermissionTab(Varien_Event_Observer $observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        /* @var $tabs Mage_Adminhtml_Block_Catalog_Category_Tabs */
        $tabs->addTab(
            'permissions',
            'enterprise_catalogpermissions/adminhtml_catalog_category_tab_permissions'
        );

        return $this;
    }
}