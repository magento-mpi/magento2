<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data;

/**
 * LinkAttribute Service Data Object
 *
 * @codeCoverageIgnore
 */
class LinkAttribute extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**#@+
     * Constants for Data Object keys
     */
    const CODE = 'code';
    const TYPE = 'type';

    /**
     * Get attribute code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * Get attribute type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }
}
