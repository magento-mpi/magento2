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
 * API2 for products collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Products_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Products_Rest
{
    /**
     * The greatest value which could be stored in CatalogInventory Qty field
     */
    const MAX_QTY_VALUE = 99999999.9999;

    /**
     * Pre-validate request data
     *
     * @param array $data
     * @param array $required
     * @param array $notEmpty
     */
    protected function _validate(array $data, array $required = array(), array $notEmpty = array())
    {
        parent::_validate($data, $required, $notEmpty);

        if (!isset($data['type']) || empty($data['type'])) {
            $this->_critical('Missing "type" in request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if (!isset($data['set']) || empty($data['set'])) {
            $this->_critical('Missing "set" in request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        // Validate attribute set
        $setId = $data['set'];
        /** @var $entity Mage_Eav_Model_Entity_Type */
        $entity = Mage::getModel('eav/entity_type')->loadByCode(Mage_Catalog_Model_Product::ENTITY);
        /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
        $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($setId);
        if (!$attributeSet->getId() || $entity->getEntityTypeId() != $attributeSet->getEntityTypeId()) {
            $this->_critical('Invalid attribute set', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        // Validate product type
        $type = $data['type'];
        $productTypes = Mage_Catalog_Model_Product_Type::getTypes();
        if (!array_key_exists($type, $productTypes)) {
            $this->_critical('Invalid product type', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        // Validate store
        if (isset($data['store'])) {
            try {
                Mage::app()->getStore($data['store']);
            } catch (Mage_Core_Model_Store_Exception $e) {
                $this->_critical('Invalid store', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
        }
        // Collect required EAV attributes, validate applicable attributes and validate source attributes values
        $requiredAttributes = array('set');
        $positiveNumberAttributes = array('weight', 'price', 'special_price', 'msrp');
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        foreach ($entity->getAttributeCollection($setId) as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $value = false;
            $isSet = false;
            if (isset($data[$attribute->getAttributeCode()])) {
                $value = $data[$attribute->getAttributeCode()];
                $isSet = true;
            }
            $applicable = false;
            if (!$attribute->getApplyTo() || in_array($type, $attribute->getApplyTo())) {
                $applicable = true;
            }

            if (!$applicable && !$attribute->isStatic() && $isSet) {
                $this->_error(sprintf('Attribute "%s" is not applicable for product type "%s"', $attributeCode,
                    $productTypes[$type]['label']), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            if ($applicable && !empty($value)) {
                // Validate dropdown attributes
                if ($attribute->usesSource()) {
                    $allowedValues = $this->_getAttributeAllowedValues($attribute->getSource()->getAllOptions());
                    if (!in_array($value, $allowedValues)) {
                        $this->_error(sprintf('Invalid value for attribute "%s".', $attributeCode),
                            Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                    }
                }
                // Validate datetime attributes
                if ($attribute->getBackendType() == 'datetime') {
                    try {
                        $attribute->getBackend()->formatDate($value);
                    } catch (Zend_Date_Exception $e) {
                        $this->_error(sprintf('Invalid date in the "%s" field.', $attributeCode),
                            Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                    }
                }
                // Validate positive number required attributes
                if (in_array($attributeCode, $positiveNumberAttributes)
                    && !Zend_Validate::is($value, 'GreaterThan', array(0))) {
                    $this->_error(sprintf('Please enter a number 0 or greater in the "%s" field.', $attributeCode),
                        Mage_Api2_Model_Server::HTTP_BAD_REQUEST
                    );
                }
            }

            if ($applicable && $attribute->getIsRequired() && $attribute->getIsVisible()) {
                $requiredAttributes[] = $attribute->getAttributeCode();
            }
        }

        $this->_validateSku($data['sku']);
        if (isset($data['stock_data']) && is_array($data['stock_data'])) {
            $this->_validateStockData($data['stock_data']);
        }
        // @TODO: implement tier price & group price validation & tests

        parent::_validate($data, $requiredAttributes, $requiredAttributes);
    }

    /**
     * Validate SKU
     *
     * @param string $sku
     */
    protected function _validateSku($sku)
    {
        if (!Zend_Validate::is($sku, 'StringLength', array('min' => 0, 'max' => 64))) {
            $this->_error('The SKU length should be 64 characters maximum.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Validate product inventory data
     *
     * @param array $data
     */
    protected function _validateStockData($data)
    {
        if (isset($data['manage_stock']) && (bool)$data['manage_stock'] == true) {
            if (!isset($data['qty'])) {
                $this->_error('Missing "stock_data:qty" in request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            if (isset($data['qty']) && empty($data['qty'])) {
                $this->_error('Empty value for "stock_data:qty" in request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Retrieve all attribute allowed values from source model in plain array format
     *
     * @param array $options
     * @return array
     */
    protected function _getAttributeAllowedValues(array $options)
    {
        $values = array();
        foreach ($options as $option) {
            if (isset($option['value'])) {
                $value = $option['value'];
                if (is_array($value)) {
                    $values = array_merge($values, $this->_getAttributeAllowedValues($value));
                } else {
                    $values[] = $value;
                }
            }
        }

        return $values;
    }

    /**
     * Create product
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        $this->_validate($data);

        $type = $data['type'];
        $set = $data['set'];
        $sku = $data['sku'];
        $productData = array_diff_key($data, array_flip(array('type', 'set', 'sku')));

        $store = isset($data['store']) ? $data['store'] : '';
        $storeId = Mage::app()->getStore($store)->getId();
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')
            ->setStoreId($storeId)
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        $this->_prepareDataForSave($product, $productData);
        try {
            $product->save();
            $this->_multicall($product->getId());
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }

        return $this->_getLocation($product);
    }

    /**
     *  Set additional data before product saved
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $productData
     */
    protected function _prepareDataForSave($product, $productData)
    {
        if (isset($productData['stock_data'])) {
            $this->_filterStockData($productData['stock_data']);
            $product->setStockData($productData['stock_data']);
        }

        if (isset($productData['website_ids']) && is_array($productData['website_ids'])) {
            $product->setWebsiteIds($productData['website_ids']);
        }
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            //Unset data if object attribute has no value in current store
            if (Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID !== (int)$product->getStoreId()
                && !$product->getExistsStoreValueFlag($attribute->getAttributeCode())
                && !$attribute->isScopeGlobal()) {
                $product->setData($attribute->getAttributeCode(), false);
            }

            if ($this->_isAllowedAttribute($attribute)) {
                if (isset($productData[$attribute->getAttributeCode()])) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData[$attribute->getAttributeCode()]
                    );
                }
            }
        }
    }

    /**
     * Filter stock data values
     *
     * @param array $stockData
     */
    protected function _filterStockData(&$stockData) {
        if (!isset($stockData['use_config_manage_stock'])) {
            $stockData['use_config_manage_stock'] = 0;
        }
        if (!isset($stockData['use_config_manage_stock'])) {
            $stockData['original_inventory_qty'] = 0;
        }
        if (isset($stockData['qty'])
            && (float)$stockData['qty'] > self::MAX_QTY_VALUE) {
            $stockData['qty'] = self::MAX_QTY_VALUE;
        }
        if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
            $stockData['min_qty'] = 0;
        }
        if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
            $stockData['is_decimal_divided'] = 0;
        }
    }

    /**
     * Check is attribute allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute, $attributes = null)
    {
        if (is_array($attributes) && !(in_array($attribute->getAttributeCode(), $attributes)
            || in_array($attribute->getAttributeId(), $attributes))
        ) {
            return false;
        }

        $ignoredAttributeTypes = array();
        $ignoredAttributeCodes = array('entity_id', 'attribute_set_id', 'entity_type_id');

        return !in_array($attribute->getFrontendInput(), $ignoredAttributeTypes)
            && !in_array($attribute->getAttributeCode(), $ignoredAttributeCodes);
    }
}
