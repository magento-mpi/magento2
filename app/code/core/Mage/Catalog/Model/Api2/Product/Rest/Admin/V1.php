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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 for products instance (Admin)
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Product_Rest
{
    /**
     * Product helper
     *
     * @var Mage_Catalog_Model_Api2_Helper
     */
    protected $_productResourceHelper;

    /**
     * Initialize product resource helper
     */
    function __construct()
    {
        $this->_productResourceHelper = Mage::getSingleton('catalog/api2_helper');
    }

    /**
     * Retrieve product data
     *
     * @return array
     */
    protected function _retrieve()
    {
        $product = $this->_getProduct();
        $productData = $product->getData();
        return $productData;
    }

    /**
     * Retrieve list of products
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        // TODO: Check category filter
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $store = $this->_getStore();
        $collection->setStoreId($store->getId());
        $collection->addAttributeToSelect(array_keys(
            $this->getAvailableAttributes($this->getUserType(), Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        ));
        $this->_applyCollectionModifiers($collection);
        $products = $collection->load()->toArray();
        return $products;
    }

    /**
     * Delete product by its ID
     *
     * @throws Mage_Api2_Exception
     */
    protected function _delete()
    {
        $product = $this->_getProduct();
        try {
            $product->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Create product
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        /* @var $validator Mage_Catalog_Model_Api2_Product_Validator_Product */
        $validator = Mage::getModel('catalog/api2_product_validator_product', array(
            'operation' => self::OPERATION_CREATE
        ));

        if (!$validator->isSatisfiedByData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $type = $data['type_id'];
        if ($type !== 'simple') {
            $this->_critical("Creation of products with type '$type' is not implemented",
                Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED);
        }
        $set = $data['attribute_set_id'];
        $sku = $data['sku'];

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        $this->_productResourceHelper->prepareDataForSave($product, $data);
        try {
            $product->validate();
            $product->save();
            $this->_multicall($product->getId());
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_critical(sprintf('Invalid attribute "%s": %s', $e->getAttributeCode(), $e->getMessage()),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }

        return $this->_getLocation($product);
    }

    /**
     * Update product by its ID
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->_getProduct();
        /* @var $validator Mage_Catalog_Model_Api2_Product_Validator_Product */
        $validator = Mage::getModel('catalog/api2_product_validator_product', array(
            'operation' => self::OPERATION_UPDATE,
            'product'   => $product
        ));

        if (!$validator->isSatisfiedByData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }
        if (isset($data['sku'])) {
            $product->setSku($data['sku']);
        }
        $this->_productResourceHelper->setIsUpdate(true)->prepareDataForSave($product, $data);
        try {
            $product->validate();
            $product->save();
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_critical(sprintf('Invalid attribute "%s": %s', $e->getAttributeCode(), $e->getMessage()),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }
    }

    /**
     * Check if store exist by its code or ID
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        $store = $this->getRequest()->getParam('store');
        try {
            if (is_null($store)) {
                $store = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
            }
            $store = Mage::app()->getStore($store);
        } catch (Mage_Core_Model_Store_Exception $e) {
            // store does not exist
            $this->_critical('Requested store is invalid', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        return $store;
    }
}
