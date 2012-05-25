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
 * Catalog product resource validator for configurable product
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Validator_Product_Configurable
    extends Mage_Catalog_Model_Api2_Product_Validator_Product_Simple
{
    /**
     * Validate data specific for configurable product
     *
     * @param array $data
     * @return bool
     */
    protected function _validateProductTypeSpecificData(array $data)
    {
        parent::_validateProductTypeSpecificData($data);
        if ($this->isValid()) {
            $this->_validateConfigurableAttributes($data);
        }
        return $this->isValid();
    }

    /**
     * Check if configurable attributes data is valid
     *
     * @param array $data
     */
    protected function _validateConfigurableAttributes(array $data)
    {
        if ($this->_isCreateOperation() && !isset($data['configurable_attributes'])) {
            $this->_addError('The "configurable_attributes" array must be set in the request.');
            return;
        }
        if (isset($data['configurable_attributes'])) {
            if (!is_array($data['configurable_attributes'])) {
                $this->_addError('The "configurable_attributes" field is expected to be an array.');
            }
            foreach ($data['configurable_attributes'] as $configurableData) {
                $isFrontendLabelSet = isset($configurableData['frontend_label']);
                $frontendLabel = ($isFrontendLabelSet && trim($configurableData['frontend_label']));
                if (($this->_isCreateOperation() && !($isFrontendLabelSet && !empty($frontendLabel)))
                    || ($this->_isUpdateOperation() && $isFrontendLabelSet && empty($frontendLabel))
                ) {
                    $this->_addError(sprintf('The "frontend_label" value for the configurable attribute with code "%s" '
                        . 'is required.', $configurableData['attribute_code']));
                }
                if (isset($configurableData['frontend_label']) && !is_string($configurableData['frontend_label'])) {
                    $this->_addError(sprintf('The "frontend_label" value for the configurable attribute with code "%s" '
                        . 'is expected to be a string.', $configurableData['attribute_code']));
                }
                if (isset($configurableData['position'])
                    && !(is_numeric($configurableData['position']) && $configurableData['position'] >= 0)
                ) {
                    $this->_addError(sprintf('The "position" value for the configurable attribute with code "%s" '
                        . 'is expected to be a positive integer.', $configurableData['attribute_code']));
                }
                $allowedUseDefaultValues = array(0, 1);
                if (isset($configurableData['frontend_label_use_default'])
                    && !(is_numeric($configurableData['frontend_label_use_default'])
                    && in_array($configurableData['frontend_label_use_default'], $allowedUseDefaultValues))
                ) {
                    $this->_addError(sprintf('The "frontend_label_use_default" value for the configurable attribute '
                        . 'with code "%s" is invalid.', $configurableData['attribute_code']));
                }
                if (isset($configurableData['prices'])) {
                    $this->_validateConfigurablePrice($configurableData);
                }
            }
        }
    }

    /**
     * Check configurable price data
     *
     * @param array $configurableData
     */
    protected function _validateConfigurablePrice($configurableData)
    {
        $prices = $configurableData['prices'];
        if (!is_array($prices)) {
            $this->_addError(sprintf('The "prices" value for the configurable attribute with code "%s" '
                . 'is expected to be an array.', $configurableData['attribute_code']));
            return;
        }

        foreach ($prices as $priceItem) {
            /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            $attribute = Mage::getResourceModel('Mage_Catalog_Model_Resource_Eav_Attribute');
            $attribute->load($configurableData['attribute_code'], 'attribute_code');
            /** @var $attributeSource Mage_Eav_Model_Entity_Attribute_Source_Table */
            $attributeSource = $attribute->getSource();
            if (!isset($priceItem['option_value'])) {
                $this->_addError(sprintf('The "option_value" value must be set in all "prices" array items for the '
                    . 'configurable attribute with code "%s"', $configurableData['attribute_code']));
            } else if (!$attributeSource->getOptionId($priceItem['option_value'])) {
                $this->_addError(sprintf('The "option_value" value "%s" for the configurable attribute with '
                    . 'code "%s" is invalid.', $priceItem['option_value'], $configurableData['attribute_code']));
            } else {
                if (($this->_isCreateOperation() && !(isset($priceItem['price']) && is_numeric($priceItem['price'])))
                    || ($this->_isUpdateOperation() && isset($priceItem['price']) && !empty($priceItem['price'])
                        && !is_numeric($priceItem['price']))
                ) {
                    $this->_addError(sprintf('The "price" value for the option value "%s" in the "prices" '
                        . 'array for the configurable attribute with code "%s" is invalid.',
                        $priceItem['option_value'], $configurableData['attribute_code']));
                }
                $allowedPriceTypeValues = array('fixed', 'percent');
                $isPriceTypeSet = isset($priceItem['price_type']);
                $isPriceTypeAllowed = $isPriceTypeSet && in_array($priceItem['price_type'], $allowedPriceTypeValues);
                if (($this->_isCreateOperation() && !($isPriceTypeSet && $isPriceTypeAllowed))
                    || ($this->_isUpdateOperation() && $isPriceTypeSet && !$isPriceTypeAllowed)
                ) {
                    $this->_addError(sprintf('The "price_type" value for the option value "%s" in the '
                        . '"prices" array for the configurable attribute with code "%s" is invalid.',
                        $priceItem['option_value'], $configurableData['attribute_code']));
                }
            }
        }
    }

    /**
     * Validate attribute set for the configurable product creation
     *
     * @param array $data
     * @param Mage_Eav_Model_Entity_Type $productEntity
     * @return bool
     */
    protected function _validateAttributeSet(array $data, $productEntity)
    {
        parent::_validateAttributeSet($data, $productEntity);
        if ($this->_isCreateOperation()) {
            $attributes = $this->_getProduct()->getTypeInstance()->getSetAttributes($this->_getProduct());
            $configurableAttributesInAttributeSet = array();
            /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            foreach ($attributes as $attribute) {
                if ($this->_getProduct()->getTypeInstance()->canUseAttribute($attribute, $this->_getProduct())) {
                    $configurableAttributesInAttributeSet[] = $attribute->getAttributeCode();
                }
            }
            // ensure that the specified attribute set can be used for configurable product creation
            if (empty($configurableAttributesInAttributeSet)) {
                $this->_critical("The specified attribute set does not contain attributes which can be used "
                    . "for the configurable product.");
            }
            // check that at least one configurable attribute is set
            $configurableAttributesFromRequest = isset($data["configurable_attributes"])
                ? $data["configurable_attributes"] : array();
            if (!(is_array($configurableAttributesFromRequest) && count($configurableAttributesFromRequest))) {
                $this->_critical("At least one configurable attribute must be set to create a configurable product.");
            }
            // check that all attributes passed as "configurable_attributes" are configurable ones
            foreach ($configurableAttributesFromRequest as $attribute) {
                if (!isset($attribute['attribute_code'])) {
                    $this->_critical('All items in "configurable_attributes" must have an "attribute_code" value set.');
                }
                if (!in_array($attribute['attribute_code'], $configurableAttributesInAttributeSet)) {
                    $this->_addError("The attribute with code \"{$attribute['attribute_code']}\" cannot be used "
                        . "to create a configurable product.");
                }
            }
        }
    }
}
