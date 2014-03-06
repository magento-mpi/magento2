<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

/**
 * Class CustomerGroupBuilder
 */
class CustomerGroupBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * Set id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->_set('id', $id);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set('code', $code);
    }

    /**
     * Set tax class id
     *
     * @param string $taxClassId
     * @return $this
     */
    public function setTaxClassId($taxClassId)
    {
        return $this->_set('tax_class_id', $taxClassId);
    }
}
