<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\File\Source\Decorator;

class ModuleOutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\File\Source\Decorator\ModuleOutput
     */
    private $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_fileSource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_moduleManager;

    protected function setUp()
    {
        $this->_fileSource = $this->getMockForAbstractClass('Magento\View\File\SourceInterface');
        $this->_moduleManager = $this->getMock('Magento\Module\Manager', array(), array(), '', false);
        $this->_moduleManager
            ->expects($this->any())
            ->method('isOutputEnabled')
            ->will($this->returnValueMap(array(
                array('Module_OutputEnabled', true),
                array('Module_OutputDisabled', false),
            )))
        ;
        $this->_model = new \Magento\View\File\Source\Decorator\ModuleOutput(
            $this->_fileSource, $this->_moduleManager
        );
    }

    public function testGetFiles()
    {
        $theme = $this->getMockForAbstractClass('Magento\View\Design\ThemeInterface');
        $fileOne = new \Magento\View\File('1.xml', 'Module_OutputEnabled');
        $fileTwo = new \Magento\View\File('2.xml', 'Module_OutputDisabled');
        $fileThree = new \Magento\View\File('3.xml', 'Module_OutputEnabled', $theme);
        $this->_fileSource
            ->expects($this->once())
            ->method('getFiles')
            ->with($theme)
            ->will($this->returnValue(array($fileOne, $fileTwo, $fileThree)))
        ;
        $this->assertSame(array($fileOne, $fileThree), $this->_model->getFiles($theme));
    }
}
