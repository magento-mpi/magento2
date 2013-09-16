<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_JsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Theme id of virtual theme
     */
    const TEST_THEME_ID = 15;

    /**
     * @var Magento_Backend_Model_Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var Magento_DesignEditor_Model_Theme_Context|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_themeContext;

    /**
     * @var Magento_Core_Model_Theme|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_theme;

    /**
     * @var Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    public function setUp()
    {
        $this->_urlBuilder = $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false);
        $this->_themeContext = $this->getMock('Magento_DesignEditor_Model_Theme_Context', array(), array(), '', false);
        $this->_theme = $this->getMock('Magento_Core_Model_Theme', array('getId', 'getCustomization'), array(), '',
            false);
        $this->_theme->expects($this->any())->method('getId')->will($this->returnValue(self::TEST_THEME_ID));
        $this->_themeContext->expects($this->any())->method('getEditableTheme')
            ->will($this->returnValue($this->_theme));
        $this->_themeContext->expects($this->any())->method('getStagingTheme')
            ->will($this->returnValue($this->_theme));

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $constructArguments = $objectManagerHelper->getConstructArguments(
            'Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js',
            array(
                'urlBuilder' => $this->_urlBuilder,
                'themeContext' => $this->_themeContext,
                'formFactory' => $this->getMock('Magento_Data_Form_Factory', array(), array(), '', false),
        ));
        $this->_model = $this->getMock(
            'Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js',
            array('helper'),
            $constructArguments
        );
    }

    public function tearDown()
    {
        $this->_model = null;
        $this->_urlBuilder = null;
        $this->_themeContext = null;
        $this->_theme = null;
    }

    /**
     * @covers Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js::getJsUploadUrl
     */
    public function testGetDownloadCustomCssUrl()
    {
        $expectedUrl = 'some_url';
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/system_design_editor_tools/uploadjs', array('theme_id' => self::TEST_THEME_ID))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_model->getJsUploadUrl());
    }

    /**
     * @covers Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js::getJsReorderUrl
     */
    public function testGetJsReorderUrl()
    {
        $expectedUrl = 'some_url';
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/system_design_editor_tools/reorderjs', array('theme_id' => self::TEST_THEME_ID))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_model->getJsReorderUrl());
    }

    /**
     * @covers Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js::getTitle
     */
    public function testGetTitle()
    {
        $this->assertEquals('Custom javascript files', $this->_model->getTitle());
    }

    /**
     * @covers Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js::getFiles
     */
    public function testGetJsFiles()
    {
        $customization = $this->getMock('Magento_Core_Model_Theme_Customization', array(), array(), '', false);
        $this->_theme->expects($this->any())->method('getCustomization')->will($this->returnValue($customization));

        $customization->expects($this->once())
            ->method('getFilesByType')
            ->with(Magento_Core_Model_Theme_Customization_File_Js::TYPE)
            ->will($this->returnValue(array()));
        $helperMock = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $this->_model->expects($this->once())->method('helper')->with('Magento_Core_Helper_Data')
            ->will($this->returnValue($helperMock));

        $this->_model->getFiles();
    }
}
