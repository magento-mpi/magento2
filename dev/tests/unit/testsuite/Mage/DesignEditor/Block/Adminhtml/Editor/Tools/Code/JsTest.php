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
        $this->_model = $objectManagerHelper->getBlock(
            'Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js',
            array(
                 'config' => $this->getMock('Mage_Core_Model_Config', array(), array(), '', false),
                 'urlBuilder' => $this->_urlBuilder
            ));
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
        $theme = $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false);
        $theme->expects($this->once())->method('getId')->will($this->returnValue($themeId));

        $this->_model->setTheme($theme);
        $expectedUrl = 'some_url';

        $this->_urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('*/system_design_editor_tools/uploadjs', array('id' => $themeId))
            ->will($this->returnValue($expectedUrl));

        $this->assertEquals($expectedUrl, $this->_model->getJsUploadUrl());
    }
}
