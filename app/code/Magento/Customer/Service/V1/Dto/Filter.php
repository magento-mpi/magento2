<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

/**
 * Class Filter
 */
class Filter extends \Magento\Service\Entity\AbstractDto
{
    /**
     * Get field
     *
     * @return string
     */
    public function getField()
    {
        return $this->_get('field');
    }

    /**
     * Get value
     *
     * @return string | string[]
     */
    public function getValue()
    {
        return $this->_get('value');
    }

    /**
     * Get condition type
     *
     * @return string
     */
    public function getConditionType()
    {
        return $this->_get('condition_type');
    }
}
