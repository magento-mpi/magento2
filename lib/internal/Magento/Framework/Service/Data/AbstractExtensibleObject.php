<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Base Class for extensible data Objects
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractExtensibleObject extends AbstractSimpleObject implements ExtensibleDataInterface
{
    /**
     * Array key for custom attributes
     */
    const CUSTOM_ATTRIBUTES_KEY = 'custom_attributes';

    /**
     * {@inheritdoc}
     */
    public function getCustomAttribute($attributeCode)
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY])
            && isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode])
                ? $this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode]
                : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributes()
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY]) ? $this->_data[self::CUSTOM_ATTRIBUTES_KEY] : array();
    }
}
