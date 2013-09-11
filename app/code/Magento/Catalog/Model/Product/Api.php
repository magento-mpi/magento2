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
 * Catalog product api
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product;

class Api extends \Magento\Catalog\Model\Api\Resource
{
    protected $_filtersMap = array(
        'product_id' => 'entity_id',
        'set'        => 'attribute_set_id',
        'type'       => 'type_id'
    );

    protected $_defaultProductAttributeList = array(
        'type_id',
        'category_ids',
        'website_ids',
        'name',
        'description',
        'short_description',
        'sku',
        'weight',
        'status',
        'url_key',
        'url_path',
        'visibility',
        'has_options',
        'gift_message_available',
        'price',
        'special_price',
        'special_from_date',
        'special_to_date',
        'tax_class_id',
        'tier_price',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'custom_design',
        'custom_layout_update',
        'options_container',
        'image_label',
        'small_image_label',
        'thumbnail_label',
        'created_at',
        'updated_at'
    );

    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
        $this->_ignoredAttributeTypes[] = 'gallery';
        $this->_ignoredAttributeTypes[] = 'media_image';
    }

    /**
     * Retrieve list of products with basic info (id, sku, type, set, name)
     *
     * @param null|object|array $filters
     * @param string|int $store
     * @return array
     */
    public function items($filters = null, $store = null)
    {
        $collection = \Mage::getModel('Magento\Catalog\Model\Product')->getCollection()
            ->addStoreFilter($this->_getStoreId($store))
            ->addAttributeToSelect('name');

        /** @var $apiHelper \Magento\Api\Helper\Data */
        $apiHelper = \Mage::helper('Magento\Api\Helper\Data');
        $filters = $apiHelper->parseFilters($filters, $this->_filtersMap);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach ($collection as $product) {
            $result[] = array(
                'product_id' => $product->getId(),
                'sku'        => $product->getSku(),
                'name'       => $product->getName(),
                'set'        => $product->getAttributeSetId(),
                'type'       => $product->getTypeId(),
                'category_ids' => $product->getCategoryIds(),
                'website_ids'  => $product->getWebsiteIds()
            );
        }
        return $result;
    }

    /**
     * Retrieve product info
     *
     * @param int|string $productId
     * @param string|int $store
     * @param array      $attributes
     * @param string     $identifierType
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

        foreach ($product->getTypeInstance()->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $attributes)) {
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

        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        $product->setStoreId($this->_getStoreId($store))
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        if (!isset($productData['stock_data']) || !is_array($productData['stock_data'])) {
            //Set default stock_data if not exist in product data
            $product->setStockData(array('use_config_manage_stock' => 0));
        }

        foreach ($product->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $product->setData($mediaAttrCode, 'no_selection');
        }

        $this->_prepareDataForSave($product, $productData);

        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             * @todo see \Magento\Catalog\Model\Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                $strErrors = array();
                foreach($errors as $code => $error) {
                    if ($error === true) {
                        $error = __('Please correct attribute "%1".', $code);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            $product->save();
        } catch (\Magento\Core\Exception $e) {
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
             * @todo see \Magento\Catalog\Model\Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                $strErrors = array();
                foreach($errors as $code => $error) {
                    if ($error === true) {
                        $error = __('Please correct the value for "%1".', $code);
                    } else {
                        $error = __('Please correct the value for "%1": %2.', $code, $error);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            $product->save();
        } catch (\Magento\Core\Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     *  Set additional data before product saved
     *
     *  @param    \Magento\Catalog\Model\Product $product
     *  @param    array $productData
     *  @return   object
     */
    protected function _prepareDataForSave($product, $productData)
    {
        if (isset($productData['website_ids']) && is_array($productData['website_ids'])) {
            $product->setWebsiteIds($productData['website_ids']);
        }

        foreach ($product->getTypeInstance()->getEditableAttributes($product) as $attribute) {
            //Unset data if object attribute has no value in current store
            if (\Magento\Catalog\Model\AbstractModel::DEFAULT_STORE_ID !== (int) $product->getStoreId()
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
                        $website = \Mage::app()->getWebsite($website)->getId();
                    } catch (\Exception $e) { }
                }
            }
            $product->setWebsiteIds($productData['websites']);
        }

        if (\Mage::app()->hasSingleStore()) {
            $product->setWebsiteIds(array(\Mage::app()->getStore(true)->getWebsite()->getId()));
        }

        if (isset($productData['stock_data']) && is_array($productData['stock_data'])) {
            $product->setStockData($productData['stock_data']);
        }

        if (isset($productData['tier_price']) && is_array($productData['tier_price'])) {
             $tierPrices = \Mage::getModel('Magento\Catalog\Model\Product\Attribute\Tierprice\Api')
                 ->prepareTierPrices($product, $productData['tier_price']);
             $product->setData(\Magento\Catalog\Model\Product\Attribute\Tierprice\Api::ATTRIBUTE_CODE, $tierPrices);
        }
        $this->_prepareConfigurableAttributes($product, $productData);
    }

    /**
     * Process configurable attributes
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $saveData
     */
    protected function _prepareConfigurableAttributes(\Magento\Catalog\Model\Product $product, array $saveData)
    {
        if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE) {
            if ($product->isObjectNew()) {
                $this->_prepareConfigurableAttributesForCreate($product, $saveData);
            } else {
                // TODO: Implement part related to product update
            }
        }
    }

    /**
     * Process configurable attributes for product create
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $saveData
     */
    protected function _prepareConfigurableAttributesForCreate(\Magento\Catalog\Model\Product $product, array $saveData)
    {
        $usedConfigurableAttributeIds = array();
        $configurableAttributesData = array();
        if (isset($saveData['configurable_attributes']) && is_array($saveData['configurable_attributes'])) {
            foreach ($saveData['configurable_attributes'] as $configurableData) {
                if (!isset($configurableData['attribute_code'])) {
                    continue;
                }
                /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
                $attribute = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Eav\Attribute');
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
        /** @var $configurableType \Magento\Catalog\Model\Product\Type\Configurable */
        $configurableType = $product->getTypeInstance();
        $configurableType->setUsedProductAttributeIds($usedConfigurableAttributeIds, $product);
    }

    /**
     * Update product special price
     *
     * @param int|string $productId
     * @param float $specialPrice
     * @param string $fromDate
     * @param string $toDate
     * @param string|int $store
     * @return boolean
     */
    public function setSpecialPrice($productId, $specialPrice = null, $fromDate = null, $toDate = null, $store = null)
    {
        return $this->update($productId, array(
            'special_price'     => $specialPrice,
            'special_from_date' => $fromDate,
            'special_to_date'   => $toDate
        ), $store);
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
        return $this->info($productId, $store, array('special_price', 'special_from_date', 'special_to_date'));
    }

    /**
     * Delete product
     *
     * @param int|string $productId
     * @return boolean
     */
    public function delete($productId, $identifierType = null)
    {
        $product = $this->_getProduct($productId, null, $identifierType);

        try {
            $product->delete();
        } catch (\Magento\Core\Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }

   /**
    * Get list of additional attributes which are not in default create/update list
    *
    * @param  $productType
    * @param  $attributeSetId
    * @return array
    */
    public function getAdditionalAttributes($productType, $attributeSetId)
    {
        $this->_checkProductTypeExists($productType);
        $this->_checkProductAttributeSet($attributeSetId);

        /** @var $product \Magento\Catalog\Model\Product */
        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        $productAttributes = $product->setAttributeSetId($attributeSetId)
            ->setTypeId($productType)
            ->getTypeInstance()
            ->getEditableAttributes($product);

        $result = array();
        foreach ($productAttributes as $attribute) {
            /* @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
            if ($attribute->isInSet($attributeSetId) && $this->_isAllowedAttribute($attribute)
                && !in_array($attribute->getAttributeCode(), $this->_defaultProductAttributeList)) {

                if ($attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = array(
                    'attribute_id' => $attribute->getId(),
                    'code' => $attribute->getAttributeCode(),
                    'type' => $attribute->getFrontendInput(),
                    'required' => $attribute->getIsRequired(),
                    'scope' => $scope
                );
            }
        }

        return $result;
    }

    /**
     * Check if product type exists
     *
     * @param  $productType
     * @throw \Magento\Api\Exception
     * @return void
     */
    protected function _checkProductTypeExists($productType)
    {
        if (!in_array($productType, array_keys(\Mage::getModel('Magento\Catalog\Model\Product\Type')->getOptionArray()))) {
            $this->_fault('product_type_not_exists');
        }
    }

    /**
     * Check if attributeSet is exits and in catalog_product entity group type
     *
     * @param  $attributeSetId
     * @throw \Magento\Api\Exception
     * @return void
     */
    protected function _checkProductAttributeSet($attributeSetId)
    {
        $attributeSet = \Mage::getModel('Magento\Eav\Model\Entity\Attribute\Set')->load($attributeSetId);
        if (is_null($attributeSet->getId())) {
            $this->_fault('product_attribute_set_not_exists');
        }
        if (\Mage::getModel('Magento\Catalog\Model\Product')->getResource()->getTypeId() != $attributeSet->getEntityTypeId()) {
            $this->_fault('product_attribute_set_not_valid');
        }
    }
} // Class \Magento\Catalog\Model\Product\Api End
