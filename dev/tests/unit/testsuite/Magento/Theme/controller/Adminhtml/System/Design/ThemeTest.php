<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test backend controller for the theme
 */
class Magento_Theme_Controller_Adminhtml_System_Design_ThemeControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Theme_Controller_Adminhtml_System_Design_Theme
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);

        $this->_request = $this->getMock(
            'Magento_Core_Controller_Request_Http', array('getParam', 'getPost'), array(), '', false
        );

        $helper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = array(
            'request' => $this->_request,
            'objectManager' => $this->_objectManagerMock,

        );
        $context = $helper->getObject('Magento_Backend_Controller_Context', $arguments);

        $this->_model = $this->getMock('Magento_Theme_Controller_Adminhtml_System_Design_Theme',
            array('_forward', '_title', 'loadLayout', 'renderLayout', '_redirect'),
            array($context, null)
        );
        $this->_model->expects($this->any())->method('_title')->will($this->returnValue($this->_model));
        $this->_model->expects($this->any())->method('loadLayout');
        $this->_model->expects($this->any())->method('renderLayout');
    }

    /**
     * @covers Magento_Theme_Controller_Adminhtml_System_Design_Theme::saveAction
     */
    public function testSaveAction()
    {
        $themeData = array('theme_id' => 123);
        $customCssContent = 'custom css content';
        $jsRemovedFiles = array(3, 4);
        $jsOrder = array(1 => '1', 2 => 'test');

        $this->_request->expects($this->at(0))->method('getParam')->with('back', false)
            ->will($this->returnValue(true));

        $this->_request->expects($this->at(1))->method('getParam')->with('theme')
            ->will($this->returnValue($themeData));
        $this->_request->expects($this->at(2))->method('getParam')->with('custom_css_content')
            ->will($this->returnValue($customCssContent));
        $this->_request->expects($this->at(3))->method('getParam')->with('js_removed_files')
            ->will($this->returnValue($jsRemovedFiles));
        $this->_request->expects($this->at(4))->method('getParam')->with('js_order')
            ->will($this->returnValue($jsOrder));
        $this->_request->expects($this->once(5))->method('getPost')->will($this->returnValue(true));

        $themeMock = $this->getMock('Magento_Core_Model_Theme',
            array('save', 'load', 'setCustomization', 'getThemeImage'), array(), '', false);

        $themeImage = $this->getMock('Magento_Core_Model_Theme_Image', array(), array(), '', false);
        $themeMock->expects($this->any())->method('getThemeImage')->will($this->returnValue($themeImage));

        $themeFactory = $this->getMock('Magento_Core_Model_Theme_FlyweightFactory', array('create'), array(), '',
            false);
        $themeFactory->expects($this->once())->method('create')->will($this->returnValue($themeMock));

        $this->_objectManagerMock
            ->expects($this->at(0))
            ->method('get')
            ->with('Magento_Core_Model_Theme_FlyweightFactory')
            ->will($this->returnValue($themeFactory));

        $this->_objectManagerMock
            ->expects($this->at(1))
            ->method('get')
            ->with('Magento_Theme_Model_Theme_Customization_File_CustomCss')
            ->will($this->returnValue(null));

        $this->_objectManagerMock
            ->expects($this->at(2))
            ->method('create')
            ->with('Magento_Theme_Model_Theme_SingleFile')
            ->will($this->returnValue(null));

        $this->_model->saveAction();
    }

}
