<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data;

/**
 * LinkType Service Data Object
 *
 * @codeCoverageIgnore
 */
class LinkType extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**#@+
     * Constants for Data Object keys
     */
    const TYPE = 'type';
    const CODE = 'code';

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * Get code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }
}
