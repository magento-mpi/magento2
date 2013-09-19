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
 * Test class for entity source model Magento_ImportExport_Model_Source_Import_Entity
 */
class Magento_ImportExport_Model_Source_Import_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ImportExport_Model_Source_Import_Entity
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_importConfigMock;

    protected function setUp()
    {
        $this->_importConfigMock = $this->getMock('Magento_ImportExport_Model_Import_ConfigInterface');
        $this->_model = new Magento_ImportExport_Model_Source_Import_Entity(
            $this->_importConfigMock
        );
    }

    public function testToOptionArray()
    {
        $entities = array(
            'entity_name_1' => array(
                'name' => 'entity_name_1',
                'label' => 'entity_label_1',
            ),
            'entity_name_2' => array(
                'name' => 'entity_name_2',
                'label' => 'entity_label_2',
            ),
        );
        $expectedResult = array(
            array(
                'label' => __('-- Please Select --'),
                'value' => '',
            ),
            array(
                'label' => __('entity_label_1'),
                'value' => 'entity_name_1',
            ),
            array(
                'label' => __('entity_label_2'),
                'value' => 'entity_name_2',
            ),

        );
        $this->_importConfigMock->expects($this->any())
            ->method('getEntities')
            ->will($this->returnValue($entities));
        $this->assertEquals($expectedResult, $this->_model->toOptionArray());
    }
}
