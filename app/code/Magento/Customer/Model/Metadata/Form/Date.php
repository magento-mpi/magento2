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
     * {@inheritdoc}
     */
    public function extractValue(\Magento\App\RequestInterface $request)
    {
        $value = $this->_getRequestValue($request);
        return $this->_applyInputFilter($value);
    }

    /**
     * {@inheritdoc}
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

        if ($attribute->isRequired() && empty($value)) {
            $errors[] = __('"%1" is a required value.', $label);
        }

        if (!$errors && !$attribute->isRequired() && empty($value)) {
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function restoreValue($value)
    {
        return $this->compactValue($value);
    }

    /**
     * {@inheritdoc}
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
