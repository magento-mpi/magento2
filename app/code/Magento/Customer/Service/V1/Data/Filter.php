<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

class Filter extends \Magento\Service\Data\AbstractObject
{
    /**
     * @return string
     */
    public function getField()
    {
        return $this->_get('field');
    }

    /**
     * @return string | string[]
     */
    public function getValue()
    {
        return $this->_get('value');
    }

    /**
     * @return string
     */
    public function getConditionType()
    {
        return $this->_get('condition_type');
    }
}
