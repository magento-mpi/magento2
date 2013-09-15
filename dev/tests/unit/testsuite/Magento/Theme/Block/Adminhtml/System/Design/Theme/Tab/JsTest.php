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

class Magento_Theme_Block_Adminhtml_System_Design_Theme_Tab_JsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Js
     */
    protected $_model;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $_urlBuilder;

    protected function setUp()
    {
        $this->_urlBuilder = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false);

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $constructArguments = $objectManagerHelper->getConstructArguments(
            'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Js',
            array(
                 'formFactory' => $this->getMock('Magento_Data_Form_Factory', array(), array(), '', false),
                 'objectManager' => $this->getMock('Magento_ObjectManager', array(), array(), '', false),
                 'urlBuilder'    => $this->_urlBuilder
            )
        );

        $this->_model = $this->getMock(
            'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Js',
            array('_getCurrentTheme'),
            $constructArguments,
            '',
            true
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @param string $name
     * @return ReflectionMethod
     */
    protected function _getMethod($name)
    {
        $class = new ReflectionClass('Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Js');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    public function testGetAdditionalElementTypes()
    {
        $method = $this->_getMethod('_getAdditionalElementTypes');
        $result = $method->invokeArgs($this->_model, array());
        $expectedResult = array(
            'js_files' => 'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element\File'
        );
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetTabLabel()
    {
        $this->assertEquals('JS Editor', $this->_model->getTabLabel());
    }

    public function testGetJsUploadUrl()
    {
        $themeId = 2;
        $uploadUrl = 'upload_url';
        $themeMock = $this->getMock('Magento\Core\Model\Theme', array('isVirtual', 'getId'), array(), '', false);
        $themeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($themeId));

        $this->_model->expects($this->any())
            ->method('_getCurrentTheme')
            ->will($this->returnValue($themeMock));

        $this->_urlBuilder
            ->expects($this->once())
            ->method('getUrl')
            ->with('*/system_design_theme/uploadjs', array('id' => $themeId))
            ->will($this->returnValue($uploadUrl));

        $this->assertEquals($uploadUrl, $this->_model->getJsUploadUrl());
    }

    public function testGetUploadJsFileNote()
    {
        $method = $this->_getMethod('_getUploadJsFileNote');
        $result = $method->invokeArgs($this->_model, array());
        $this->assertEquals('Allowed file types *.js.', $result);
    }
}
