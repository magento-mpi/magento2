<?php
/**
 * Created by PhpStorm.
 * User: yuxzheng
 * Date: 8/21/14
 * Time: 4:06 PM
 */

namespace Magento\Framework\Service\Data;

use Magento\Framework\Service\Data\Eav\MetadataObjectInterface;

/**
 * Base data object for custom attribute metadata
 */
class AttributeMetadata extends AbstractObject implements MetadataObjectInterface
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
