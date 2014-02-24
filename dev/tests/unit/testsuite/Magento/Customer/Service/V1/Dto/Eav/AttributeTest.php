<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data\Eav;

use Magento\Customer\Service\V1\Data\Eav\Attribute;
use Magento\Customer\Service\V1\Data\Eav\AttributeBuilder;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_CODE = 'ATTRIBUTE_CODE';

    const VALUE = 'VALUE';

    public function testConstructorAndGetters()
    {
        $attributeBuilder = (new AttributeBuilder())->setAttributeCode(self::ATTRIBUTE_CODE)->setValue(self::VALUE);
        $attribute = new Attribute($attributeBuilder);

        $this->assertSame(self::ATTRIBUTE_CODE, $attribute->getAttributeCode());
        $this->assertSame(self::VALUE, $attribute->getValue());
    }
}
