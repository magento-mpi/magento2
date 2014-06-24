<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option\Validator;

use Magento\Catalog\Model\Product\Option;

class Select extends DefaultValidator
{
    /**
     * Check if all values are marked for removal
     *
     * @param $values
     * @return bool
     */
    protected function checkAllValuesRemoved($values)
    {
        foreach ($values as $value) {
            if (!array_key_exists('is_delete', $value) || $value['is_delete'] != 1) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate option type fields
     *
     * @param Option $option
     * @return bool
     */
    protected function validateOptionValue(Option $option)
    {
        $values = $option->getData('values');
        if (!is_array($values) || $this->isEmpty($values)) {
            return false;
        }

        //forbid removal of last value for option
        if ($this->checkAllValuesRemoved($values)) {
            return false;
        }

        foreach ($option->getData('values') as $value) {
            $type = isset($value['price_type']) ? $value['price_type'] : '';
            $price = isset($value['price']) ? $value['price'] : 0;
            $title = isset($value['title']) ? $value['title'] : '';
            if (!$this->isInRange($type, $this->priceTypes) || $this->isNegative($price) || $this->isEmpty($title)) {
                return false;
            }
        }
        return true;
    }
}
