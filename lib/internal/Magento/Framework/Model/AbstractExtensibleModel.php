<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Model;

/**
 * Abstract model with custom attributes support.
 */
abstract class AbstractExtensibleModel extends AbstractModel implements \Magento\Framework\Api\ExtensibleDataInterface
{
    const CUSTOM_ATTRIBUTES_KEY = 'custom_attributes';

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributes()
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY]) ? $this->_data[self::CUSTOM_ATTRIBUTES_KEY] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttribute($attributeCode)
    {
        $customAttributes = $this->getCustomAttributes();
        return isset($customAttributes[$attributeCode]) ? $customAttributes[$attributeCode] : null;
    }

    /**
     * Set custom attribute value.
     *
     * @param \Magento\Framework\Api\AttributeInterface $attribute
     * @return $this
     */
    public function setCustomAttribute(\Magento\Framework\Api\AttributeInterface $attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        if (!isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode])
            || ($this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode] != $attribute)
        ) {
            $this->_hasDataChanges = true;
        }
        $this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode] = $attribute;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value = null)
    {
        if ($key == self::CUSTOM_ATTRIBUTES_KEY) {
            throw new \LogicException("Custom attributes must be set only using setCustomAttribute() method.");
        }
        return parent::setData($key, $value);
    }

    /**
     * {@inheritdoc}
     *
     * In addition to parent implementation custom attributes support is added.
     */
    public function getData($key = '', $index = null)
    {
        if ($key == self::CUSTOM_ATTRIBUTES_KEY) {
            throw new \LogicException("Custom attributes array should be retrieved via getCustomAttributes() only.");
        } else if ($key == '') {
            /** Represent model data and custom attributes as a flat array */
            $data = array_merge($this->_data, $this->getCustomAttributes());
            unset($data[self::CUSTOM_ATTRIBUTES_KEY]);
        } else {
            $data = parent::getData($key, $index);
            if ($data === null) {
                /** Try to find necessary data in custom attributes */
                $data = parent::getData(self::CUSTOM_ATTRIBUTES_KEY . "/{$key}", $index);
            }
        }
        return $data;
    }
}
