<?php
/**
 * Test Enterprise_Logging_Model_Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Logging_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Logging_Model_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Logging_Model_Config
     */
    protected $_model;

    public function setUp()
    {
        $this->_storageMock = $this->getMock('Magento_Logging_Model_Config_Data', array('get'), array(), '', false);
        $loggingConfig = array(
            'test' => array(
                'label' => 'Test Label'
            ),
            'actions' => array(
                'test_action_withlabel' => array(
                    'label' => 'Test Action Label'
                ),
                'test_action_withoutlabel' => array()
            )
        );
        $this->_storageMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('logging'))
            ->will($this->returnValue($loggingConfig));
        $this->_model = new Magento_Logging_Model_Config($this->_storageMock);
    }

    public function testLabels()
    {
        $expected = array('test' => 'Test Label');
        $result = $this->_model->getLabels();
        $this->assertEquals($expected, $result);
    }

    public function testGetActionLabel()
    {
        $expected = 'Test Action Label';
        $result = $this->_model->getActionLabel('test_action_withlabel');
        $this->assertEquals($expected, $result);
    }

    public function testGetActionWithoutLabel()
    {
        $expected = 'test_action_withoutlabel';
        $result = $this->_model->getActionLabel('test_action_withoutlabel');
        $this->assertEquals($expected, $result);
    }
}