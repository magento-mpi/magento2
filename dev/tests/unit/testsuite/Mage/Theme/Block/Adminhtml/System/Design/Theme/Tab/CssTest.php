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

class Mage_Theme_Block_Adminhtml_System_Design_Theme_Tab_CssTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_model = $this->getMock(
            'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css',
            array('_getCurrentTheme'),
            $this->_prepareModelArguments(),
            '',
            true
        );
    }

    /**
     * @return array
     */
    protected function _prepareModelArguments()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $constructArguments = $objectManagerHelper->getConstructArguments(
            'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css',
            array(
                 'objectManager'   => $this->_objectManager,
                 'dirs'            => new Mage_Core_Model_Dir(__DIR__),
                 'uploaderService' => $this->getMock('Mage_Theme_Model_Uploader_Service', array(), array(), '', false),
                 'urlBuilder'      => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false)
            )
        );
        return $constructArguments;
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testGetUploadCssFileNote()
    {
        $method = self::getMethod('_getUploadCssFileNote');
        /** @var $sizeModel Magento_File_Size */
        $sizeModel = $this->getMock('Magento_File_Size', null, array(), '', false);

        $this->_objectManager->expects($this->any())
            ->method('get')
            ->with('Magento_File_Size')
            ->will($this->returnValue($sizeModel));

        $result = $method->invokeArgs($this->_model, array());
        $expectedResult = 'Allowed file types *.css.<br />';
        $expectedResult .= 'The file you upload will replace the existing custom.css file (shown below).<br />';
        $expectedResult .= sprintf(
            'Max file size to upload %sM',
            $sizeModel->getMaxFileSizeInMb()
        );
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetAdditionalElementTypes()
    {
        $method = self::getMethod('_getAdditionalElementTypes');

        /** @var $configModel Mage_Core_Model_Config */
        $configModel = $this->getMock('Mage_Core_Model_Config', null, array(), '', false);

        $this->_objectManager->expects($this->any())
            ->method('get')
            ->with('Mage_Core_Model_Config')
            ->will($this->returnValue($configModel));

        $result = $method->invokeArgs($this->_model, array());
        $expectedResult = array(
            'links' => 'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_Links',
            'css_file' => 'Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form_Element_File'
        );
        $this->assertEquals($expectedResult, $result);
    }

    public function testGetTabLabel()
    {
        $this->assertEquals('CSS Editor', $this->_model->getTabLabel());
    }

    /**
     * @param string $name
     * @return ReflectionMethod
     */
    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Mage_Theme_Block_Adminhtml_System_Design_Theme_Edit_Tab_Css');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}
