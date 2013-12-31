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
     * @param int $id
     * @param string $code
     * @param int $taxClassId
     */
    public function __construct($id = null, $code = null, $taxClassId = null)
    {
        parent::__construct();
        $this->setId($id);
        $this->setCode($code);
        $this->setTaxClassId($taxClassId);
    }

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

    /**
     * @param string $id
     *
     * @return CustomerGroup
     */
    public function setId($id)
    {
        return $this->_set('id', $id);
    }

    /**
     * @param string $code
     *
     * @return CustomerGroup
     */
    public function setCode($code)
    {
        return $this->_set('code', $code);
    }
    /**
     * @param string $taxClassId
     *
     * @return CustomerGroup
     */
    public function setTaxClassId($taxClassId)
    {
        return $this->_set('tax_class_id', $taxClassId);
    }
}