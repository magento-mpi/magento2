<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

class CustomerGroupBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param string $id
     *
     * @return CustomerGroupBuilder
     */
    public function setId($id)
    {
        return $this->_set('id', $id);
    }

    /**
     * @param string $code
     *
     * @return CustomerGroupBuilder
     */
    public function setCode($code)
    {
        return $this->_set('code', $code);
    }

    /**
     * @param string $taxClassId
     *
     * @return CustomerGroupBuilder
     */
    public function setTaxClassId($taxClassId)
    {
        return $this->_set('tax_class_id', $taxClassId);
    }
}
