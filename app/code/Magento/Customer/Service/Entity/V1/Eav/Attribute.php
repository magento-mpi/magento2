<?php
/**
 * Eav Attribute
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1\Eav;

class Attribute extends \Magento\Service\Entity\AbstractDto
{
    /**
     * Constants used as keys into $_data
     */
    const ATTRIBUTE_CODE = 'attribute_code';
    const VALUE = 'value';

    /**
     * @return string the attribute code
     */
    public function getAttributeCode()
    {
        return $this->_get(self::ATTRIBUTE_CODE);
    }

    /**
     * @return string the attribute value
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
