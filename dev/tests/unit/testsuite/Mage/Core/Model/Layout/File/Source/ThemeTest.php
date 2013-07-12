<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Layout_File_Source_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_File_Source_Theme
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_filesystem;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_dirs;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_fileFactory;

    protected function setUp()
    {
        $this->_filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $this->_dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $this->_dirs->expects($this->any())->method('getDir')->will($this->returnArgument(0));
        $this->_fileFactory = $this->getMock('Mage_Core_Model_Layout_File_Factory', array(), array(), '', false);
        $this->_model = new Mage_Core_Model_Layout_File_Source_Theme(
            $this->_filesystem, $this->_dirs, $this->_fileFactory
        );
    }

    public function testGetFiles()
    {
        $theme = $this->getMockForAbstractClass('Mage_Core_Model_ThemeInterface');
        $theme->expects($this->once())->method('getArea')->will($this->returnValue('area'));
        $theme->expects($this->once())->method('getThemePath')->will($this->returnValue('theme/path'));

        $this->_filesystem
            ->expects($this->once())
            ->method('searchKeys')
            ->with('design', 'area/theme/path/*_*/layout/*.xml')
            ->will($this->returnValue(array(
                'design/area/theme/path/Module_One/layout/1.xml',
                'design/area/theme/path/Module_One/layout/2.xml',
                'design/area/theme/path/Module_Two/layout/3.xml',
            )))
        ;

        $fileOne = new Mage_Core_Model_Layout_File('1.xml', 'Module_One', $theme);
        $fileTwo = new Mage_Core_Model_Layout_File('2.xml', 'Module_One', $theme);
        $fileThree = new Mage_Core_Model_Layout_File('3.xml', 'Module_Two', $theme);
        $this->_fileFactory
            ->expects($this->at(0))
            ->method('create')
            ->with('design/area/theme/path/Module_One/layout/1.xml', 'Module_One', $theme)
            ->will($this->returnValue($fileOne))
        ;
        $this->_fileFactory
            ->expects($this->at(1))
            ->method('create')
            ->with('design/area/theme/path/Module_One/layout/2.xml', 'Module_One', $theme)
            ->will($this->returnValue($fileTwo))
        ;
        $this->_fileFactory
            ->expects($this->at(2))
            ->method('create')
            ->with('design/area/theme/path/Module_Two/layout/3.xml', 'Module_Two', $theme)
            ->will($this->returnValue($fileThree))
        ;

        $this->assertSame(array($fileOne, $fileTwo, $fileThree), $this->_model->getFiles($theme));
    }
}
