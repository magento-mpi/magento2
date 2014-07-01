<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Rule Product Condition data model
 */
namespace Magento\CatalogRule\Model\Rule\Condition;

class Product extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{

    /**
     * Validate product attribute value for condition
     *
     * @param \Magento\Framework\Object $object
     * @return bool
     */
    public function validate(\Magento\Framework\Object $object)
    {
        $attrCode = $this->getAttribute();
        if ('category_ids' == $attrCode) {
            return $this->validateAttribute($object->getAvailableInCategories());
        }
        if ('attribute_set_id' == $attrCode) {
            return (bool)$object->hasData($attrCode);
        }

        $oldAttrValue = $object->hasData($attrCode) ? $object->getData($attrCode) : null;
        if (!empty($this->_entityAttributeValues[$object->getId()])) {
            $object->setData($attrCode, $this->_getAttributeValue($object));
        }

        $result = $this->_validateProduct($object);
        $this->_restoreOldAttrValue($object, $oldAttrValue);

        return (bool)$result;
    }

    /**
     * Validate product
     *
     * @param \Magento\Framework\Object $object
     * @return bool
     */
    protected function _validateProduct($object)
    {
        return $this->validateAttribute($object->getData($this->getAttribute()));
    }

    /**
     * Restore old attribute value
     *
     * @param \Magento\Framework\Object $object
     * @param mixed $oldAttrValue
     */
    protected function _restoreOldAttrValue($object, $oldAttrValue)
    {
        $attrCode = $this->getAttribute();
        if (is_null($oldAttrValue)) {
            $object->unsetData($attrCode);
        } else {
            $object->setData($attrCode, $oldAttrValue);
        }
    }

    /**
     * Get attribute value
     *
     * @param \Magento\Framework\Object $object
     * @return mixed
     */
    protected function _getAttributeValue($object)
    {
        $storeId = $object->getStoreId();
        $defaultStoreId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        $productValues  = $this->_entityAttributeValues[$object->getId()];
        $value = isset($productValues[$storeId]) ? $productValues[$storeId] : $productValues[$defaultStoreId];

        $value = $this->_prepareDatetimeValue($value, $object);
        $value = $this->_prepareMultiselectValue($value, $object);

        return $value;
    }

    /**
     * Prepare datetime attribute value
     *
     * @param mixed $value
     * @param \Magento\Framework\Object $object
     * @return mixed
     */
    protected function _prepareDatetimeValue($value, $object)
    {
        $attribute = $object->getResource()->getAttribute($this->getAttribute());
        if ($attribute && $attribute->getBackendType() == 'datetime') {
            $value = strtotime($value);
        }
        return $value;
    }

    /**
     * Prepare multiselect attribute value
     *
     * @param mixed $value
     * @param \Magento\Framework\Object $object
     * @return mixed
     */
    protected function _prepareMultiselectValue($value, $object)
    {
        $attribute = $object->getResource()->getAttribute($this->getAttribute());
        if ($attribute && $attribute->getFrontendInput() == 'multiselect') {
            $value = strlen($value) ? explode(',', $value) : array();
        }
        return $value;
    }
}
