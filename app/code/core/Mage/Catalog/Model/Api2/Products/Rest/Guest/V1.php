<?php

class Mage_Catalog_Model_Api2_Products_Rest_Guest_V1 extends Mage_Catalog_Model_Api2_Products_Rest
{
    /**
     * Create product
     *
     * @see Mage_Catalog_Model_Product_Api::create()
     * @param array $data
     * @return array
     */
    protected function _create(array $data)
    {
        $required = array('type', 'set', 'sku');
        $valueable = array('type', 'set', 'sku');
        $this->_validate($data, $required, $valueable);

        $productData = isset($data['productData']) ? $data['productData'] : array();
        $type = $data['type'];
        $set = $data['set'];
        $sku = $data['sku'];
        $store = isset($data['store']) ? $data['store'] : '';
        $productData = array_diff_key($data, array_flip(array('type', 'set', 'sku')));

        /** @var $typeModel Mage_Catalog_Model_Product_Type */
        $typeModel = Mage::getModel('catalog/product_type');
        if (!in_array($type, array_keys($typeModel->getOptionArray()))) {
            $this->_critical(self::RESOURCE_DATA_INVALID);
        }

        /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
        $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($set);
        if (is_null($attributeSet->getId())) {
            $this->_critical(self::RESOURCE_DATA_INVALID);  //product_attribute_set_not_exists
        }

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');
        if ($product->getResource()->getTypeId() != $attributeSet->getEntityTypeId()) {
            $this->_critical(self::RESOURCE_DATA_INVALID);  //product_attribute_set_not_valid
        }

        try {
            $storeId = Mage::app()->getStore($store)->getId();
        } catch (Mage_Core_Model_Store_Exception $e) {
            $this->_critical(self::RESOURCE_DATA_INVALID);    //store_not_exists
        }

        $product->setStoreId($storeId)
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        if (!isset($productData['stock_data']) || !is_array($productData['stock_data'])) {
            //Set default stock_data if not exist in product data
            $product->setStockData(array('use_config_manage_stock' => 0));
        }

        $this->_prepareDataForSave($product, $productData);

        try {
            /**
             * @TODO implement full validation process with errors returning which are ignoring now
             * @TODO see Mage_Catalog_Model_Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                foreach($errors as $code => $error) {
                    if ($error === true) {
                        $this->_error(
                            sprintf('Attribute "%s" is invalid.', $code),
                            Mage_Api2_Model_Server::HTTP_BAD_REQUEST
                        );   //data_invalid
                    }
                }
                $this->_critical(self::RESOURCE_DATA_INVALID);    //data_invalid
            }

            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);    //data_invalid
        }

        return $this->_getLocation($product);
    }

    /**
     * Get product
     *
     * @return array
     */
    protected function _retrieve()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');

        $this->_applyCollectionModifiers($collection);
        $this->_applyAttributeFilters($collection);

        $collection->load();

        return $collection->toArray();
    }

    /**
     *  Set additional data before product saved
     *
     *  @param    Mage_Catalog_Model_Product $product
     *  @param    array $productData
     *  @return   object
     */
    protected function _prepareDataForSave($product, $productData)
    {
        if (isset($productData['website_ids']) && is_array($productData['website_ids'])) {
            $product->setWebsiteIds($productData['website_ids']);
        }

        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            //Unset data if object attribute has no value in current store
            if (Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID !== $product->getStoreId()
                && !$product->getExistsStoreValueFlag($attribute->getAttributeCode())
                && !$attribute->isScopeGlobal()
            ) {
                $product->setData($attribute->getAttributeCode(), false);
            }

            if ($this->_isAllowedAttribute($attribute)) {
                if (isset($productData[$attribute->getAttributeCode()])) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData[$attribute->getAttributeCode()]
                    );
                } elseif (isset($productData['additional_attributes']['single_data'][$attribute->getAttributeCode()])) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData['additional_attributes']['single_data'][$attribute->getAttributeCode()]
                    );
                } elseif (isset($productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()])) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()]
                    );
                }
            }
        }

        if (isset($productData['categories']) && is_array($productData['categories'])) {
            $product->setCategoryIds($productData['categories']);
        }

        if (isset($productData['websites']) && is_array($productData['websites'])) {
            foreach ($productData['websites'] as &$website) {
                if (is_string($website)) {
                    try {
                        $website = Mage::app()->getWebsite($website)->getId();
                    } catch (Exception $e) { }
                }
            }
            $product->setWebsiteIds($productData['websites']);
        }

        if (Mage::app()->isSingleStoreMode()) {
            $product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
        }

        if (isset($productData['stock_data']) && is_array($productData['stock_data'])) {
            $product->setStockData($productData['stock_data']);
        }

        if (isset($productData['tier_price']) && is_array($productData['tier_price'])) {
             $tierPrices = Mage::getModel('catalog/product_attribute_tierprice_api')
                 ->prepareTierPrices($product, $productData['tier_price']);
             $product->setData(Mage_Catalog_Model_Product_Attribute_Tierprice_Api::ATTRIBUTE_CODE, $tierPrices);
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
        if (is_array($attributes)
            && !( in_array($attribute->getAttributeCode(), $attributes)
                  || in_array($attribute->getAttributeId(), $attributes))) {
            return false;
        }

        $ignoredAttributeTypes = array();
        $ignoredAttributeCodes = array('entity_id', 'attribute_set_id', 'entity_type_id');

        return !in_array($attribute->getFrontendInput(), $ignoredAttributeTypes)
               && !in_array($attribute->getAttributeCode(), $ignoredAttributeCodes);
    }

    /**
     * Get location for given resource
     *
     * @param Mage_Catalog_Model_Abstract $product
     * @return string Location of new resource
     */
    protected function _getLocation(Mage_Catalog_Model_Abstract $product)
    {
        /** @var $config Mage_Api2_Model_Config */
        $config = Mage::getModel('api2/config');

        /** @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('api2/route_apiType');

        $chain = $apiTypeRoute->chain(
            new Zend_Controller_Router_Route($config->getMainRoute('product'))
        );
        $params = array(
            'api_type' => $this->getRequest()->getApiType(),
            'id'       => $product->getId()
        );
        $uri = $chain->assemble($params);

        return '/'.$uri;
    }
}
