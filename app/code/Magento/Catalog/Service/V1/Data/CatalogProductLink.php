<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data;

/**
 * CatalogProductLink Service Data Object
 */
class CatalogProductLink extends \Magento\Framework\Service\Data\AbstractObject
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
