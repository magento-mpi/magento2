<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Data;

/**
 * Customer Group data model.
 */
class Group extends \Magento\Framework\Service\Data\AbstractExtensibleObject
    implements \Magento\Customer\Api\Data\GroupInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const ID = 'id';
    const CODE = 'code';
    const TAX_CLASS_ID = 'tax_class_id';

    /**
     * Get ID
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
     * Get tax class ID
     *
     * @return int
     */
    public function getTaxClassId()
    {
        return $this->_get(self::TAX_CLASS_ID);
    }
}
