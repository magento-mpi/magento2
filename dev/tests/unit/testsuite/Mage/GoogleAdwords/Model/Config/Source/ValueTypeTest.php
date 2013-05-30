<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_GoogleAdwords_Model_Config_Source_ValueTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var Mage_GoogleAdwords_Model_Config_Source_ValueType
     */
    protected $_model;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Mage_GoogleAdwords_Helper_Data', array('__'), array(), '', false);
        $this->_helperMock->expects($this->atLeastOnce())->method('__')->with($this->isType('string'))
            ->will($this->returnArgument(0));

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('Mage_GoogleAdwords_Model_Config_Source_ValueType', array(
            'helper' => $this->_helperMock,
        ));
    }

    public function testToOptionArray()
    {
        $this->assertEquals(array(
            array(
                'value' => Mage_GoogleAdwords_Helper_Data::CONVERSION_VALUE_TYPE_DYNAMIC,
                'label' => 'Dynamic',
            ),
            array(
                'value' => Mage_GoogleAdwords_Helper_Data::CONVERSION_VALUE_TYPE_CONSTANT,
                'label' => 'Constant',
            ),
        ), $this->_model->toOptionArray());
    }
}
