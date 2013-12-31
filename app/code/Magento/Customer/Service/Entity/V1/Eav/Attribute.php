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

use Magento\Service\Entity\AbstractDto;

class Attribute extends AbstractDto
{
    /**
     * Constants used as keys into $_data
     */
    const ATTRIBUTE_CODE = 'attribute_code';
    const VALUE = 'value';

    /**
     * @param string $attributeCode
     * @param string $value
     */
    public function __construct($attributeCode, $value)
    {
        parent::__construct();
        $this->_set(self::ATTRIBUTE_CODE, $attributeCode);
        $this->setValue($value);
    }

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

    /**
     * @param string $value
     * @return Attribute
     */
    public function setValue($value)
    {
        return $this->_set(self::VALUE, $value);
    }
}
