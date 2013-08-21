<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_File_Source_Decorator_ModuleOutputTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_File_Source_Decorator_ModuleOutput
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_fileSource;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_moduleManager;

    protected function setUp()
    {
        $this->_fileSource = $this->getMockForAbstractClass('Magento_Core_Model_Layout_File_SourceInterface');
        $this->_moduleManager = $this->getMock('Magento_Core_Model_ModuleManager', array(), array(), '', false);
        $this->_moduleManager
            ->expects($this->any())
            ->method('isOutputEnabled')
            ->will($this->returnValueMap(array(
                array('Module_OutputEnabled', true),
                array('Module_OutputDisabled', false),
            )))
        ;
        $this->_model = new Magento_Core_Model_Layout_File_Source_Decorator_ModuleOutput(
            $this->_fileSource, $this->_moduleManager
        );
    }

    public function testGetFiles()
    {
        $theme = $this->getMockForAbstractClass('Magento_Core_Model_ThemeInterface');
        $fileOne = new Magento_Core_Model_Layout_File('1.xml', 'Module_OutputEnabled');
        $fileTwo = new Magento_Core_Model_Layout_File('2.xml', 'Module_OutputDisabled');
        $fileThree = new Magento_Core_Model_Layout_File('3.xml', 'Module_OutputEnabled', $theme);
        $this->_fileSource
            ->expects($this->once())
            ->method('getFiles')
            ->with($theme)
            ->will($this->returnValue(array($fileOne, $fileTwo, $fileThree)))
        ;
        $this->assertSame(array($fileOne, $fileThree), $this->_model->getFiles($theme));
    }
}
