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
 * Catalog product resource abstract validator
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Model_Api2_Product_Validator_ProductAbstract extends Mage_Api2_Model_Resource_Validator
{
    /**
     * The greatest decimal value which could be stored. Corresponds to DECIMAL (12,4) SQL type
     */
    const MAX_DECIMAL_VALUE = 99999999.9999;

    /**
     * Factory method for product validators creation by product type
     *
     * @param string $productType
     * @return Mage_Catalog_Model_Api2_Product_Validator_ProductAbstract
     */
    public static function getValidatorByProductType($productType)
    {
        switch ($productType) {
            case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                return Mage::getModel("catalog/api2_product_validator_product_$productType");
            default:
                throw new Mage_Api2_Exception("Creation of products with type '$productType' is not implemented",
                    Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * Check if product data is valid for update
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     * @return bool
     */
    abstract public function isValidForUpdate(Mage_Catalog_Model_Product $product, array $data);

    /**
     * Check if product data is valid for create
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     * @return bool
     */
    abstract public function isValidForCreate(Mage_Catalog_Model_Product $product, array $data);

    /**
     * Validate field to be positive number
     *
     * @param array $data
     * @param string $fieldSet
     * @param string $field
     * @param bool $required
     * @param bool $equalsZero
     * @param bool $skipIfConfigValueUsed
     */
    protected function _validatePositiveNumber($data, $fieldSet, $field, $required = true, $equalsZero = false,
        $skipIfConfigValueUsed = false)
    {
        // in case when 'Use Config Settings' is selected no validation needed
        if (!($skipIfConfigValueUsed && $this->_isConfigValueUsed($data, $field))) {
            if (!isset($data[$field]) && $required) {
                $this->_addError(sprintf('The "%s" value in the "%s" set is a required field.', $field, $fieldSet));
            }

            if (isset($data[$field])) {
                $isValid = $equalsZero ? $data[$field] >= 0 : $data[$field] > 0;
                if (!(is_numeric($data[$field]) && $isValid)) {
                    $message = $equalsZero
                        ? 'Please enter a number 0 or greater in the "%s" field in the "%s" set.'
                        : 'Please enter a number greater than 0 in the "%s" field in the "%s" set.';
                    $this->_addError(sprintf($message, $field, $fieldSet));
                }
            }
        }
    }

    /**
     * Validate field to be a positive number
     *
     * @param array $data
     * @param string $fieldSet
     * @param string $field
     * @param bool $required
     * @param bool $skipIfConfigValueUsed
     */
    protected function _validatePositiveNumeric($data, $fieldSet, $field, $required = false,
        $skipIfConfigValueUsed = false)
    {
        // in case when 'Use Config Settings' is selected no validation needed
        if (!($skipIfConfigValueUsed && $this->_isConfigValueUsed($data, $field))) {
            if (!isset($data[$field]) && $required) {
                $this->_addError(sprintf('The "%s" value in the "%s" set is a required field.', $field, $fieldSet));
            }

            if (isset($data[$field]) && (!is_numeric($data[$field]) || $data[$field] < 0)) {
                $this->_addError(sprintf('Please use numbers only in the "%s" field in the "%s" set. ' .
                    'Please avoid spaces or other non numeric characters.', $field, $fieldSet));
            }
        }
    }

    /**
     * Validate field to be a number
     *
     * @param array $data
     * @param string $fieldSet
     * @param string $field
     * @param bool $required
     * @param bool $skipIfConfigValueUsed
     */
    protected function _validateNumeric($data, $fieldSet, $field, $required = false, $skipIfConfigValueUsed = false)
    {
        // in case when 'Use Config Settings' is selected no validation needed
        if (!($skipIfConfigValueUsed && $this->_isConfigValueUsed($data, $field))) {
            if (!isset($data[$field]) && $required) {
                $this->_addError(sprintf('The "%s" value in the "%s" set is a required field.', $field, $fieldSet));
            }

            if (isset($data[$field]) && !is_numeric($data[$field])) {
                $this->_addError(sprintf('Please enter a valid number in the "%s" field in the "%s" set.',
                    $field, $fieldSet));
            }
        }
    }

    /**
     * Validate dropdown fields value
     *
     * @param array $data
     * @param string $fieldSet
     * @param string $field
     * @param string $sourceModelName
     * @param bool $skipIfConfigValueUsed
     */
    protected function _validateSource($data, $fieldSet, $field, $sourceModelName, $skipIfConfigValueUsed = false)
    {
        // in case when 'Use Config Settings' is selected no validation needed
        if (!($skipIfConfigValueUsed && $this->_isConfigValueUsed($data, $field))) {
            if (isset($data[$field])) {
                $sourceModel = Mage::getSingleton($sourceModelName);
                if ($sourceModel) {
                    $allowedValues = $this->_getAttributeAllowedValues($sourceModel->toOptionArray());
                    $useStrictMode = !is_numeric($data[$field]);
                    if (!in_array($data[$field], $allowedValues, $useStrictMode)) {
                        $this->_addError(sprintf('Invalid "%s" value in the "%s" set.', $field, $fieldSet));
                    }
                }
            }
        }
    }

    /**
     * Validate bolean fields value
     *
     * @param array $data
     * @param string $fieldSet
     * @param string $field
     * @param bool $skipIfConfigValueUsed
     */
    protected function _validateBoolean($data, $fieldSet, $field, $skipIfConfigValueUsed = false)
    {
        // in case when 'Use Config Settings' is selected no validation needed
        if (!($skipIfConfigValueUsed && $this->_isConfigValueUsed($data, $field))) {
            if (isset($data[$field])) {
                $allowedValues = $this->_getAttributeAllowedValues(
                    Mage::getSingleton('Mage_Eav_Model_Entity_Attribute_Source_Boolean')->getAllOptions());
                $useStrictMode = !is_numeric($data[$field]);
                if (!in_array($data[$field], $allowedValues, $useStrictMode)) {
                    $this->_addError(sprintf('Invalid "%s" value in the "%s" set.', $field, $fieldSet));
                }
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
     * Check if value from config is used
     *
     * @param array $data
     * @param string $field
     * @return bool
     */
    protected function _isConfigValueUsed($data, $field)
    {
        return isset($data["use_config_$field"]) && $data["use_config_$field"];
    }
}
