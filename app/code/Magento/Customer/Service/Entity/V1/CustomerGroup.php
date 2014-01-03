<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1;

class CustomerGroup extends \Magento\Service\Entity\AbstractDto
{
    /**
     * @return int
     */
    public function getId()
    {
        return $this->_get('id');
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_get('code');
    }

    /**
     * @return int
     */
    public function getTaxClassId()
    {
        return $this->_get('tax_class_id');
    }
}
