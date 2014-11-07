<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data\Eav;

/**
 * Integration test for \Magento\Customer\Service\V1\Data\AttributeMetadataBuilder
 */
class AttributeMetadataBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    private $_objectManager;

    /**
     * AttributeMetadata builder
     *
     * @var AttributeMetadataBuilder
     */
    private $_builder;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_builder = $this->_objectManager->create(
            'Magento\Customer\Service\V1\Data\Eav\AttributeMetadataBuilder'
        );
    }

    public function testCreate()
    {
        $attributeMetadata = $this->_builder->create();
        $this->assertInstanceOf('\Magento\Customer\Service\V1\Data\Eav\AttributeMetadata', $attributeMetadata);
        $this->assertEquals(array(), $attributeMetadata->getOptions());
    }

    /**
     * @param $data
     * @param bool $expectOptions
     * @dataProvider populateWithArrayDataProvider
     */
    public function testPopulateWithArray($data, $expectOptions = false)
    {
        $attributeMetadata = $this->_builder->populateWithArray($data)->create();
        $attributeMetadataA = $this->_builder->populateWithArray($attributeMetadata->__toArray())->create();
        if ($expectOptions) {
            $this->assertInstanceOf(
                '\Magento\Customer\Service\V1\Data\Eav\Option',
                $attributeMetadata->getOptions()[0]
            );
        }
        $this->assertEquals($attributeMetadataA, $attributeMetadata);
    }

    /**
     * @param $data
     * @param bool $expectOptions
     * @dataProvider populateWithArrayDataProvider
     */
    public function testPopulate($data, $expectOptions = false)
    {
        $attributeMetadata = $this->_builder->populateWithArray($data)->create();
        $attributeMetadataA = $this->_builder->populate($attributeMetadata)->create();
        if ($expectOptions) {
            $this->assertInstanceOf(
                '\Magento\Customer\Service\V1\Data\Eav\Option',
                $attributeMetadataA->getOptions()[0]
            );
        }
        $this->assertEquals($attributeMetadata, $attributeMetadataA);
    }

    public function populateWithArrayDataProvider()
    {
        $dataNoOptions = array(
            'attribute_code' => 'prefix',
            'front_end_input' => 'text',
            'input_filter' => null,
            'store_label' => 'Prefix',
            'validation_rules' => array(),
            'visible' => '0',
            'options' => array()
        );

        $dataWithOptions = array(
            'attribute_code' => 'country_id',
            'front_end_input' => 'select',
            'input_filter' => null,
            'store_label' => 'Country',
            'validation_rules' => array(),
            'visible' => '1',
            'options' => array(
                array('label' => '', 'value' => ''),
                'Afghanistan' => array('label' => 'Afghanistan', 'value' => 'AF')
            )
        );


        return array(array(array()), array($dataNoOptions), array($dataWithOptions, true));
    }

    public function testMergeDataObjects()
    {
        $dataNoOptions = array(
            'attribute_code' => 'prefix',
            'front_end_input' => 'text',
            'input_filter' => null,
            'store_label' => 'Prefix',
            'validation_rules' => array(),
            'visible' => '0',
            'options' => array()
        );

        $dataWithOptions = array(
            'attribute_code' => 'country_id',
            'front_end_input' => 'select',
            'input_filter' => null,
            'store_label' => 'Country',
            'validation_rules' => array(),
            'visible' => '1',
            'options' => array(
                array('label' => '', 'value' => ''),
                'Afghanistan' => array('label' => 'Afghanistan', 'value' => 'AF')
            )
        );

        $attributeMetadata = $this->_builder->populateWithArray($dataNoOptions)->create();
        $attributeMetadataA = $this->_builder->populateWithArray($dataWithOptions)->create();
        $merged = $this->_builder->mergeDataObjects($attributeMetadata, $attributeMetadataA)
            ->create();
        $this->assertEquals($attributeMetadataA, $merged);
    }

    public function testMergeDataObjectWithArray()
    {
        $dataNoOptions = array(
            'attribute_code' => 'prefix',
            'front_end_input' => 'text',
            'input_filter' => null,
            'store_label' => 'Prefix',
            'validation_rules' => array(),
            'visible' => '0',
            'options' => array()
        );

        $dataWithOptions = array(
            'attribute_code' => 'country_id',
            'front_end_input' => 'select',
            'input_filter' => null,
            'store_label' => 'Country',
            'validation_rules' => array(),
            'visible' => '1',
            'options' => array(
                array('label' => '', 'value' => ''),
                'Afghanistan' => array('label' => 'Afghanistan', 'value' => 'AF')
            )
        );

        $attributeMetadata = $this->_builder->populateWithArray($dataNoOptions)->create();
        $attributeMetadataA = $this->_builder->populateWithArray($dataWithOptions)->create();
        $merged = $this->_builder->mergeDataObjectWithArray($attributeMetadata, $dataWithOptions)
            ->create();
        $this->assertEquals($attributeMetadataA, $merged);
    }
}
