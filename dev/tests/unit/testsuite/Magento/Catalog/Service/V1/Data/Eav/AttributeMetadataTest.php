<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder;

class AttributeMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Constants for testing
     */
    const ATTRIBUTE_ID = 'ATTRIBUTE_ID';

    const ATTRIBUTE_CODE = 'ATTRIBUTE_CODE';

    const FRONTEND_INPUT = 'FRONT_END_INPUT';

    const STORE_LABEL = 'STORE_LABEL';

    const VALIDATION_RULES = 'VALIDATION_RULES';

    const APPLY_TO = 'APPLY_TO';

    /**
     * Instance of AttributeMetadataBuilder
     *
     * @var AttributeMetadataBuilder
     */
    protected $attributeBuilder;

    /**
     * Create AttributeMetadataBuilder
     */
    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Catalog\Service\V1\Data\Eav\OptionBuilder $optionBuilder */
        $optionBuilder = $objectManager->getObject('Magento\Catalog\Service\V1\Data\Eav\OptionBuilder');
        /** @var \Magento\Catalog\Service\V1\Data\Eav\ValidationRuleBuilder $validationRuleBuilder */
        $validationRuleBuilder = $objectManager->getObject(
            'Magento\Catalog\Service\V1\Data\Eav\ValidationRuleBuilder'
        );

        $this->attributeBuilder = (new AttributeMetadataBuilder($optionBuilder, $validationRuleBuilder));
    }

    public function testConstructorAndGetters()
    {
        $options = array(array('value' => 'OPTION_ONE'), array('value' => 'OPTION_TWO'));
        $this->attributeBuilder->populateWithArray(
            array(
                'attribute_id' => self::ATTRIBUTE_ID,
                'attribute_code' => self::ATTRIBUTE_CODE,
                'frontend_input' => self::FRONTEND_INPUT,
                'store_label' => self::STORE_LABEL,
                'validation_rules' => array(),
                'options' => $options
            )
        );
        $attributeMetadata = new AttributeMetadata($this->attributeBuilder);

        $this->assertSame(self::ATTRIBUTE_CODE, $attributeMetadata->getAttributeCode());
        $this->assertSame(self::FRONTEND_INPUT, $attributeMetadata->getFrontendInput());
        $this->assertSame(self::ATTRIBUTE_ID, $attributeMetadata->getAttributeId());
        $this->assertSame(self::STORE_LABEL, $attributeMetadata->getStoreLabel());
        $this->assertSame(array(), $attributeMetadata->getValidationRules());
        $this->assertSame($options[0], $attributeMetadata->getOptions()[0]->__toArray());
        $this->assertSame($options[1], $attributeMetadata->getOptions()[1]->__toArray());
    }

    /**
     * Test applyTy method of builder
     *
     * ApplyTo method transform string to array
     *
     * @dataProvider applyToDataProvider()
     *
     * @param $applyTo
     */
    public function testApplyTo($applyTo)
    {
        $this->attributeBuilder->populateWithArray(array(
            'apply_to' => $applyTo
        ));

        $attributeMetadata = new AttributeMetadata($this->attributeBuilder);
        $this->assertTrue(is_array($attributeMetadata->getApplyTo()));

        $this->attributeBuilder->setApplyTo($applyTo);
        $attributeMetadata = new AttributeMetadata($this->attributeBuilder);
        $this->assertTrue(is_array($attributeMetadata->getApplyTo()));
    }

    public function applyToDataProvider()
    {
        return array(array(
            'simple,virtual,bundle',
            array('simple', 'virtual', 'bundle')
        ));
    }
}
