<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data\Eav;


class AttributeMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Constants for testing
     */
    const ATTRIBUTE_CODE = 'ATTRIBUTE_CODE';

    const FRONTEND_INPUT = 'FRONT_END_INPUT';

    const INPUT_FILTER = 'INPUT_FILTER';

    const STORE_LABEL = 'STORE_LABEL';

    const VALIDATION_RULES = 'VALIDATION_RULES';

    public function testConstructorAndGetters()
    {
        $options = [['value' => 'OPTION_ONE'], ['value' => 'OPTION_TWO']];
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Customer\Service\V1\Data\Eav\OptionBuilder $optionBuilder */
        $optionBuilder = $objectManager->getObject('Magento\Customer\Service\V1\Data\Eav\OptionBuilder');
        $validationRuleBuilder = $objectManager->getObject(
            'Magento\Customer\Service\V1\Data\Eav\ValidationRuleBuilder'
        );

        $attributeMetadataBuilder = $objectManager->getObject(
            '\Magento\Customer\Service\V1\Data\Eav\AttributeMetadataBuilder',
            ['optionBuilder' => $optionBuilder, 'validationRuleBuilder' => $validationRuleBuilder]
        )->populateWithArray(
            [
                'attribute_code' => self::ATTRIBUTE_CODE,
                'frontend_input' => self::FRONTEND_INPUT,
                'input_filter' => self::INPUT_FILTER,
                'store_label' => self::STORE_LABEL,
                'validation_rules' => [],
                'options' => $options,
            ]
        );
        $attributeMetadata = new AttributeMetadata($attributeMetadataBuilder);

        $this->assertSame(self::ATTRIBUTE_CODE, $attributeMetadata->getAttributeCode());
        $this->assertSame(self::FRONTEND_INPUT, $attributeMetadata->getFrontendInput());
        $this->assertSame(self::INPUT_FILTER, $attributeMetadata->getInputFilter());
        $this->assertSame(self::STORE_LABEL, $attributeMetadata->getStoreLabel());
        $this->assertSame([], $attributeMetadata->getValidationRules());
        $this->assertSame($options[0], $attributeMetadata->getOptions()[0]->__toArray());
        $this->assertSame($options[1], $attributeMetadata->getOptions()[1]->__toArray());
    }
}
