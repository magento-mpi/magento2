<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product api V2
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Api_V2 extends Magento_Catalog_Model_Product_Api
{
    /**
     * Retrieve product info
     *
     * @param int|string $productId
     * @param string|int $store
     * @param stdClass $attributes
     * @param string $identifierType if set to sku, then consider the productId to be a SKU even if it is numeric
     * @return array
     */
    public function info($productId, $store = null, $attributes = null, $identifierType = null)
    {
        // make sku flag case-insensitive
        if (!empty($identifierType)) {
            $identifierType = strtolower($identifierType);
        }

        $product = $this->_getProduct($productId, $store, $identifierType);

        $result = array( // Basic product data
            'product_id' => $product->getId(),
            'sku'        => $product->getSku(),
            'set'        => $product->getAttributeSetId(),
            'type'       => $product->getTypeId(),
            'categories' => $product->getCategoryIds(),
            'websites'   => $product->getWebsiteIds()
        );

        $allAttributes = array();
        if (!empty($attributes->attributes)) {
            $allAttributes = array_merge($allAttributes, $attributes->attributes);
        } else {
            foreach ($product->getTypeInstance()->getEditableAttributes($product) as $attribute) {
                if ($this->_isAllowedAttribute($attribute, $attributes)) {
                    $allAttributes[] = $attribute->getAttributeCode();
                }
            }
        }

        $_additionalAttributeCodes = array();
        if (!empty($attributes->additional_attributes)) {
            foreach ($attributes->additional_attributes as $k => $_attributeCode) {
                $allAttributes[] = $_attributeCode;
                $_additionalAttributeCodes[] = $_attributeCode;
            }
        }

        $_additionalAttribute = 0;
        foreach ($product->getTypeInstance()->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $allAttributes)) {
                if (in_array($attribute->getAttributeCode(), $_additionalAttributeCodes)) {
                    $result['additional_attributes'][$_additionalAttribute]['key'] = $attribute->getAttributeCode();
                    $result['additional_attributes'][$_additionalAttribute]['value'] = $product
                        ->getData($attribute->getAttributeCode());
                    $_additionalAttribute++;
                } else {
                    $result[$attribute->getAttributeCode()] = $product->getData($attribute->getAttributeCode());
                }
            }
        }

        return $result;
    }

    /**
     * Create new product.
     *
     * @param string $type
     * @param int $set
     * @param string $sku
     * @param array $productData
     * @param string $store
     * @return int
     */
    public function create($type, $set, $sku, $productData, $store = null)
    {
        if (!$type || !$set || !$sku) {
            $this->_fault('data_invalid');
        }

        $this->_checkProductTypeExists($type);
        $this->_checkProductAttributeSet($set);

        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->setStoreId($this->_getStoreId($store))
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        if (!property_exists($productData, 'stock_data')) {
            //Set default stock_data if not exist in product data
            $_stockData = array('use_config_manage_stock' => 0);
            $product->setStockData($_stockData);
        }

        foreach ($product->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $product->setData($mediaAttrCode, 'no_selection');
        }

        $this->_prepareDataForSave($product, $productData);

        try {
            $product->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $product->getId();
    }

    /**
     * Update product data
     *
     * @param int|string $productId
     * @param array $productData
     * @param string|int $store
     * @return boolean
     */
    public function update($productId, $productData, $store = null, $identifierType = null)
    {
        $product = $this->_getProduct($productId, $store, $identifierType);

        $this->_prepareDataForSave($product, $productData);

        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             * @todo see Magento_Catalog_Model_Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                $strErrors = array();
                foreach ($errors as $code => $error) {
                    if ($error === true) {
                        $error = __('Please correct the value for "%1".', $code);
                    } else {
                        $error = __('Please correct the value for "%1": %2',
                            $code, $error);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            $product->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Update multiple products information at once
     *
     * @param array      $productIds
     * @param array      $productData
     * @param string|int $store
     * @param string     $identifierType
     * @return boolean
     */
    public function multiUpdate($productIds, $productData, $store = null, $identifierType = null)
    {
        if (count($productIds) != count($productData)) {
            $this->_fault('multi_update_not_match');
        }

        $productData = (array)$productData;
        $failMessages = array();

        foreach ($productIds as $index => $productId) {
            try {
                $this->update($productId, $productData[$index], $store, $identifierType);
            } catch (Magento_Api_Exception $e) {
                $failMessages[] = sprintf("Product ID %d:\n %s", $productId, $e->getMessage());
            }
        }

        if (empty($failMessages)) {
            return true;
        } else {
            $this->_fault('partially_updated', implode("\n", $failMessages));
        }

        return false;
    }

    /**
     *  Set additional data before product saved
     *
     * @param Magento_Catalog_Model_Product $product
     * @param array $productData
     * @return object
     */
    protected function _prepareDataForSave ($product, $productData)
    {
        if (property_exists($productData, 'website_ids') && is_array($productData->website_ids)) {
            $product->setWebsiteIds($productData->website_ids);
        }

        if (property_exists($productData, 'additional_attributes')) {
            if (property_exists($productData->additional_attributes, 'single_data')) {
                foreach ($productData->additional_attributes->single_data as $_attribute) {
                    $_attrCode = $_attribute->key;
                    $productData->$_attrCode = $_attribute->value;
                }
            }
            if (property_exists($productData->additional_attributes, 'multi_data')) {
                foreach ($productData->additional_attributes->multi_data as $_attribute) {
                    $_attrCode = $_attribute->key;
                    $productData->$_attrCode = $_attribute->value;
                }
            }
            unset($productData->additional_attributes);
        }

        foreach ($product->getTypeInstance()->getEditableAttributes($product) as $attribute) {
            $_attrCode = $attribute->getAttributeCode();

            //Unset data if object attribute has no value in current store
            if (Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID !== (int) $product->getStoreId()
                && !$product->getExistsStoreValueFlag($_attrCode)
                && !$attribute->isScopeGlobal()
            ) {
                $product->setData($_attrCode, false);
            }

            if ($this->_isAllowedAttribute($attribute) && (isset($productData->$_attrCode))) {
                $product->setData(
                    $_attrCode,
                    $productData->$_attrCode
                );
            }
        }

        if (property_exists($productData, 'categories') && is_array($productData->categories)) {
            $product->setCategoryIds($productData->categories);
        }

        if (property_exists($productData, 'websites') && is_array($productData->websites)) {
            foreach ($productData->websites as &$website) {
                if (is_string($website)) {
                    try {
                        $website = Mage::app()->getWebsite($website)->getId();
                    } catch (Exception $e) {
                    }
                }
            }
            $product->setWebsiteIds($productData->websites);
        }

        if (Mage::app()->hasSingleStore()) {
            $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
        }

        if (property_exists($productData, 'stock_data')) {
            $_stockData = array();
            foreach ($productData->stock_data as $key => $value) {
                $_stockData[$key] = $value;
            }
            $product->setStockData($_stockData);
        }

        if (property_exists($productData, 'tier_price')) {
             $tierPrices = Mage::getModel('Magento_Catalog_Model_Product_Attribute_Tierprice_Api_V2')
                 ->prepareTierPrices($product, $productData->tier_price);
             $product->setData(Magento_Catalog_Model_Product_Attribute_Tierprice_Api_V2::ATTRIBUTE_CODE, $tierPrices);
        }

        /** @var $helper Magento_Api_Helper_Data */
        $helper = $this->_apiData;
        $helper->v2AssociativeArrayUnpacker($productData);
        $helper->toArray($productData);
        $this->_prepareConfigurableAttributes($product, $productData);
    }

    /**
     * Update product special price
     *
     * @param int|string $productId
     * @param float $specialPrice
     * @param string $fromDate
     * @param string $toDate
     * @param string|int $store
     * @param string $identifierType OPTIONAL If 'sku' - search product by SKU, if any except for NULL - search by ID,
     *                                        otherwise - try to determine identifier type automatically
     * @return boolean
     */
    public function setSpecialPrice($productId, $specialPrice = null, $fromDate = null, $toDate = null, $store = null,
        $identifierType = null
    ) {
        $obj = new stdClass();
        $obj->special_price = $specialPrice;
        $obj->special_from_date = $fromDate;
        $obj->special_to_date = $toDate;
        return $this->update($productId, $obj, $store, $identifierType);
    }

    /**
     * Retrieve product special price
     *
     * @param int|string $productId
     * @param string|int $store
     * @return array
     */
    public function getSpecialPrice($productId, $store = null)
    {
        $product = $this->_getProduct($productId, $store);

        $result = array(
            'special_price'     => $product->getSpecialPrice(),
            'special_from_date' => $product->getSpecialFromDate(),
            'special_to_date'   => $product->getSpecialToDate(),
        );

        return $result;
    }
}
