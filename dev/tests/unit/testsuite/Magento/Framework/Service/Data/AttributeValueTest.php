<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Api;

use Magento\Framework\Validator\Test\True;

class AttributeValueTest extends \PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_CODE = 'ATTRIBUTE_CODE';

    const STRING_VALUE = 'VALUE';

    const INTEGER_VALUE = 1;

    const FLOAT_VALUE = 1.0;

    const BOOLEAN_VALUE = true;

    public function testConstructorAndGettersWithString()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $attributeBuilder = $helper->getObject('\Magento\Framework\Api\AttributeValueBuilder')
            ->setAttributeCode(self::ATTRIBUTE_CODE)
            ->setValue(self::STRING_VALUE);
        $attribute = new AttributeValue($attributeBuilder);

        $this->assertSame(self::ATTRIBUTE_CODE, $attribute->getAttributeCode());
        $this->assertSame(self::STRING_VALUE, $attribute->getValue());
    }

    public function testConstructorAndGettersWithInteger()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $attributeBuilder = $helper->getObject('\Magento\Framework\Api\AttributeValueBuilder')
            ->setAttributeCode(self::ATTRIBUTE_CODE)
            ->setValue(self::INTEGER_VALUE);
        $attribute = new AttributeValue($attributeBuilder);

        $this->assertSame(self::ATTRIBUTE_CODE, $attribute->getAttributeCode());
        $this->assertSame(self::INTEGER_VALUE, $attribute->getValue());
    }

    public function testConstructorAndGettersWithFloat()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $attributeBuilder = $helper->getObject('\Magento\Framework\Api\AttributeValueBuilder')
            ->setAttributeCode(self::ATTRIBUTE_CODE)
            ->setValue(self::FLOAT_VALUE);
        $attribute = new AttributeValue($attributeBuilder);

        $this->assertSame(self::ATTRIBUTE_CODE, $attribute->getAttributeCode());
        $this->assertSame(self::FLOAT_VALUE, $attribute->getValue());
    }

    public function testConstructorAndGettersWithBoolean()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $attributeBuilder = $helper->getObject('\Magento\Framework\Api\AttributeValueBuilder')
            ->setAttributeCode(self::ATTRIBUTE_CODE)
            ->setValue(self::BOOLEAN_VALUE);
        $attribute = new AttributeValue($attributeBuilder);

        $this->assertSame(self::ATTRIBUTE_CODE, $attribute->getAttributeCode());
        $this->assertSame(self::BOOLEAN_VALUE, $attribute->getValue());
    }
}
