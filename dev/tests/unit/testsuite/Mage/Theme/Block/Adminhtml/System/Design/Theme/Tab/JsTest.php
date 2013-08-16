<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Theme_Block_Adminhtml_System_Design_Theme_Tab_JsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Js
     */
    protected $_model;

    /**
     * @var Mage_Backend_Model_Url
     */
    protected $_urlBuilder;

    protected function setUp()
    {
        $this->_urlBuilder = $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $constructArguments = $objectManagerHelper->getConstructArguments(
            'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Js',
            array('urlBuilder' => $this->_urlBuilder)
        );

        $this->_model = $this->getMock(
            'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Js',
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
        $class = new ReflectionClass('Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Js');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    public function testGetAdditionalElementTypes()
    {
        $method = $this->_getMethod('_getAdditionalElementTypes');
        $result = $method->invokeArgs($this->_model, array());
        $expectedResult = array(
            'js_files' => 'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File'
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
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('isVirtual', 'getId'), array(), '', false);
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
