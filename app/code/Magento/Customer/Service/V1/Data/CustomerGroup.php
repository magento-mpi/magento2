<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

/**
 * CustomerGroup Service Data Object
 */
class CustomerGroup extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Constants for Data Object keys
     */
    const ID = 'id';
    const CODE = 'code';
    const TAX_CLASS_ID = 'tax_class_id';

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * Get tax class id
     *
     * @return int
     */
    public function getTaxClassId()
    {
        return $this->_get(self::TAX_CLASS_ID);
    }

    /**
     * Retrieve tax class name
     *
     * @return string|null
     */
    public function getTaxClassName()
    {
        return $this->_get('tax_class_name');
    }
}
