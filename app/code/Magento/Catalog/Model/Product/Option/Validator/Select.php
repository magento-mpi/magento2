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
     * @param array $values
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

        $storeId = $option->getProduct()->getStoreId();
        foreach ($option->getData('values') as $value) {
            $type = isset($value['price_type']) ? $value['price_type'] : null;
            $price = isset($value['price']) ? $value['price'] : null;
            $title = isset($value['title']) ? $value['title'] : null;
            if (!$this->isValidOptionPrice($type, $price, $storeId)
                || !$this->isValidOptionTitle($title, $storeId)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate option price
     *
     * @param $priceType
     * @param $price
     * @param $storeId
     * @return bool
     */
    protected function isValidOptionPrice($priceType, $price, $storeId)
    {
        // we should be able to remove website values for default store fallback
        if ($storeId > 0 && $priceType === null && $price === null) {
            return true;
        }
        if (!$this->isInRange($priceType, $this->priceTypes) || $this->isNegative($price)) {
            return false;
        }

        return true;
    }
}
