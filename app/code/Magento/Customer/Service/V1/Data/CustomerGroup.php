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
class CustomerGroup extends \Magento\Service\Data\AbstractObject
{
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get('id');
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_get('code');
    }

    /**
     * Get tax class id
     *
     * @return int
     */
    public function getTaxClassId()
    {
        return $this->_get('tax_class_id');
    }

    /**
     * Get tax class name
     *
     * @return int
     */
    public function getClassName()
    {
        return $this->_get('class_name');
    }

}
