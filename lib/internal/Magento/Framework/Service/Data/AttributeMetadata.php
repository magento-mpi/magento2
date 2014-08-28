<?php
/**
 * Created by PhpStorm.
 * User: yuxzheng
 * Date: 8/21/14
 * Time: 4:06 PM
 */

namespace Magento\Framework\Service\Data;

/**
 * Base data object for custom attribute metadata
 */
class AttributeMetadata extends AbstractSimpleObject implements MetadataObjectInterface
{
    const ATTRIBUTE_CODE = 'attribute_code';

    /**
     * Retrieve code of the attribute.
     *
     * @return string|null
     */
    public function getAttributeCode()
    {
        return $this->_get(self::ATTRIBUTE_CODE);
    }
}
