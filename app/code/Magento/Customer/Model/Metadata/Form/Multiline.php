<?php
/**
 * Form Element Multiline Data Model
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata\Form;

class Multiline extends Text
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
        if (!is_array($value)) {
            $value = false;
        } else {
            $value = array_map(array($this, '_applyInputFilter'), $value);
        }
        return $value;
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

        if ($value === false) {
            // try to load original value and validate it
            $value = $this->_value;
            if (!is_array($value)) {
                $value = explode("\n", $value);
            }
        }

        if (!is_array($value)) {
            $value = array($value);
        }
        for ($i = 0; $i < $attribute->getMultilineCount(); $i ++) {
            if (!isset($value[$i])) {
                $value[$i] = null;
            }
            // validate first line
            if ($i == 0) {
                $result = parent::validateValue($value[$i]);
                if ($result !== true) {
                    $errors = $result;
                }
            } else {
                if (!empty($value[$i])) {
                    $result = parent::validateValue($value[$i]);
                    if ($result !== true) {
                        $errors = array_merge($errors, $result);
                    }
                }
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
     * @return \Magento\Customer\Model\Metadata\Form\Multiline
     */
    public function compactValue($value)
    {
        if (is_array($value)) {
            $value = trim(implode("\n", $value));
        }
        return parent::compactValue($value);
    }

    /**
     * Restore attribute value from SESSION to entity model
     *
     * @param array|string $value
     * @return \Magento\Customer\Model\Metadata\Form\Multiline
     */
    public function restoreValue($value)
    {
        return $this->compactValue($value);
    }

    /**
     * Return formated attribute value from entity model
     *
     * @param string $format
     * @return array|string
     */
    public function outputValue($format = \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_TEXT)
    {
        $values = $this->_value;
        if (!is_array($values)) {
            $values = explode("\n", $values);
        }
        $values = array_map(array($this, '_applyOutputFilter'), $values);
        switch ($format) {
            case \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_ARRAY:
                $output = $values;
                break;
            case \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_HTML:
                $output = implode("<br />", $values);
                break;
            case \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_ONELINE:
                $output = implode(" ", $values);
                break;
            default:
                $output = implode("\n", $values);
                break;
        }
        return $output;
    }
}
