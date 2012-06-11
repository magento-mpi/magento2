<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 for catalog_product (Admin)
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Product_Rest
{
    /**
     * The greatest decimal value which could be stored. Corresponds to DECIMAL (12,4) SQL type
     */
    const MAX_DECIMAL_VALUE = 99999999.9999;

    /**
     * Add special fields to product get response
     *
     * @param Mage_Catalog_Model_Product $product
     */
    protected function _prepareProductForResponse(Mage_Catalog_Model_Product $product)
    {
        $pricesFilterKeys = array('price_id', 'all_groups', 'website_price');
        $groupPrice = $product->getData('group_price');
        $product->setData('group_price', $this->_filterOutArrayKeys($groupPrice, $pricesFilterKeys, true));
        $tierPrice = $product->getData('tier_price');
        $product->setData('tier_price', $this->_filterOutArrayKeys($tierPrice, $pricesFilterKeys, true));

        $stockData = $product->getStockItem()->getData();
        $stockDataFilterKeys = array('item_id', 'product_id', 'stock_id', 'low_stock_date', 'type_id',
            'stock_status_changed_auto', 'stock_status_changed_automatically', 'product_name', 'store_id',
            'product_type_id', 'product_status_changed', 'product_changed_websites',
            'use_config_enable_qty_inc');
        $product->setData('stock_data', $this->_filterOutArrayKeys($stockData, $stockDataFilterKeys));
        $product->setData('product_type_name', $product->getTypeId());
        $this->_addConfigurableAttributes($product);
        $this->_unsetUnnecessaryDataFromConfigurable($product);
    }

    /**
     * Add information about the configurable attributes to the product of the configurable type
     *
     * @param Mage_Catalog_Model_Product $product
     */
    protected function _addConfigurableAttributes(Mage_Catalog_Model_Product $product)
    {
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
            $configurableType = $product->getTypeInstance();
            $configurableAttributes = $configurableType->getConfigurableAttributesAsArray($product);
            $formattedConfigurableAttributes = array();
            foreach ($configurableAttributes as $configurableAttribute) {
                // prepare array of the option prices
                $prices = array();
                foreach ($configurableAttribute['values'] as $priceItem) {
                    $prices[] = array(
                        'option_value' => $priceItem['value_index'],
                        'option_label' => $priceItem['label'],
                        'price' => $priceItem['pricing_value'],
                        'price_type' => $priceItem['is_percent'] ? 'percent' : 'fixed',
                    );
                }
                // format configurable attribute data
                $formattedConfigurableAttributes[] = array(
                    'attribute_code' => $configurableAttribute['attribute_code'],
                    'frontend_label' => $configurableAttribute['label'],
                    'frontend_label_use_default' => $configurableAttribute['use_default'],
                    'position' => $configurableAttribute['position'],
                    'prices' => $prices,
                );
            }
            $product->setConfigurableAttributes($formattedConfigurableAttributes);
        }
    }

    /**
     * Remove specified keys from associative or indexed array
     *
     * @param array $array
     * @param array $keys
     * @param bool $dropOrigKeys if true - return array as indexed array
     * @return array
     */
    protected function _filterOutArrayKeys(array $array, array $keys, $dropOrigKeys = false)
    {
        $isIndexedArray = is_array(reset($array));
        if ($isIndexedArray) {
            foreach ($array as &$value) {
                if (is_array($value)) {
                    $value = array_diff_key($value, array_flip($keys));
                }
            }
            if ($dropOrigKeys) {
                $array = array_values($array);
            }
            unset($value);
        } else {
            $array = array_diff_key($array, array_flip($keys));
        }

        return $array;
    }

    /**
     * Retrieve list of products
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        /** @var $productsCollection Mage_Catalog_Model_Resource_Product_Collection */
        $productsCollection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Collection');
        $store = $this->_getStore();
        $productsCollection->setStoreId($store->getId());
        $productsCollection->addAttributeToSelect(array_keys(
            $this->getAvailableAttributes($this->getUserType(), Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        ));
        $this->_applyCategoryFilter($productsCollection);
        $this->_applyCollectionModifiers($productsCollection);
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($productsCollection as $product) {
            $this->_unsetUnnecessaryDataFromConfigurable($product);
        }
        return $productsCollection->toArray();
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
        $this->_prevalidateRequiredFields($data);
        $type = $data['type_id'];
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')
            ->setStoreId(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            ->setAttributeSetId($data['attribute_set_id'])->setTypeId($type)->setSku($data['sku']);
        foreach ($product->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $product->setData($mediaAttrCode, 'no_selection');
        }
        $this->_prepareProductForSave($product, $data);

        $validator = Mage_Catalog_Model_Api2_Product_Validator_ProductAbstract::getValidatorByProductType($type);
        if (!$validator->isValidForCreate($product, $data)) {
            $this->_processValidationErrors($validator);
        }
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
     * Update product by its ID
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        // attribute set and product type cannot be updated
        unset($data['attribute_set_id']);
        unset($data['type_id']);
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->_getProduct();
        if (isset($data['sku'])) {
            $product->setSku($data['sku']);
        }
        $this->_prepareProductForSave($product, $data);
        $type = $product->getTypeId();
        $validator = Mage_Catalog_Model_Api2_Product_Validator_ProductAbstract::getValidatorByProductType($type);
        if (!$validator->isValidForUpdate($product, $data)) {
            $this->_processValidationErrors($validator);
        }
        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }
    }

    /**
     * Set additional data before product save
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $saveData
     */
    protected function _prepareProductForSave($product, &$saveData)
    {
        $this->_prepareStockData($product, $saveData);
        $this->_prepareGiftOptions($product, $saveData);
        $this->_prepareEavAttributes($product, $saveData);
        $this->_prepareConfigurableAttributes($product, $saveData);
        if (isset($saveData['website_ids']) && is_array($saveData['website_ids'])) {
            $product->setWebsiteIds($saveData['website_ids']);
        }
        // Create Permanent Redirect for old URL key
        if (!$product->isObjectNew() && isset($saveData['url_key']) && isset($saveData['url_key_create_redirect'])) {
            $product->setData('save_rewrites_history', (bool)$saveData['url_key_create_redirect']);
        }
    }

    /**
     * Prepare Inventory save data.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $saveData
     */
    protected function _prepareStockData($product, $saveData)
    {
        if (isset($saveData['stock_data'])) {
            if (!$product->isObjectNew() && !isset($saveData['stock_data']['manage_stock'])) {
                $saveData['stock_data']['manage_stock'] = $product->getStockItem()->getManageStock();
            }
            $this->_filterStockData($saveData['stock_data']);
        } else {
            $saveData['stock_data'] = array(
                'use_config_manage_stock' => 1,
                'use_config_min_sale_qty' => 1,
                'use_config_max_sale_qty' => 1
            );
        }
        $product->setStockData($saveData['stock_data']);
    }

    /**
     * Prepare Gift Options save data.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $saveData
     */
    protected function _prepareGiftOptions($product, $saveData)
    {
        $this->_filterConfigValueUsed($saveData, array(
            'gift_message_available' => 'use_config_gift_message_available',
            'gift_wrapping_available' => 'use_config_gift_wrapping_available'
        ));
        if (isset($saveData['use_config_gift_message_available'])) {
            $product->setData('use_config_gift_message_available', $saveData['use_config_gift_message_available']);
            if (!$saveData['use_config_gift_message_available'] && is_null($product->getGiftMessageAvailable())) {
                $product->setData('gift_message_available', (int) Mage::getStoreConfig(
                    Mage_GiftMessage_Helper_Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, $product->getStoreId()
                ));
            }
        }
        if (isset($saveData['use_config_gift_wrapping_available'])) {
            $product->setData('use_config_gift_wrapping_available', $saveData['use_config_gift_wrapping_available']);
            if (!$saveData['use_config_gift_wrapping_available'] && is_null($product->getGiftWrappingAvailable())) {
                $xmlPathGiftWrappingAvailable = 'sales/gift_options/wrapping_allow_items';
                $product->setData('gift_wrapping_available', (int) Mage::getStoreConfig(
                    $xmlPathGiftWrappingAvailable, $product->getStoreId()
                ));
            }
        }
    }

    /**
     * Prepare EAV attributes save data.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $saveData
     */
    protected function _prepareEavAttributes($product, $saveData)
    {
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        foreach ($product->getTypeInstance()->getEditableAttributes($product) as $attribute) {
            //Unset data if object attribute has no value in current store
            if (Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID !== (int)$product->getStoreId()
                && !$product->getExistsStoreValueFlag($attribute->getAttributeCode())
                && !$attribute->isScopeGlobal()
            ) {
                $product->setData($attribute->getAttributeCode(), false);
            }

            if ($this->_isAllowedAttribute($attribute) && isset($saveData[$attribute->getAttributeCode()])) {
                if (is_string($saveData[$attribute->getAttributeCode()])) {
                    $saveData[$attribute->getAttributeCode()] = trim($saveData[$attribute->getAttributeCode()]);
                }
                $product->setData(
                    $attribute->getAttributeCode(),
                    $saveData[$attribute->getAttributeCode()]
                );
            }
        }
    }

    /**
     * Process configurable attributes
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $saveData
     */
    protected function _prepareConfigurableAttributes(Mage_Catalog_Model_Product $product, array $saveData)
    {
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            if ($product->isObjectNew()) {
                $this->_prepareConfigurableAttributesForCreate($product, $saveData);
            } else {
                $this->_prepareConfigurableAttributesForUpdate($product, $saveData);
            }
        }
    }

    /**
     * Process configurable attributes for product create
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $saveData
     */
    protected function _prepareConfigurableAttributesForCreate(Mage_Catalog_Model_Product $product, array $saveData)
    {
        $usedConfigurableAttributeIds = array();
        $configurableAttributesData = array();
        if (isset($saveData['configurable_attributes']) && is_array($saveData['configurable_attributes'])) {
            foreach ($saveData['configurable_attributes'] as $configurableData) {
                if (!isset($configurableData['attribute_code'])) {
                    continue;
                }
                /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
                $attribute = Mage::getResourceModel('Mage_Catalog_Model_Resource_Eav_Attribute');
                $attribute->load($configurableData['attribute_code'], 'attribute_code');
                if ($attribute->getId()) {
                    $usedConfigurableAttributeIds[] = $attribute->getId();
                    $configurableAttributesData[$attribute->getAttributeCode()] = array(
                        'attribute_id' => $attribute->getId(),
                        'attribute_code' => $attribute->getAttributeCode(),
                        'label' => (isset($configurableData['frontend_label']) && $configurableData['frontend_label'])
                            ? trim((string) $configurableData['frontend_label']) : null,
                        'use_default' => (isset($configurableData['frontend_label_use_default'])
                            && $configurableData['frontend_label_use_default']) ? 1 : 0,
                        'position' => (isset($configurableData['position']) && $configurableData['position'])
                            ? (int) $configurableData['position'] : 0,
                    );

                    // save information about configurable options' prices
                    if (isset($configurableData['prices']) && is_array($configurableData['prices'])) {
                        $formattedOptions = array();
                        foreach ($configurableData['prices'] as $optionPrice) {
                            if (isset($optionPrice['option_value']) && isset($optionPrice['price'])
                                && isset($optionPrice['price_type'])
                            ) {
                                $formattedOptions[] = array(
                                    'value_index' => $optionPrice['option_value'],
                                    'pricing_value' => $optionPrice['price'],
                                    'is_percent' => ($optionPrice['price_type'] == 'percent')
                                );
                            }
                        }
                        $configurableAttributesData[$attribute->getAttributeCode()]['values'] = $formattedOptions;
                    }
                }
            }
        }
        $product->setConfigurableAttributesData($configurableAttributesData);
        /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
        $configurableType = $product->getTypeInstance();
        $configurableType->setUsedProductAttributeIds($usedConfigurableAttributeIds, $product);
    }

    /**
     * Process configurable attributes for product update
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $saveData
     */
    protected function _prepareConfigurableAttributesForUpdate(Mage_Catalog_Model_Product $product, array $saveData)
    {
        if (isset($saveData['configurable_attributes']) && is_array($saveData['configurable_attributes'])) {
            $requestData = array();
            foreach ($saveData['configurable_attributes'] as $data) {
                if (isset($data['attribute_code'])) {
                    $requestData[$data['attribute_code']] = array();
                    if (isset($data['frontend_label'])) {
                        $requestData[$data['attribute_code']]['label'] = trim((string) $data['frontend_label']);
                    }
                    if (isset($data['frontend_label_use_default'])) {
                        $requestData[$data['attribute_code']]['use_default'] = $data['frontend_label_use_default']
                            ? 1 : 0;
                    }
                    if (isset($data['position'])) {
                        $requestData[$data['attribute_code']]['position'] = (int) $data['position'];
                    }
                    if (isset($data['prices']) && is_array($data['prices'])) {
                        $values = array();
                        foreach ($data['prices'] as $price) {
                            if (isset($price['option_value'])) {
                                $option = array('value_index' => $price['option_value'], 'use_default_value' => false);
                                if (isset($price['price'])) {
                                    $option['pricing_value'] = $price['price'];
                                }
                                if (isset($price['price_type'])) {
                                    $option['is_percent'] = ($price['price_type'] == 'percent');
                                }
                                if (isset($price['use_default_value'])) {
                                    $option['use_default_value'] = $price['use_default_value'] == 1;
                                }
                                $values[] = $option;
                            }
                        }
                        $requestData[$data['attribute_code']]['values'] = $values;
                    }
                }
            }

            /** @var $typeInstance Mage_Catalog_Model_Product_Type_Configurable */
            $typeInstance = $product->getTypeInstance();
            $configurableAttributesData = $typeInstance->getConfigurableAttributesAsArray($product);
            foreach ($configurableAttributesData as &$attribute) {
                if (isset($requestData[$attribute['attribute_code']])) {
                    $requestDataAttribute = $requestData[$attribute['attribute_code']];
                    $values = $attribute['values'];
                    if (isset($requestDataAttribute['values'])) {
                        foreach ($requestDataAttribute['values'] as $requestValue) {
                            $isValueSaved = false;
                            foreach ($values as &$savedValue) {
                                if ($savedValue['value_index'] == $requestValue['value_index']) {
                                    $isValueSaved = true;
                                    $savedValue = array_merge($savedValue, $requestValue);
                                    break;
                                }
                            }
                            // delete $savedValue link to prevent accidental $values array modification through it
                            unset($savedValue);
                            if (!$isValueSaved) {
                                $values[] = $requestValue;
                            }
                        }
                    }

                    $attribute = array_merge($attribute, $requestData[$attribute['attribute_code']]);
                    $attribute['values'] = $values;
                }
            }
            // delete $attribute link to prevent accidental $configurableAttributesData array modification through it
            unset($attribute);
            $product->setConfigurableAttributesData($configurableAttributesData);
            $product->setCanSaveConfigurableAttributes(true);
        }
    }

    /**
     * Make sure that required fields are set and not empty
     *
     * @param array $data
     */
    protected function _prevalidateRequiredFields($data)
    {
        if (!isset($data['type_id']) || empty($data['type_id'])) {
            $this->_critical('The "type_id" value is missing in the request.',
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if (!isset($data['attribute_set_id']) || empty($data['attribute_set_id'])) {
            $this->_critical('The "attribute_set_id" value is missing in the request.',
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if (!isset($data['sku']) || empty($data['sku'])) {
            $this->_critical('The "sku" value is missing in the request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Filter stock data values
     *
     * @param array $stockData
     */
    protected function _filterStockData(&$stockData)
    {
        $fieldsWithPossibleDefaultValuesInConfig = array(
            'manage_stock' => 'use_config_manage_stock',
            'min_sale_qty' => 'use_config_min_sale_qty',
            'max_sale_qty' => 'use_config_max_sale_qty',
            'backorders' => 'use_config_backorders',
            'qty_increments' => 'use_config_qty_increments',
            'notify_stock_qty' => 'use_config_notify_stock_qty',
            'min_qty' => 'use_config_min_qty',
            'enable_qty_increments' => 'use_config_enable_qty_inc');
        $this->_filterConfigValueUsed($stockData, $fieldsWithPossibleDefaultValuesInConfig);

        if ($this->_isManageStockEnabled($stockData)) {
            if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_DECIMAL_VALUE) {
                $stockData['qty'] = self::MAX_DECIMAL_VALUE;
            }
            if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
                $stockData['min_qty'] = 0;
            }
            if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
                $stockData['is_decimal_divided'] = 0;
            }
        } else {
            $nonManageStockFields = array('manage_stock', 'use_config_manage_stock', 'min_sale_qty',
                'use_config_min_sale_qty', 'max_sale_qty', 'use_config_max_sale_qty');
            foreach ($stockData as $field => $value) {
                if (!in_array($field, $nonManageStockFields)) {
                    unset($stockData[$field]);
                }
            }
        }
    }

    /**
     * Filter out fields if Use Config Settings option used
     *
     * @param array $data
     * @param string $fields
     */
    protected function _filterConfigValueUsed(&$data, $fields)
    {
        foreach ($fields as $field => $useConfigField) {
            if (isset($data[$useConfigField]) && $data[$useConfigField]) {
                unset($data[$field]);
            }
        }
    }

    /**
     * Check if attribute is allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute, $attributes = null)
    {
        $isAllowed = true;
        if (is_array($attributes)
            && !in_array($attribute->getAttributeCode(), $attributes)
            && !in_array($attribute->getAttributeId(), $attributes)
        ) {
            $isAllowed = false;
        }
        return $isAllowed;
    }

    /**
     * Determine if stock management is enabled
     *
     * @param array $stockData
     * @return bool
     */
    protected function _isManageStockEnabled($stockData)
    {
        if (!(isset($stockData['use_config_manage_stock']) && $stockData['use_config_manage_stock'])) {
            $manageStock = isset($stockData['manage_stock']) && $stockData['manage_stock'];
        } else {
            $manageStock = Mage::getStoreConfig(
                Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . 'manage_stock');
        }
        return (bool)$manageStock;
    }
}
