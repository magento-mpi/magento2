<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

/**
 * Builder for the CustomerGroup Service Data Object
 *
 * @method CustomerGroup create()
 */
class CustomerGroupBuilder extends \Magento\Service\Data\AbstractObjectBuilder
{
    /**
     * Set id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->_set(CustomerGroup::ID, $id);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(CustomerGroup::CODE, $code);
    }

    /**
     * Set tax class id
     *
     * @param string $taxClassId
     * @return $this
     */
    public function setTaxClassId($taxClassId)
    {
        return $this->_set(CustomerGroup::TAX_CLASS_ID, $taxClassId);
    }
}
