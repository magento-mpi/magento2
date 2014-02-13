<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Widget;

/**
 * Test class for \Magento\Customer\Block\Widget\Taxvat
 *
 * @magentoAppArea frontend
 */
class TaxvatTest extends \PHPUnit_Framework_TestCase
{
    /* Constants used in the unit tests */
    const CUSTOMER_ENTITY_TYPE = 'customer';
    const TAXVAT_ATTRIBUTE_CODE = 'taxvat';

    public function testGetDateFormat()
    {
        $attribute
            = $this->getMock('Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata', [], [], '', false);
        $attribute->expects($this->any())
            ->method('isRequired')
            ->will($this->returnValue(true));

        $attributeMetadata
            = $this->getMockForAbstractClass(
            'Magento\Customer\Service\V1\CustomerMetadataServiceInterface',
            [],
            '',
            false
        );
        $attributeMetadata->expects($this->any())->method('getAttributeMetadata')
            ->with(self::CUSTOMER_ENTITY_TYPE, self::TAXVAT_ATTRIBUTE_CODE)
            ->will($this->returnValue($attribute));

        /** @var \Magento\Customer\Block\Widget\Taxvat $block */
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Block\Widget\Taxvat', ['attributeMetadata' => $attributeMetadata]);
        $this->assertContains('<div class="field taxvat required">', $block->toHtml());
    }
}
