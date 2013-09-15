<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ImportExport\Model\Source\Import\BehaviorAbstract
 */
class Magento_ImportExport_Model_Source_Import_BehaviorAbstractTest
    extends Magento_ImportExport_Model_Source_Import_BehaviorTestCaseAbstract
{
    /**
     * Source array data
     *
     * @var array
     */
    protected $_sourceArray = array(
        'key_1' => 'label_1',
        'key_2' => 'label_2',
    );

    /**
     * Expected options (without first empty record)
     *
     * @var array
     */
    protected $_expectedOptions = array(
        array(
            'value' => 'key_1',
            'label' => 'label_1',
        ),
        array(
            'value' => 'key_2',
            'label' => 'label_2',
        ),
    );

    public function setUp()
    {
        parent::setUp();

        $model = $this->getMockForAbstractClass(
            'Magento\ImportExport\Model\Source\Import\BehaviorAbstract',
            array(array()),
            '',
            false,
            true,
            true,
            array('toArray')
        );
        $model->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue($this->_sourceArray));

        $this->_model = $model;
    }

    /**
     * Test for toOptionArray method
     *
     * @covers \Magento\ImportExport\Model\Source\Import\BehaviorAbstract::toOptionArray
     */
    public function testToOptionArray()
    {
        $actualOptions = $this->_model->toOptionArray();

        // all elements must have value and label fields
        foreach ($actualOptions as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
        }

        // first element must has empty value
        $firstElement = $actualOptions[0];
        $this->assertEquals('', $firstElement['value']);

        // other elements must be equal to expected data
        $actualOptions = array_slice($actualOptions, 1);
        $this->assertEquals($this->_expectedOptions, $actualOptions);
    }
}
