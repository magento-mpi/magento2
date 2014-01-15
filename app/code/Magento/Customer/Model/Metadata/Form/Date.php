<?php
/**
 * Form Element Date Data Model
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata\Form;

class Date extends AbstractData
{
    /**
     * Extract data from request and return value
     *
     * @param \Magento\App\RequestInterface $request
     * @return array|string
     */
    public function extractValue(\Magento\App\RequestInterface $request)
    {
        $value = $this->_getRequestValue($request);
        return $this->_applyInputFilter($value);
    }

    /**
     * Validate data
     * Return true or array of errors
     *
     * @param array|string $value
     * @return boolean|array
     */
    public function validateValue($value)
    {
        $errors     = array();
        $attribute  = $this->getAttribute();
        $label      = $attribute->getStoreLabel();

        if ($value === false) {
            // try to load original value and validate it
            $value = $this->_value;
        }

        if ($attribute->getIsRequired() && empty($value)) {
            $errors[] = __('"%1" is a required value.', $label);
        }

        if (!$errors && !$attribute->getIsRequired() && empty($value)) {
            return true;
        }

        $result = $this->_validateInputRule($value);
        if ($result !== true) {
            $errors = array_merge($errors, $result);
        }

        //range validation
        $validateRules = $attribute->getValidationRules();
        if ((!empty($validateRules['date_range_min']) && (strtotime($value) < $validateRules['date_range_min']))
            || (!empty($validateRules['date_range_max']) && (strtotime($value) > $validateRules['date_range_max']))
        ) {
            if (!empty($validateRules['date_range_min']) && !empty($validateRules['date_range_max'])) {
                $errors[] = __('Please enter a valid date between %1 and %2 at %3.', date('d/m/Y', $validateRules['date_range_min']), date('d/m/Y', $validateRules['date_range_max']), $label);
            } elseif (!empty($validateRules['date_range_min'])) {
                $errors[] = __('Please enter a valid date equal to or greater than %1 at %2.', date('d/m/Y', $validateRules['date_range_min']), $label);
            } elseif (!empty($validateRules['date_range_max'])) {
                $errors[] = __('Please enter a valid date less than or equal to %1 at %2.', date('d/m/Y', $validateRules['date_range_max']), $label);
            }
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

    /**
     * Export attribute value to entity model
     *
     * @param array|string $value
     * @return string
     */
    public function compactValue($value)
    {
        if ($value !== false) {
            if (empty($value)) {
                $value = null;
            }
            return $value;
        }
        return false;
    }


    /**
     * Restore attribute value from SESSION to entity model
     *
     * @param array|string $value
     * @return \Magento\Eav\Model\Attribute\Data\Date
     */
    public function restoreValue($value)
    {
        return $this->compactValue($value);
    }

    /**
     * Return formated attribute value from entity model
     *
     * @param string $format
     * @return string|array
     */
    public function outputValue($format = \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_TEXT)
    {
        $value = $this->_value;
        if ($value) {
            switch ($format) {
                case \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_TEXT:
                case \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_HTML:
                case \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_PDF:
                    $this->_dateFilterFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM);
                    break;
            }
            $value = $this->_applyOutputFilter($value);
        }

        $this->_dateFilterFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);

        return $value;
    }
}
