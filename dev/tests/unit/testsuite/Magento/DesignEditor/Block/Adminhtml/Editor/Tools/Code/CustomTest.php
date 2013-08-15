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

class Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_CustomTest extends PHPUnit_Framework_TestCase
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
     * @var Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    public function setUp()
    {
        $this->_urlBuilder = $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false);
        $this->_themeContext = $this->getMock('Magento_DesignEditor_Model_Theme_Context', array(), array(), '', false);
        $this->_theme = $this->getMock('Magento_Core_Model_Theme', array('getId', 'getCustomization'), array(),
            '', false);
        $this->_theme->expects($this->any())->method('getId')->will($this->returnValue(self::TEST_THEME_ID));
        $this->_themeContext->expects($this->any())->method('getEditableTheme')
            ->will($this->returnValue($this->_theme));
        $this->_themeContext->expects($this->any())->method('getStagingTheme')
            ->will($this->returnValue($this->_theme));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            'Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom',
            array(
                'config'       => $this->getMock('Magento_Core_Model_Config', array(), array(), '', false),
                'urlBuilder'   => $this->_urlBuilder,
                'themeContext' => $this->_themeContext
        ));
    }

    public function tearDown()
    {
        $this->_model = null;
        $this->_urlBuilder = null;
        $this->_themeContext = null;
        $this->_theme = null;
    }

    /**
     * @covers Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Custom::getDownloadCustomCssUrl
     */
    public function testGetDownloadCustomCssUrl()
    {
        $expectedUrl = 'some_url';

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/system_design_theme/downloadCustomCss', array('theme_id' => self::TEST_THEME_ID))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_model->getDownloadCustomCssUrl());
    }

    public function testGetSaveCustomCssUrl()
    {
        $expectedUrl = 'some_url';

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/system_design_editor_tools/saveCssContent', array('theme_id' => self::TEST_THEME_ID))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_model->getSaveCustomCssUrl());
    }

    public function testGetCustomCssContent()
    {
        $expectedContent = 'New file content';

        $customization = $this->getMock('Magento_Core_Model_Theme_Customization', array(), array(), '', false);
        $this->_theme->expects($this->any())->method('getCustomization')->will($this->returnValue($customization));

        /** @var $cssFile Magento_Core_Model_Theme_Customization_File_Css */
        $cssFile = $this->getMock(
            'Magento_Core_Model_Theme_Customization_File', array('getContent'), array(), '', false
        );

        $customization->expects($this->once())
            ->method('getFilesByType')
            ->with(Magento_Theme_Model_Theme_Customization_File_CustomCss::TYPE)
            ->will($this->returnValue(array($cssFile)));

        $cssFile->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('New file content'));

        $this->assertEquals($expectedContent, $this->_model->getCustomCssContent());
    }
}
