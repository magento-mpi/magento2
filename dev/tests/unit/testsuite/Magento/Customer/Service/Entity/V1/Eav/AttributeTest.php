<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\Entity\V1\Eav;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_CODE = 'ATTRIBUTE_CODE';

    const VALUE = 'VALUE';

    public function testConstructorAndGetters()
    {
        $attribute = new Attribute(self::ATTRIBUTE_CODE, self::VALUE);

        $this->assertSame(self::ATTRIBUTE_CODE, $attribute->getAttributeCode());
        $this->assertSame(self::VALUE, $attribute->getValue());
    }

    public function testSetters()
    {
        $attribute = new Attribute(self::ATTRIBUTE_CODE, self::VALUE);
        $attribute->setValue('other_value');

        $this->assertSame(self::ATTRIBUTE_CODE, $attribute->getAttributeCode());
        $this->assertSame('other_value', $attribute->getValue());
    }
}
