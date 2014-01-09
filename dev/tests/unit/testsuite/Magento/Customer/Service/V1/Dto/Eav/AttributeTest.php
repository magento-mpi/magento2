<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Dto\Eav;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_CODE = 'ATTRIBUTE_CODE';

    const VALUE = 'VALUE';

    public function testConstructorAndGetters()
    {
        $attribute = new \Magento\Customer\Service\V1\Dto\Eav\Attribute([
            'attribute_code' => self::ATTRIBUTE_CODE,
            'value' => self::VALUE
        ]);

        $this->assertSame(self::ATTRIBUTE_CODE, $attribute->getAttributeCode());
        $this->assertSame(self::VALUE, $attribute->getValue());
    }
}
