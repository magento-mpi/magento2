<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Dto\Eav;

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

        $attributeMetadata = new \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata([
            'attribute_code' => self::ATTRIBUTE_CODE,
            'front_end_input' => self::FRONT_END_INPUT,
            'input_filter' => self::INPUT_FILTER,
            'store_label' => self::STORE_LABEL,
            'validation_rules' => self::VALIDATION_RULES,
            'options' => $options
        ]);

        $this->assertSame(self::ATTRIBUTE_CODE, $attributeMetadata->getAttributeCode());
        $this->assertSame(self::FRONT_END_INPUT, $attributeMetadata->getFrontendInput());
        $this->assertSame(self::INPUT_FILTER, $attributeMetadata->getInputFilter());
        $this->assertSame(self::STORE_LABEL, $attributeMetadata->getStoreLabel());
        $this->assertSame(self::VALIDATION_RULES, $attributeMetadata->getValidationRules());
        $this->assertSame($options, $attributeMetadata->getOptions());
    }
}
