<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\Entity\V1\Eav;

class AttributeMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Constants for testing
     */
    const ATTRIBUTE_CODE = 'ATTRIBUTE_CODE';
    const FRONT_END_INPUT = 'FRONT_END_INPUT';
    const INPUT_FILTER = 'INPUT_FILTER';
    const STORE_LABEL = 'STORE_LABEL';
    const VALIDATION_RULES = 'VALIDATION_RULES';

    public function testConstructorAndGetters()
    {
        $options = array('OPTION_ONE', 'OPTION_TWO');
        $attributeMetadata = new AttributeMetadata();
        $attributeMetadata->setAttributeCode(self::ATTRIBUTE_CODE)
            ->setFrontendInput(self::FRONT_END_INPUT)
            ->setInputFilter(self::INPUT_FILTER)
            ->setStoreLabel(self::STORE_LABEL)
            ->setValidationRules(self::VALIDATION_RULES)
            ->setOptions($options);

        $this->assertSame(self::ATTRIBUTE_CODE, $attributeMetadata->getAttributeCode());
        $this->assertSame(self::FRONT_END_INPUT, $attributeMetadata->getFrontendInput());
        $this->assertSame(self::INPUT_FILTER, $attributeMetadata->getInputFilter());
        $this->assertSame(self::STORE_LABEL, $attributeMetadata->getStoreLabel());
        $this->assertSame(self::VALIDATION_RULES, $attributeMetadata->getValidationRules());
        $this->assertSame($options, $attributeMetadata->getOptions());
    }
}
