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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product api
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrives store id from store code, if no store id specified,
     * it use seted session or admin store
     *
     * @param string|int $store
     * @return int
     */
    protected function _getStoreId($store = null)
    {
        if (is_null($store)) {
            $store = ($this->_getSession()->hasProductStoreId() ? $this->_getSession()->getStoreProductId() : 0);
        }

        try {
            $storeId = Mage::app()->getStore($store)->getId();
        } catch (Mage_Core_Model_Store_Exception $e) {
            $this->_fault('store_not_exists');
        }

        return $storeId;
    }

    /**
     * Set current store for products.
     *
     * @param unknown_type $store
     * @return unknown
     */
    public function currentStore($store=null)
    {
        if (!is_null($store)) {
            try {
                $storeId = Mage::app()->getStore($store)->getId();
            } catch (Mage_Core_Model_Store_Exception $e) {
                $this->_fault('store_not_exists');
            }

            $this->_getSession()->setProductStoreId($storeId);
        }

        return $this->_getStoreId();
    }

    /**
     * Retrieve list of products with basic info (id, sku, type, set, name)
     *
     * @param array $filters
     * @param string|int $store
     * @return array
     */
    public function items($filters = null, $store = null)
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setStoreId($this->_getStoreId($store))
            ->addAttributeToSelect('name');

        if (is_array($filters)) {
            try {
                foreach ($filters as $field => $value) {
                    $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }

        $result = array();

        foreach ($collection as $product) {
            $result[] = array( // Basic product data
                'product_id' => $product->getId(),
                'sku'        => $product->getSku(),
                'name'       => $product->getName(),
                'set'        => $product->getAttributeSetId(),
                'type'       => $product->getTypeId()
            );
        }

        return $result;
    }

    /**
     * Retrieve product info
     *
     * @param int $productId
     * @param string|int $store
     * @param array $attributes
     * @return array
     */
    public function info($productId, $store = null, $attributes = null)
    {
        $product = Mage::getModel('catalog/product');
        /* @var $product Mage_Catalog_Model_Product */
        $product->setStoreId($this->_getStoreId($store))
            ->load($productId);

        if (!$product->getId()) {
            $this->_fault('not_exists');
        }

        $result = array( // Basic product data
            'product_id' => $product->getId(),
            'sku'        => $product->getSku(),
            'set'        => $product->getAttributeSetId(),
            'type'       => $product->getTypeId(),
            'categories' => $product->getCategoryIds(),
            'websites'   => $product->getWebsiteIds()
        );

        foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
            if (!in_array($attribute->getFrontendInput(), array('media_gallery', 'media_image'))
                && (!is_array($attributes)
                    || in_array($attribute->getId(), $attributes)
                    || in_array($attribute->getAttributeCode(), $attributes))) {
                $result[$attribute->getAttributeCode()] = $product->getData(
                                                                $attribute->getAttributeCode());
            }
        }

        return $result;
    }

    /**
     * Create new product.
     *
     * @param string $type
     * @param int $set
     * @param array $productData
     * @return int
     */
    public function create($type, $set, $productData)
    {
        $product = Mage::getModel('catalog/product');
        /* @var $product Mage_Catalog_Model_Product */
        $product->setStoreId($this->_getStoreId($store))
            ->setAttributeSetId($set)
            ->setTypeId($type);

        foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
            if (!in_array($attribute->getFrontendInput(), array('media_gallery', 'media_image'))
                && isset($productData[$attribute->getAttributeCode()])) {
                $product->setData($attribute->getAttributeCode(), $productData[$attribute->getAttributeCode()]);
            }
        }

        if (isset($productData['categories']) && is_array($productData['categories'])) {
            $product->setCategoryIds($productData['categories']);
        }

        if (isset($productData['websites']) && is_array($productData['websites'])) {
            $product->setWebsiteIds($productData['websites']);
        }

        if (is_array($errors = $product->validate())) {
            $this->_fault('data_invalid', implode("\n", $errors));
        }

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $product->getId();
    }

    /**
     * Update product data
     *
     * @param int $productId
     * @param array $productData
     * @param string|int $store
     * @return boolean
     */
    public function update($productId, $productData = array(), $store = null)
    {
        $product = Mage::getModel('catalog/product');
        /* @var $product Mage_Catalog_Model_Product */
        $product->setStoreId($this->_getStoreId($store))
            ->load($productId);

        if (!$product->getId()) {
            $this->_fault('not_exists');
        }

        foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
            if (!in_array($attribute->getFrontendInput(), array('media_gallery', 'media_image'))
                && isset($productData[$attribute->getAttributeCode()])) {
                $product->setData($attribute->getAttributeCode(), $productData[$attribute->getAttributeCode()]);
            }
        }

        if (isset($productData['categories']) && is_array($productData['categories'])) {
            $product->setCategoryIds($productData['categories']);
        }

        if (isset($productData['websites']) && is_array($productData['websites'])) {
            $product->setWebsiteIds($productData['websites']);
        }

        if (is_array($errors = $product->validate())) {
            $this->_fault('data_invalid', implode("\n", $errors));
        }

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    public function delete($productId)
    {
        $product = Mage::getModel('catalog/product');
        /* @var $product Mage_Catalog_Model_Product */
        $product->load($productId);

        if (!$product->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $product->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }
} // Class Mage_Catalog_Model_Product_Api End