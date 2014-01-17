<?php
/**
 * Form Element Multiselect Data Model
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
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

        switch ($format) {
            case \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_JSON:
            case \Magento\Customer\Model\Metadata\ElementFactory::OUTPUT_FORMAT_ARRAY:
                $output = $values;
                break;
            default:
                $output = array();
                foreach ($values as $value) {
                    if (!$value) {
                        continue;
                    }
                    $optionText = false;
                    foreach ($this->getAttribute()->getOptions() as $optionKey => $optionValue) {
                        if ($optionValue == $value) {
                            $optionText = $optionKey;
                        }
                    }
                    $output[] = $optionText;
                }
                $output = implode(', ', $output);
                break;
        }

        return $output;
    }
}
