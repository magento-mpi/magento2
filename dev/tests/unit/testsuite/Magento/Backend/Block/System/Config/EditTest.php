<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Block_System_Config_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\System\Config\Edit
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

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sectionMock;

    protected function setUp()
    {
        $this->_systemConfigMock = $this->getMock('Magento\Backend\Model\Config\Structure',
            array(), array(), '', false, false
        );

        $this->_requestMock = $this->getMock('Magento\Core\Controller\Request\Http',
            array(), array(), '', false, false
        );
        $this->_requestMock->expects($this->any())
            ->method('getParam')
            ->with('section')
            ->will($this->returnValue('test_section'));

        $this->_layoutMock = $this->getMock('Magento\Core\Model\Layout',
            array(), array(), '', false, false
        );

        $this->_urlModelMock = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false, false);

        $this->_sectionMock = $this->getMock(
            '\Magento\Backend\Model\Config\Structure\Element\Section', array(), array(), '', false
        );
        $this->_systemConfigMock->expects($this->any())
            ->method('getElement')
            ->with('test_section')
            ->will($this->returnValue($this->_sectionMock));

        $data = array(
            'data' => array(
                'systemConfig' => $this->_systemConfigMock,
            ),
            'request' => $this->_requestMock,
            'layout' => $this->_layoutMock,
            'urlBuilder' => $this->_urlModelMock,
            'configStructure' => $this->_systemConfigMock
        );

        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_object = $helper->getObject('\Magento\Backend\Block\System\Config\Edit', $data);
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
        $expectedUrl = '*/system_config_save/index';
        $expectedParams = array('_current' => true);

        $this->_urlModelMock->expects($this->once())
            ->method('getUrl')
            ->with($expectedUrl, $expectedParams)
            ->will($this->returnArgument(0)
        );

        $this->assertEquals($expectedUrl, $this->_object->getSaveUrl());
    }
}
