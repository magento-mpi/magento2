<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Framework\Api;

/**
 * Custom Attribute Data object
 */
class AttributeValue extends AbstractSimpleObject implements AttributeInterface
{
    /**
     * Initialize internal storage
     *
     * @param AttributeDataBuilder $builder
     */
    public function __construct(AttributeDataBuilder $builder)
    {
        $this->_data = $builder->getData();
    }

    /**
     * Get attribute code
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->_get(self::ATTRIBUTE_CODE);
    }

    /**
     * Get attribute value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
