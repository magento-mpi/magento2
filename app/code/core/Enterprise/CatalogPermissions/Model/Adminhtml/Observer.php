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
    protected $_indexQueue = array();
    protected $_indexProductQueue = array();

    /**
     * Save category permissions on category after save event
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function saveCategoryPermissions(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        $category = $observer->getEvent()->getCategory();

        /* @var $category Mage_Catalog_Model_Category */
        if ($category->hasData('permissions') && is_array($category->getData('permissions'))) {
            foreach ($category->getData('permissions') as $data) {
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

                if (strlen($data['website_id']) == 0) {
                    $data['website_id'] = null;
                }

                if (strlen($data['customer_group_id']) == 0) {
                    $data['customer_group_id'] = null;
                }

                $permission->addData($data);
                if ($permission->getGrantCatalogCategoryView() == -2) {
                    $permission->setGrantCatalogProductPrice(-2);
                }

                if ($permission->getGrantCatalogProductPrice() == -2) {
                    $permission->setGrantCheckoutItems(-2);
                }
                $permission->setCategoryId($category->getId());
                $permission->save();
            }
        }
        $this->_indexQueue[] = $category->getPath();
        return $this;
    }

    /**
     * Reindex permissions in queue on postdipatch
     *
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindexPermissions()
    {
        if (!empty($this->_indexQueue)) {
            foreach ($this->_indexQueue as $item) {
                Mage::getSingleton('enterprise_catalogpermissions/permission_index')->reindex($item);
            }
            $this->_indexQueue = array();
            Mage::app()->cleanCache(array(Mage_Catalog_Model_Category::CACHE_TAG));
        }

        if (!empty($this->_indexProductQueue)) {
            foreach ($this->_indexProductQueue as $item) {
                Mage::getSingleton('enterprise_catalogpermissions/permission_index')->reindexProducts($item);
            }
            $this->_indexProductQueue = array();
        }

        return $this;
    }

    /**
     * Refresh category related cache on catalog permissions config save
     *
     * @return Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function cleanCacheOnConfigChange()
    {
        Mage::app()->cleanCache(array(Mage_Catalog_Model_Category::CACHE_TAG));
        return $this;
    }

    /**
     * Rebuild index for products
     *
     * @return  Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindexProducts()
    {
        $this->_indexProductQueue[] = null;
        return $this;
    }

    /**
     * Rebuild index
     *
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindex()
    {
        $this->_indexQueue[] = '1';
        return $this;
    }

    /**
     * Rebuild index after product assigned websites
     *
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function reindexAfterProductAssignedWebsite(Varien_Event_Observer $observer)
    {
        $productIds = $observer->getEvent()->getProducts();
        $this->_indexProductQueue = array_merge($this->_indexProductQueue, $productIds);
        return $this;
    }


    /**
     * Save product permission index
     *
     * @param   Varien_Event_Observer $observer
     * @return  Enterprise_CatalogPermissions_Model_Adminhtml_Observer
     */
    public function saveProductPermissionIndex(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $this->_indexProductQueue[] = $product->getId();
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
        if (!Mage::helper('enterprise_catalogpermissions')->isEnabled()) {
            return $this;
        }

        $tabs = $observer->getEvent()->getTabs();
        /* @var $tabs Mage_Adminhtml_Block_Catalog_Category_Tabs */
        $tabs->addTab(
            'permissions',
            'enterprise_catalogpermissions/adminhtml_catalog_category_tab_permissions'
        );

        return $this;
    }
}