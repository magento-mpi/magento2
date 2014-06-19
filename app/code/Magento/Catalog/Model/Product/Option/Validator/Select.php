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
     * Validate option type fields
     *
     * @param Option $option
     * @return bool
     */
    protected function validateOptionValue(Option $option)
    {
        if (!is_array($option->getData('values')) || $this->isEmpty($option->getData('values'))) {
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
