<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_System_Config_EditTest extends Magento_Test_TestCase_ObjectManagerAbstract
{
    /**
     * @var Mage_Backend_Block_System_Config_Edit
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_systemConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    protected function setUp()
    {
        $this->_systemConfigMock = $this->getMock('Mage_Backend_Model_Config_Structure',
            array(), array(), '', false, false
        );

        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http',
            array(), array(), '', false, false
        );
        $this->_requestMock->expects($this->once())
            ->method('getParam')
            ->with('section')
            ->will($this->returnValue('test_section'));

        $this->_layoutMock = $this->getMock('Mage_Core_Model_Layout',
            array(), array(), '', false, false
        );

        $this->_urlModelMock = $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false, false);

        $sections = array(
            'frontend_model' => 'Some_Frontend_Model',
            'label' => 'test_label'
        );
        $this->_systemConfigMock->expects($this->once())
            ->method('getSection')
            ->with('test_section')
            ->will($this->returnValue($sections)
        );

        $data = array(
            'data' => array(
                'systemConfig' => $this->_systemConfigMock,
            ),
            'request' => $this->_requestMock,
            'layout' => $this->_layoutMock,
            'urlBuilder' => $this->_urlModelMock
        );

        $this->_object = $this->getBlock('Mage_Backend_Block_System_Config_Edit', $data);
    }

    public function testGetSaveButtonHtml()
    {
        $expected = 'element_html_code';

        $this->_layoutMock->expects($this->once())->method('getChildName')
            ->with(null, 'save_button')
            ->will($this->returnValue('test_child_name'));

        $this->_layoutMock->expects($this->once())->method('renderElement')
            ->with('test_child_name')->will($this->returnValue('element_html_code'));

        $this->assertEquals($expected, $this->_object->getSaveButtonHtml());
    }

    public function testGetSaveUrl()
    {
        $expectedUrl = '*/*/save';
        $expectedParams = array('_current' => true);

        $this->_urlModelMock->expects($this->once())
            ->method('getUrl')
            ->with($expectedUrl, $expectedParams)
            ->will($this->returnArgument(0)
        );

        $this->assertEquals($expectedUrl, $this->_object->getSaveUrl());
    }

    public function testInitFormWhenFrontendModelIsSet()
    {
        $block = $this->getMock('Mage_Core_Block_Template',
            array('initForm', 'getNameInLayout', 'getIsAnonymous'), array(), '', false, false
        );
        $block->expects($this->once())->method('initForm');
        $block->expects($this->once())->method('getNameInLayout')->will($this->returnValue('test_block_name'));
        $block->expects($this->once())->method('getIsAnonymous')->will($this->returnValue(false));

        $this->_layoutMock->expects($this->once())
            ->method('createBlock')
            ->with('Some_Frontend_Model')
            ->will($this->returnValue($block)
        );

        $this->_layoutMock->expects($this->once())->method('setChild')->with(null, 'test_block_name', 'form');
        $this->_object->initForm();
    }
}
