<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

/**
 * Tax class data
 */
class TaxClass extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**#@+
     *
     * Tax class field key.
     */
    const KEY_ID = 'class_id';
    const KEY_NAME = 'class_name';
    const KEY_TYPE = 'class_type';
    /**#@-*/

    /**
     * Get tax class ID.
     *
     * @return int|null
     */
    public function getClassId()
    {
        return $this->_get(self::KEY_ID);
    }

    /**
     * Get tax class name.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->_get(self::KEY_NAME);
    }

    /**
     * Get tax class type.
     *
     * @return string
     */
    public function getClassType()
    {
        return $this->_get(self::KEY_TYPE);
    }
}
