<?php
/**
 * Form Element Multiselect Data Model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata\Form;

class Multiselect extends Select
{
    /**
     * {@inheritdoc}
     */
    public function extractValue(\Magento\App\RequestInterface $request)
    {
        $values = $this->_getRequestValue($request);
        if ($values !== false && !is_array($values)) {
            $values = array($values);
        }
        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function compactValue($value)
    {
        if (is_array($value)) {
            foreach($value as $key => $val) {
                $value[$key] = parent::compactValue($val);
            }

            $value = implode(',', $value);
        }
        return parent::compactValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function outputValue($format = \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_TEXT)
    {
        $values = $this->_value;
        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        $output = array();
        foreach ($values as $value) {
            if (!$value) {
                continue;
            }
            $output[] = $this->_getOptionText($value);
        }

        if ($format == \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_TEXT) {
            $output = implode(', ', $output);
        }

        return $output;
    }
}
