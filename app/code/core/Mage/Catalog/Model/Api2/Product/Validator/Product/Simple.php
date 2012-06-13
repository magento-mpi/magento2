<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Catalog product resource validator for simple product
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Validator_Product_Simple
    extends Mage_Catalog_Model_Api2_Product_Validator_ProductAbstract
{
    /**
     * Current API operation
     *
     * @var string
     */
    protected $_operation;

    /**
     * Current product
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Retreive the product that is currently being validated
     *
     * @return Mage_Catalog_Model_Product
     * @throws Exception
     */
    protected function _getProduct()
    {
        if (!($this->_product && $this->_product instanceof Mage_Catalog_Model_Product)) {
            throw new Exception("Product is not specified.");
        }
        return $this->_product;
    }

    /**
     * Check if product data is valid for update
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     * @return bool
     */
    public function isValidForUpdate(Mage_Catalog_Model_Product $product, array $data)
    {
        $this->_operation = Mage_Api2_Model_Resource::OPERATION_UPDATE;
        $this->_product = $product;
        if ($product->getId()) {
            $data['attribute_set_id'] = $product->getAttributeSetId();
            $data['type_id'] = $product->getTypeId();
        }
        return $this->_isValidForSave($data);
    }

    /**
     * Check if product data is valid for create
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     * @return bool
     */
    public function isValidForCreate(Mage_Catalog_Model_Product $product, array $data)
    {
        $this->_operation = Mage_Api2_Model_Resource::OPERATION_CREATE;
        $this->_product = $product;
        return $this->_isValidForSave($data);
    }

    /**
     * Is update mode
     *
     * @return bool
     */
    protected function _isUpdateOperation()
    {
        return $this->_operation == Mage_Api2_Model_Resource::OPERATION_UPDATE;
    }

    /**
     * Is create mode
     *
     * @return bool
     */
    protected function _isCreateOperation()
    {
        return $this->_operation == Mage_Api2_Model_Resource::OPERATION_CREATE;
    }

    /**
     * Check if product data is valid for save
     *
     * @param array $data
     * @return bool
     */
    protected function _isValidForSave(array $data)
    {
        try {
            $this->_validateProductType($data);
            /** @var $productEntity Mage_Eav_Model_Entity_Type */
            $productEntity = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode(Mage_Catalog_Model_Product::ENTITY);
            $this->_validateAttributeSet($data, $productEntity);
            $this->_validateSku($data);
            $this->_validateGiftOptions($data);
            $this->_validateGroupPrice($data);
            $this->_validateTierPrice($data);
            $this->_validateStockData($data);
            $requiredAttributes = $this->_validateAttributes($data, $productEntity);
            $this->_validateRequiredAttributes($data, $requiredAttributes);
            if ($this->isValid()) {
                // perform native validation only if custom validation succeed to prevent duplicate error messages
                $this->_getProduct()->validate();
            }
            $this->_validateProductTypeSpecificData($data);
        } catch (Mage_Api2_Model_Resource_Validator_Exception $e) {
            $this->_addError($e->getMessage());
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_addError(sprintf('Invalid attribute "%s": %s', $e->getAttributeCode(), $e->getMessage()));
        }

        return $this->isValid();
    }

    /**
     * Validate data specific for current product type
     *
     * @param array $data
     * @return bool
     */
    protected function _validateProductTypeSpecificData(array $data)
    {
        return $this->isValid();
    }

    /**
     * Collect required EAV attributes, validate applicable attributes and validate source attributes values
     *
     * @param array $data
     * @param Mage_Eav_Model_Entity_Type $productEntity
     * @return array
     */
    protected function _validateAttributes($data, $productEntity)
    {
        if (!isset($data['attribute_set_id']) || empty($data['attribute_set_id'])) {
            $this->_critical('Missing "attribute_set_id" in request.');
        }
        if (!isset($data['type_id']) || empty($data['type_id'])) {
            $this->_critical('Missing "type_id" in request.');
        }
        // Validate weight
        if (isset($data['weight']) && !empty($data['weight']) && $data['weight'] > 0
            && !Zend_Validate::is($data['weight'], 'Between', array(0, self::MAX_DECIMAL_VALUE))
        ) {
            $this->_addError('The "weight" value is not within the specified range.');
        }
        // msrp_display_actual_price_type attribute values needs to be a string to pass validation
        // see Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price::getAllOptions()
        if (isset($data['msrp_display_actual_price_type'])) {
            $data['msrp_display_actual_price_type'] = (string)$data['msrp_display_actual_price_type'];
        }
        $requiredAttributes = array('attribute_set_id');
        $positiveNumberAttributes = array('weight', 'price', 'special_price', 'msrp');
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        foreach ($productEntity->getAttributeCollection($data['attribute_set_id']) as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $value = false;
            $isSet = false;
            if (isset($data[$attribute->getAttributeCode()])) {
                $value = $data[$attribute->getAttributeCode()];
                $isSet = true;
            }
            $applicable = false;
            if (!$attribute->getApplyTo() || in_array($data['type_id'], $attribute->getApplyTo())) {
                $applicable = true;
            }

            if (!$applicable && !$attribute->isStatic() && $isSet) {
                $productTypes = Mage_Catalog_Model_Product_Type::getTypes();
                $this->_addError(sprintf('Attribute "%s" is not applicable for product type "%s"', $attributeCode,
                    $productTypes[$data['type_id']]['label']));
            }

            if ($applicable && $isSet) {
                // Validate dropdown attributes
                if ($attribute->usesSource()
                    // skip check when field will be validated later as a required one
                    && !(empty($value) && $attribute->getIsRequired())
                ) {
                    $allowedValues = $this->_getAttributeAllowedValues($attribute->getSource()->getAllOptions());
                    if (!is_array($value)) {
                        // make validation of select and multiselect identical
                        $value = array($value);
                    }
                    foreach ($value as $selectValue) {
                        $useStrictMode = !is_numeric($selectValue);
                        if (!in_array($selectValue, $allowedValues, $useStrictMode)
                            && !$this->_isConfigValueUsed($data, $attributeCode)
                        ) {
                            $this->_addError(sprintf('Invalid value "%s" for attribute "%s".',
                                $selectValue, $attributeCode));
                        }
                    }
                }
                // Validate datetime attributes
                if ($attribute->getBackendType() == 'datetime') {
                    try {
                        $attribute->getBackend()->formatDate($value);
                    } catch (Zend_Date_Exception $e) {
                        $this->_addError(sprintf('Invalid date in the "%s" field.', $attributeCode));
                    }
                }
                // Validate positive number required attributes
                if (in_array($attributeCode, $positiveNumberAttributes) && (!empty($value) && $value !== 0)
                    && (!is_numeric($value) || $value < 0)
                ) {
                    $this->_addError(sprintf('Please enter a number 0 or greater in the "%s" field.', $attributeCode));
                }
            }

            if ($applicable && $attribute->getIsRequired() && $attribute->getIsVisible()) {
                if (!in_array($attributeCode, $positiveNumberAttributes) || $value !== 0) {
                    $requiredAttributes[] = $attribute->getAttributeCode();
                }
            }
        }

        return $requiredAttributes;
    }

    /**
     * Validate that required attributes are present in data.
     *
     * @param array $data
     * @param array $requiredAttributes
     */
    protected function _validateRequiredAttributes($data, $requiredAttributes)
    {
        foreach ($requiredAttributes as $key) {
            if (!array_key_exists($key, $data)) {
                if (!$this->_isUpdateOperation()) {
                    $this->_addError(sprintf('Missing "%s" in request.', $key));
                    continue;
                }
            } else {
                if (is_string($data[$key])) {
                    $data[$key] = trim($data[$key]);
                }
                if (!is_numeric($data[$key]) && empty($data[$key])) {
                    $this->_addError(sprintf('Empty value for "%s" in request.', $key));
                }
            }
        }
    }

    /**
     * Validate product type
     *
     * @param array $data
     * @return bool
     */
    protected function _validateProductType($data)
    {
        if ($this->_isUpdateOperation()) {
            return true;
        }
        if (!array_key_exists($data['type_id'], Mage_Catalog_Model_Product_Type::getTypes())) {
            $this->_critical('Invalid product type.');
        }
    }

    /**
     * Validate attribute set
     *
     * @param array $data
     * @param Mage_Eav_Model_Entity_Type $productEntity
     * @return bool
     */
    protected function _validateAttributeSet(array $data, $productEntity)
    {
        if ($this->_isUpdateOperation()) {
            return true;
        }
        /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
        $attributeSet = Mage::getModel('Mage_Eav_Model_Entity_Attribute_Set')->load($data['attribute_set_id']);
        if (!$attributeSet->getId() || $productEntity->getEntityTypeId() != $attributeSet->getEntityTypeId()) {
            $this->_critical('Invalid attribute set.');
        }
    }

    /**
     * Validate SKU
     *
     * @param array $data
     * @return bool
     */
    protected function _validateSku($data)
    {
        if ($this->_isUpdateOperation() && !isset($data['sku'])) {
            return true;
        }
        if (!Zend_Validate::is((string)$data['sku'], 'StringLength', array('min' => 0, 'max' => 64))) {
            $this->_addError('SKU length should be 64 characters maximum.');
        }
    }

    /**
     * Validate product gift options data
     *
     * @param array $data
     */
    protected function _validateGiftOptions($data)
    {
        if (isset($data['gift_wrapping_price'])) {
            if (!(is_numeric($data['gift_wrapping_price']) && $data['gift_wrapping_price'] >= 0)) {
                $this->_addError('Please enter a number 0 or greater in the "gift_wrapping_price" field.');
            }
        }
    }

    /**
     * Validate Group Price complex attribute
     *
     * @param array $data
     */
    protected function _validateGroupPrice($data)
    {
        if (isset($data['group_price']) && is_array($data['group_price'])) {
            $groupPrices = $data['group_price'];
            foreach ($groupPrices as $index => $groupPrice) {
                $fieldSet = 'group_price:' . $index;
                $this->_validateWebsiteIdForGroupPrice($groupPrice, $fieldSet);
                $this->_validateCustomerGroup($groupPrice, $fieldSet);
                $this->_validatePositiveNumber($groupPrice, $fieldSet, 'price', true, true);
            }
        }
    }

    /**
     * Validate Tier Price complex attribute
     *
     * @param array $data
     */
    protected function _validateTierPrice($data)
    {
        if (isset($data['tier_price']) && is_array($data['tier_price'])) {
            $tierPrices = $data['tier_price'];
            foreach ($tierPrices as $index => $tierPrice) {
                $fieldSet = 'tier_price:' . $index;
                $this->_validateWebsiteIdForGroupPrice($tierPrice, $fieldSet);
                $this->_validateCustomerGroup($tierPrice, $fieldSet);
                $this->_validatePositiveNumber($tierPrice, $fieldSet, 'price_qty');
                $this->_validatePositiveNumber($tierPrice, $fieldSet, 'price');
            }
        }
    }

    /**
     * Check if website id is appropriate according to price scope settings
     *
     * @param array $data
     * @param string $fieldSet
     */
    protected function _validateWebsiteIdForGroupPrice($data, $fieldSet)
    {
        if (!isset($data['website_id'])) {
            $this->_addError(sprintf('The "website_id" value in the "%s" set is a required field.', $fieldSet));
        } else {
            /** @var $catalogHelper Mage_Catalog_Helper_Data */
            $catalogHelper = Mage::helper('Mage_Catalog_Helper_Data');
            $website = Mage::getModel('Mage_Core_Model_Website')->load($data['website_id']);
            $isAllWebsitesValue = is_numeric($data['website_id']) && ($data['website_id'] == 0);
            $isGlobalPriceScope = (int)$catalogHelper->getPriceScope() == Mage_Catalog_Helper_Data::PRICE_SCOPE_GLOBAL;
            if (is_null($website->getId()) || ($isGlobalPriceScope && !$isAllWebsitesValue)) {
                $this->_addError(sprintf('Invalid "website_id" value in the "%s" set.', $fieldSet));
            }
        }
    }

    /**
     * Validate product inventory data
     *
     * @param array $data
     */
    protected function _validateStockData($data)
    {
        if (isset($data['stock_data']) && is_array($data['stock_data'])) {
            $stockData = $data['stock_data'];
            $fieldSet = 'stock_data';
            if (!(isset($stockData['use_config_manage_stock']) && $stockData['use_config_manage_stock'])) {
                $this->_validateBoolean($stockData, $fieldSet, 'manage_stock');
            }
            if ($this->_isManageStockEnabled($stockData)) {
                $this->_validateNumeric($stockData, $fieldSet, 'qty');
                $this->_validatePositiveNumber($stockData, $fieldSet, 'min_qty', false, true, true);
                $this->_validateNumeric($stockData, $fieldSet, 'notify_stock_qty', false, true);
                $this->_validateBoolean($stockData, $fieldSet, 'is_qty_decimal');
                if (isset($stockData['is_qty_decimal']) && (bool)$stockData['is_qty_decimal'] == true) {
                    $this->_validateBoolean($stockData, $fieldSet, 'is_decimal_divided');
                }
                if (!isset($stockData['use_config_enable_qty_inc']) || !$stockData['use_config_enable_qty_inc']) {
                    $this->_validateBoolean($stockData, $fieldSet, 'enable_qty_increments', true);
                }
                if (isset($stockData['enable_qty_increments']) && (bool)$stockData['enable_qty_increments'] == true) {
                    $this->_validatePositiveNumeric($stockData, $fieldSet, 'qty_increments', false, true);
                }
                if (Mage::helper('Mage_Catalog_Helper_Data')->isModuleEnabled('Mage_CatalogInventory')) {
                    $this->_validateSource($stockData, $fieldSet, 'backorders',
                        'Mage_CatalogInventory_Model_Source_Backorders', true);
                    $this->_validateSource($stockData, $fieldSet, 'is_in_stock',
                        'Mage_CatalogInventory_Model_Source_Stock');
                }
            }

            $this->_validatePositiveNumeric($stockData, $fieldSet, 'min_sale_qty', false, true);
            $this->_validatePositiveNumeric($stockData, $fieldSet, 'max_sale_qty', false, true);
        }
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

    /**
     * Validate Customer Group field
     *
     * @param string $fieldSet
     * @param array $data
     */
    protected function _validateCustomerGroup($data, $fieldSet)
    {
        if (!isset($data['cust_group'])) {
            $this->_addError(sprintf('The "cust_group" value in the "%s" set is a required field.', $fieldSet));
        } else {
            if (!is_numeric($data['cust_group'])) {
                $this->_addError(sprintf('Invalid "cust_group" value in the "%s" set', $fieldSet));
            } else {
                $customerGroup = Mage::getModel('Mage_Customer_Model_Group')->load($data['cust_group']);
                if (is_null($customerGroup->getId())) {
                    $this->_addError(sprintf('Invalid "cust_group" value in the "%s" set', $fieldSet));
                }
            }
        }
    }
}
