<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_JsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Url|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    public function setUp()
    {
        $this->_urlBuilder = $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $constructArguments = $objectManagerHelper->getConstructArguments(
            'Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js',
            array(
                 'config'     => $this->getMock('Mage_Core_Model_Config', array(), array(), '', false),
                 'service'    => $this->getMock('Mage_Core_Model_Theme_Service', array(), array(), '', false),
                 'urlBuilder' => $this->_urlBuilder
        ));
        $this->_model = $this->getMock(
            'Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js',
            array('__'),
            $constructArguments
        );
    }

    public function tearDown()
    {
        $this->_model = null;
        $this->_urlBuilder = null;
    }

    /**
     * @covers Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js::getJsUploadUrl
     */
    public function testGetDownloadCustomCssUrl()
    {
        $themeId = 15;
        $theme = $this->getMockBuilder('Mage_Core_Model_Theme')->disableOriginalConstructor()->getMock();
        $theme->expects($this->once())->method('getId')->will($this->returnValue($themeId));

        $this->_model->setTheme($theme);
        $expectedUrl = 'some_url';

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/system_design_editor_tools/uploadjs', array('theme_id' => $themeId))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_model->getJsUploadUrl());
    }

    /**
     * @covers Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js::getJsReorderUrl
     */
    public function testGetJsReorderUrl()
    {
        $themeId = 8;
        $theme = $this->getMockBuilder('Mage_Core_Model_Theme')->disableOriginalConstructor()->getMock();
        $theme->expects($this->once())->method('getId')->will($this->returnValue($themeId));
        $this->_model->setTheme($theme);

        $expectedUrl = 'some_url';
        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/system_design_editor_tools/reorderjs', array('theme_id' => $themeId))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_model->getJsReorderUrl());
    }

    /**
     * @covers Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js::getTitle
     */
    public function testGetTitle()
    {
        $this->_model->expects($this->atLeastOnce())
            ->method('__')
            ->will($this->returnArgument(0));
        $this->assertEquals('Custom javascript files', $this->_model->getTitle());
    }

    /**
     * @covers Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js::getJsFiles
     */
    public function testGetJsFiles()
    {
        $filesCollection = $this->getMockBuilder('Mage_Core_Model_Resource_Theme_File_Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $theme = $this->getMockBuilder('Mage_Core_Model_Theme')
            ->disableOriginalConstructor()
            ->setMethods(array('getCustomizationData'))
            ->getMock();

        $theme->expects($this->once())
            ->method('getCustomizationData')
            ->with(Mage_Core_Model_Theme_Customization_Files_Js::TYPE)
            ->will($this->returnValue($filesCollection));

        $this->_model->setTheme($theme);
        $this->assertEquals($filesCollection, $this->_model->getJsFiles());
    }
}
