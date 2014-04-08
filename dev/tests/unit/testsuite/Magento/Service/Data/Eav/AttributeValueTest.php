<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Data\Eav;

class AttributeValueTest extends \PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_CODE = 'ATTRIBUTE_CODE';

    const VALUE = 'VALUE';

    public function testConstructorAndGetters()
    {
        $attributeBuilder = (new AttributeValueBuilder())
            ->setAttributeCode(self::ATTRIBUTE_CODE)
            ->setValue(self::VALUE);
        $attribute = new AttributeValue($attributeBuilder);

        $this->assertSame(self::ATTRIBUTE_CODE, $attribute->getAttributeCode());
        $this->assertSame(self::VALUE, $attribute->getValue());
    }
}
