<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_File_Source_AggregateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_File_Source_Aggregated
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_fileList;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_baseFiles;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_themeFiles;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_overridingBaseFiles;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_overridingThemeFiles;

    protected function setUp()
    {
        $this->_fileList = $this->getMock('Magento_Core_Model_Layout_File_List', array(), array(), '', false);
        $this->_baseFiles = $this->getMockForAbstractClass('Magento_Core_Model_Layout_File_SourceInterface');
        $this->_themeFiles = $this->getMockForAbstractClass('Magento_Core_Model_Layout_File_SourceInterface');
        $this->_overridingBaseFiles = $this->getMockForAbstractClass('Magento_Core_Model_Layout_File_SourceInterface');
        $this->_overridingThemeFiles = $this->getMockForAbstractClass('Magento_Core_Model_Layout_File_SourceInterface');
        $fileListFactory = $this->getMock('Magento_Core_Model_Layout_File_FileList_Factory', array(), array(), '', false);
        $fileListFactory->expects($this->once())->method('create')->will($this->returnValue($this->_fileList));
        $this->_model = new Magento_Core_Model_Layout_File_Source_Aggregated(
            $fileListFactory, $this->_baseFiles, $this->_themeFiles,
            $this->_overridingBaseFiles, $this->_overridingThemeFiles
        );
    }

    public function testGetFiles()
    {
        $parentTheme = $this->getMockForAbstractClass('Magento_Core_Model_ThemeInterface');
        $theme = $this->getMockForAbstractClass('Magento_Core_Model_ThemeInterface');
        $theme->expects($this->once())->method('getParentTheme')->will($this->returnValue($parentTheme));

        $files = array(
            new Magento_Core_Model_Layout_File('0.xml', 'Module_One'),
            new Magento_Core_Model_Layout_File('1.xml', 'Module_One', $parentTheme),
            new Magento_Core_Model_Layout_File('2.xml', 'Module_One', $parentTheme),
            new Magento_Core_Model_Layout_File('3.xml', 'Module_One', $parentTheme),
            new Magento_Core_Model_Layout_File('4.xml', 'Module_One', $theme),
            new Magento_Core_Model_Layout_File('5.xml', 'Module_One', $theme),
            new Magento_Core_Model_Layout_File('6.xml', 'Module_One', $theme),
        );

        $this->_baseFiles
            ->expects($this->once())->method('getFiles')->with($theme)->will($this->returnValue(array($files[0])));

        $this->_themeFiles
            ->expects($this->at(0))->method('getFiles')->with($parentTheme)->will($this->returnValue(array($files[1])));
        $this->_overridingBaseFiles
            ->expects($this->at(0))->method('getFiles')->with($parentTheme)->will($this->returnValue(array($files[2])));
        $this->_overridingThemeFiles
            ->expects($this->at(0))->method('getFiles')->with($parentTheme)->will($this->returnValue(array($files[3])));

        $this->_themeFiles
            ->expects($this->at(1))->method('getFiles')->with($theme)->will($this->returnValue(array($files[4])));
        $this->_overridingBaseFiles
            ->expects($this->at(1))->method('getFiles')->with($theme)->will($this->returnValue(array($files[5])));
        $this->_overridingThemeFiles
            ->expects($this->at(1))->method('getFiles')->with($theme)->will($this->returnValue(array($files[6])));

        $this->_fileList->expects($this->at(0))->method('add')->with(array($files[0]));
        $this->_fileList->expects($this->at(1))->method('add')->with(array($files[1]));
        $this->_fileList->expects($this->at(2))->method('replace')->with(array($files[2]));
        $this->_fileList->expects($this->at(3))->method('replace')->with(array($files[3]));
        $this->_fileList->expects($this->at(4))->method('add')->with(array($files[4]));
        $this->_fileList->expects($this->at(5))->method('replace')->with(array($files[5]));
        $this->_fileList->expects($this->at(6))->method('replace')->with(array($files[6]));

        $this->_fileList->expects($this->atLeastOnce())->method('getAll')->will($this->returnValue($files));

        $this->assertSame($files, $this->_model->getFiles($theme));
    }
}
