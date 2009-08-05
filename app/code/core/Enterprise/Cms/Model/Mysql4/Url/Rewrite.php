<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Url rewrite resource model class
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Model_Mysql4_Url_Rewrite extends Mage_Core_Model_Mysql4_Url_Rewrite
{
    /**
     * Retrieve request_path using product's sku and current store's id.
     *
     * @param string $sku
     * @param int|Mage_Core_Model_Store $store
     * @return string|false
     */
    public function retrieveRequestPathBySkuStore($sku, $store)
    {
        $storeId = $this->_prepareStoreId($store);

        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'request_path')
            ->join(array('p' => $this->getTable('catalog/product')), 'p.entity_id = main_table.product_id', array())
            ->where('p.sku = ?', $sku)
            ->where('main_table.category_id IS NULL')
            ->where('main_table.store_id = ?', $storeId)
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }


    /**
     * Retrieve request_path using id_path and current store's id.
     *
     * @param string $idPath
     * @param int|Mage_Core_Model_Store $store
     * @return string|false
     */
    public function retrieveRequestPathByIdPath($idPath, $store)
    {
        $storeId = $this->_prepareStoreId($store);

        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'request_path')
            ->where('main_table.store_id = ?', $storeId)
            ->where('main_table.id_path = ?', $idPath)
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieve request_path using category's url key and current store's id.
     *
     * @param string $idPath
     * @param int|Mage_Core_Model_Store $store
     * @return string|false
     */
    public function retrieveRequestPathByUrlKey($urlKey, $store)
    {
        $storeId = $this->_prepareStoreId($store);

        $attrCode = 'url_key';
        $category = Mage::getResourceSingleton('catalog/category');
        /* @var $category Mage_Catalog_Model_Resource_Eav_Mysql4_Category */
        $attrUrlKey = $category->getAttribute($attrCode);
        /* @var $attrUrlKey Mage_Eav_Model_Entity_Attribute_Abstract */
        $attrField = $attrUrlKey->getBackend()->isStatic() ? $attrCode : 'value';

        $select = $this->_getReadAdapter()->select();
        /* @var $select Zend_Db_Select */
        $select->from(array('main_table' => $this->getMainTable()), 'request_path')
            ->join(array('c' => $attrUrlKey->getBackend()->getTable()), 'c.entity_id = main_table.category_id', array())
            ->where('c.' . $attrField . ' = ?', $urlKey)
            ->where('main_table.store_id = ?', $storeId)
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Prepare int store's id value
     *
     * @param mixed $store
     * @return int
     */
    protected function _prepareStoreId($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $storeId = (int)$store->getId();
        } else {
            $storeId = (int)$store;
        }

        return $storeId;
    }
}
