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
 * API2 Category validator
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Category_Validator_Category extends Mage_Api2_Model_Resource_Validator
{
    /**
     * Current category
     *
     * @var Mage_Catalog_Model_Category
     */
    protected $_category = null;

    /**
     * Current operation
     *
     * @var string
     */
    protected $_operation = null;

    /**
     * Set current operation and category
     *
     * @param $options
     */
    public function __construct($options)
    {
        if (!isset($options['operation']) || empty($options['operation'])) {
            throw new Exception("'operation' parameter must be set for validation");
        }
        $this->_operation = $options['operation'];
    }

    /**
     * Get category
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategory()
    {
        if (!(isset($this->_category))) {
            throw new Exception("Category is not set or invalid");
        }
        return $this->_category;
    }

    /**
     * Set category for validation
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Api2_Category_Validator_Category
     */
    protected function _setCategory(Mage_Catalog_Model_Category $category)
    {
        $this->_category = $category;
        return $this;
    }

    /**
     * Is update mode
     *
     * @return bool
     */
    protected function _isUpdate()
    {
        return $this->_operation == Mage_Api2_Model_Resource::OPERATION_UPDATE;
    }

    /**
     * Check if category is valid for save
     *
     * @param Mage_Catalog_Model_Category $category
     * @return bool
     */
    public function isValidForSave(Mage_Catalog_Model_Category $category)
    {
        $this->_setCategory($category);
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach ($this->_getCategory()->getAttributes() as $attribute) {
            $this->_validateRequiredFields($attribute)
                ->_validateNotEmptyStrings($attribute)
                ->_validateAttributesWithSources($attribute)
                ->_validateDates($attribute)
                ->_validatePositiveNumbers($attribute)
                ->_validateNumbers($attribute)
                ->_validateImages($attribute);
        }
        $this->_validateCustomDesignUseParentSettings();
        $this->_validateParentId();
        $this->_validateId();
        $this->_validateAttributes();
        return empty($this->_errors);
    }

    /**
     * Check if category is valid for delete
     *
     * @param Mage_Catalog_Model_Category $category
     * @return bool
     */
    public function isValidForDelete(Mage_Catalog_Model_Category $category)
    {
        $this->_setCategory($category);
        // check if the category is category tree root
        if ($this->_getCategory()->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
            $this->_addError("The tree root category cannot be deleted.");
        }
        // check if the category is root for some store
        /** @var $categoryResource Mage_Catalog_Model_Resource_Category */
        $categoryResource = $this->_getCategory()->getResource();
        if ($categoryResource->isForbiddenToDelete($this->_getCategory()->getId())) {
            $this->_addError("The root category cannot be deleted if there is a store associated with it.");
        }
        return empty($this->_errors);
    }

    /**
     * Make sure that specified parent category is valid
     */
    protected function _validateParentId()
    {
        if ($this->_isUpdate()) {
            // check if non-root category is not updated to become a root one
            if ($this->_getCategory()->getData('parent_id') != $this->_getCategory()->getParentId()
                && $this->_getCategory()->getData('parent_id') == Mage_Catalog_Model_Category::TREE_ROOT_ID
            ) {
                $this->_addFieldError("Non-root category cannot be updated to become a root one.", 'parent_id');
            }
            // make sure that category parent ID is not equal to its ID
            if ($this->_getCategory()->getData('parent_id') == $this->_getCategory()->getId()) {
                $this->_addFieldError("Category 'parent_id' value cannot be equal to its 'id' value.", 'parent_id');
            }
            // ensure that category is not moved under one of its children
            /** @var $parentCategory Mage_Catalog_Model_Category */
            $parentCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($this->_getCategory()->getData('parent_id'));
            if (!$this->_isFieldInvalid('parent_id')
                && in_array($this->_getCategory()->getId(), $parentCategory->getPathIds())
            ) {
                $this->_addFieldError("The category cannot be moved under one of its child categories.", 'parent_id');
            }
        } else if ($storeId = $this->_getCategory()->getData('store_id')) {
            // check if store root category is not created in scope of specific store
            if ($this->_getCategory()->getData('parent_id') == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $this->_addError("Root category cannot be created in the scope of specific store.");
            }
        }
        // check if parent category belongs to specified store
        if ($storeId = $this->_getCategory()->getData('store_id')) {
            $store = Mage::app()->getStore($storeId);
            $parentPathIds = explode('/', $this->_getCategory()->getPath());
            $doesParentCategoryBelongToSpecifiedStore = in_array($store->getRootCategoryId(), $parentPathIds);
            if (!$doesParentCategoryBelongToSpecifiedStore) {
                $this->_addError("The specified parent category does not match the specified store.");
            }
        }
    }

    /**
     * Ensure that the category which is intended to be updated is not the tree root categroy
     */
    protected function _validateId()
    {
        if ($this->_isUpdate()) {
            if ($this->_getCategory()->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
                $this->_addError("The tree root category cannot be changed.");
            }
        }
    }

    /**
     * Check if parent category settings could be used. This option is not available for root categories
     */
    protected function _validateCustomDesignUseParentSettings()
    {
        $attributeCode = 'custom_use_parent_settings';
        if ($this->_getCategory()->getData($attributeCode)
            && $this->_getCategory()->getData('parent_id') == Mage_Catalog_Model_Category::TREE_ROOT_ID
        ) {
            $this->_addFieldError("Custom design option 'custom_use_parent_settings' " .
                "cannot be used for root categories.", $attributeCode);
        }
    }

    /**
     * Perform native attributes validation
     *
     * @return Mage_Catalog_Model_Api2_Category_Validator_Category
     */
    protected function _validateAttributes()
    {
        $attributesWithConfigValueUsed = array();
        foreach ($this->_getCategory()->getData() as $attributeName => $attributeValue) {
            if ($this->_isConfigValueUsed($attributeName)) {
                $attributesWithConfigValueUsed[] = $attributeName;
            }
        }
        if (count($attributesWithConfigValueUsed)) {
            // set the 'use_post_data_config' for validation process
            $this->_getCategory()->setData("use_post_data_config", $attributesWithConfigValueUsed);
        }
        try {
            $this->_getCategory()->validate();
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            if (!$this->_isFieldInvalid($e->getAttributeCode())) {
                $this->_addFieldError(sprintf('Invalid value provided for "%s" attribute: %s', $e->getAttributeCode(),
                    $e->getMessage()), $e->getAttributeCode());
            }
        }
        $this->_getCategory()->unsetData("use_post_data_config");
        return $this;
    }

    /**
     * Check if image attributes are valid
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Mage_Catalog_Model_Api2_Category_Validator_Category
     */
    protected function _validateImages($attribute)
    {
        $imageAttributes = array('image', 'thumbnail');
        $attributeCode = $attribute->getAttributeCode();
        if (!$this->_isFieldInvalid($attributeCode) && in_array($attributeCode, $imageAttributes)) {
            $attributeValue = $this->_getCategory()->getData($attributeCode);
            if (!is_null($attributeValue)) {
                if ($this->_isUpdate() && is_string($attributeValue)) {
                    // attribute value is not updated as it already contains image name
                } else if ($this->_isUpdate() && is_array($attributeValue)
                    && isset($attributeValue['delete']) && $attributeValue['delete']
                ) {
                    // data is valid for update, image will be deleted
                } else {
                    /** @var $imageUploader Mage_Api2_Model_Request_Uploader_Image */
                    $imageUploader = Mage::getModel('Mage_Api2_Model_Request_Uploader_Image');
                    if ($errorMessage = $imageUploader->validate($attributeValue)) {
                        $this->_addFieldError(sprintf('Invalid value given for "%s" attribute: "%s"',
                            $attributeCode, $errorMessage), $attributeCode);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Check if all required attributes are set
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Mage_Catalog_Model_Api2_Category_Validator_Category
     */
    protected function _validateRequiredFields($attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        if (!$this->_isFieldInvalid($attributeCode) && $attribute->getIsRequired()
            && !$this->_isConfigValueUsed($attributeCode)
        ) {
            $attributeValue = $this->_getCategory()->getData($attributeCode);
            if (is_null($attributeValue) || (empty($attributeValue) && !is_numeric($attributeValue))) {
                $this->_addFieldError(sprintf('Attribute "%s" is required', $attributeCode), $attributeCode);
            }
        }
        return $this;
    }

    /**
     * Validate attributes that cannot contain empty values
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Mage_Catalog_Model_Api2_Category_Validator_Category
     */
    protected function _validateNotEmptyStrings($attribute)
    {
        $notEmptyAttributes = array('name');
        $attributeCode = $attribute->getAttributeCode();
        if (!$this->_isFieldInvalid($attributeCode) && in_array($attributeCode, $notEmptyAttributes)) {
            $attributeValue = $this->_getCategory()->getData($attributeCode);
            if (!is_null($attributeValue) && is_string($attributeValue) && preg_match("/^\s*$/", $attributeValue)) {
                $this->_addFieldError(sprintf('"%s" attribute cannot contain invisible characters only.',
                    $attributeCode), $attributeCode);
            }
        }
        return $this;
    }

    /**
     * Validate date attributes
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Mage_Catalog_Model_Api2_Category_Validator_Category
     */
    protected function _validateDates($attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        if (!$this->_isFieldInvalid($attributeCode) && $attribute->getBackendType() == 'datetime') {
            $attributeValue = $this->_getCategory()->getData($attributeCode);
            $zendDate = new Zend_Date();
            if (!is_null($attributeValue) && !$zendDate->isDate($attributeValue, Varien_Date::DATE_INTERNAL_FORMAT)) {
                $this->_addFieldError(sprintf('Date value for the "%s" attribute is invalid or has invalid '
                        . 'format. Please use the following format: "%s"', $attributeCode,
                    Varien_Date::DATE_INTERNAL_FORMAT), $attributeCode);
            }
        }
        return $this;
    }

    /**
     * Validate numeric attributes
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Mage_Catalog_Model_Api2_Category_Validator_Category
     */
    protected function _validateNumbers($attribute)
    {
        $numericAttributes = array();
        $attributeCode = $attribute->getAttributeCode();
        if (!$this->_isFieldInvalid($attributeCode) && in_array($attributeCode, $numericAttributes)) {
            $attributeValue = $this->_getCategory()->getData($attributeCode);
            if (!is_null($attributeValue) && !is_numeric($attributeValue)) {
                $this->_addFieldError(sprintf('"%s" attribute must have numeric value. "%s" given',
                    $attributeCode, $attributeValue), $attributeCode);
            }
        }
        return $this;
    }

    /**
     * Validate positive numeric attributes
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Mage_Catalog_Model_Api2_Category_Validator_Category
     */
    protected function _validatePositiveNumbers($attribute)
    {
        $positiveAttributes = array('filter_price_range');
        $attributeCode = $attribute->getAttributeCode();
        if (!$this->_isFieldInvalid($attributeCode) && in_array($attributeCode, $positiveAttributes)) {
            $attributeValue = $this->_getCategory()->getData($attributeCode);
            if (!is_null($attributeValue) && (!is_numeric($attributeValue) || ($attributeValue < 0))) {
                $this->_addFieldError(sprintf('"%s" attribute must have numeric positive value. "%s" given',
                    $attributeCode, $attributeValue), $attributeCode);
            }
        }
        return $this;
    }

    /**
     * Validate attributes with source model
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return Mage_Catalog_Model_Api2_Category_Validator_Category
     */
    protected function _validateAttributesWithSources($attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        $attributeValue = $this->_getCategory()->getData($attributeCode);
        if (!$this->_isFieldInvalid($attributeCode) && $attribute->usesSource() && !is_null($attributeValue)) {
            $allowedValues = $this->_formatAllowedSourceValues($attribute->getSource()->getAllOptions());

            if (!is_array($attributeValue)) {
                // make select and multiselect validation proces unified
                $attributeValue = array($attributeValue);
            }
            foreach ($attributeValue as $selectValue) {
                $isValid = false;
                foreach ($allowedValues as $allowedValue) {
                    // perform comparison without type check only when both values are numeric
                    $useStrictMode = !(is_numeric($selectValue) && is_numeric($allowedValue));
                    $isValid = $useStrictMode ? ($selectValue === $allowedValue) : ($selectValue == $allowedValue);
                    if ($isValid) {
                        break;
                    }
                }
                if (!$isValid) {
                    $this->_addFieldError(sprintf('Invalid value "%s" provided for "%s" attribute.',
                        $selectValue, $attributeCode), $attributeCode);
                }
            }
        }
        return $this;
    }

    /**
     * Format attribute allowed values from source model to plain array
     *
     * @param array $options
     * @return array
     */
    protected function _formatAllowedSourceValues(array $options)
    {
        $values = array();
        foreach ($options as $option) {
            if (isset($option['value'])) {
                $value = $option['value'];
                if (is_array($value)) {
                    $values = array_merge($values, $this->_formatAllowedSourceValues($value));
                } else {
                    $values[] = $value;
                }
            }
        }
        return $values;
    }

    /**
     * Add error message
     * Mark field as invalid to prevent future validation
     * Change validation status to 'failed'
     *
     * @param string $error
     * @param string $field
     * @return Mage_Api2_Model_Resource_Validator
     */
    protected function _addFieldError($error, $field)
    {
        $this->_addInvalidField($field);
        return parent::_addError($error);
    }

    /**
     * Determine if value from config should be used for specified attribute
     *
     * @param string $attributeCode
     * @return bool
     */
    protected function _isConfigValueUsed($attributeCode)
    {
        return (bool)$this->_getCategory()->getData("use_config_$attributeCode");
    }
}
