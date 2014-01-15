<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\File\FileList;

class CollatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collator
     */
    protected $_model;

    /**
     * @var \Magento\View\Layout\File[]
     */
    protected $_originFiles;

    /**
     * @var \Magento\View\Layout\File
     */
    private $_baseFile;

    /**
     * @var \Magento\View\Layout\File
     */
    private $_themeFile;

    protected function setUp()
    {
        $this->markTestIncomplete('in progress');
        $this->_baseFile = $this->createLayoutFile('fixture.less', 'Fixture_TestModule');
        $this->_themeFile = $this->createLayoutFile('fixture.less', 'Fixture_TestModule', 'area/theme/path');
        $this->_originFiles = array(
            $this->_baseFile->getFileIdentifier() => $this->_baseFile,
            $this->_themeFile->getFileIdentifier() => $this->_themeFile
        );
        $this->_model = new Collator();
    }

    /**
     * Return newly created theme layout file with a mocked theme
     *
     * @param string $filename
     * @param string $module
     * @param string|null $themeFullPath
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\View\Layout\File
     */
    protected function createLayoutFile($filename, $module, $themeFullPath = null)
    {
        $theme = null;
        if ($themeFullPath !== null) {
            $theme = $this->getMockForAbstractClass('Magento\View\Design\ThemeInterface');
            $theme->expects($this->any())->method('getFullPath')->will($this->returnValue($themeFullPath));
        }
        return new \Magento\View\Layout\File($filename, $module, $theme);
    }

    public function testCollateBaseFile()
    {
        $file = $this->createLayoutFile('test/fixture.less', 'Fixture_TestModule');
        $this->assertSame(
            array($file->getFileIdentifier() => $file, $this->_themeFile->getFileIdentifier() => $this->_themeFile),
            $this->_model->collate(array($file), $this->_originFiles)
        );
    }

    public function testReplaceThemeFile()
    {
        $file = $this->createLayoutFile('test/fixture.less', 'Fixture_TestModule', 'area/theme/path');
        $this->assertSame(
            array($this->_baseFile->getFileIdentifier() => $this->_baseFile, $file->getFileIdentifier() => $file),
            $this->_model->collate(array($file), $this->_originFiles)
        );
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Overriding layout file 'new.xml' does not match to any of the files
     */
    public function testReplaceBaseFileException()
    {
        $file = $this->createLayoutFile('new.less', 'Fixture_TestModule');
        $this->_model->collate(array($file), $this->_originFiles);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Overriding layout file 'test/fixture.xml' does not match to any of the files
     */
    public function testReplaceBaseFileEmptyThemePathException()
    {
        $file = $this->createLayoutFile('test/fixture.less', 'Fixture_TestModule', '');
        $this->_model->collate(array($file), $this->_originFiles);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Overriding layout file 'new.xml' does not match to any of the files
     */
    public function testReplaceThemeFileException()
    {
        $file = $this->createLayoutFile('new.less', 'Fixture_TestModule', 'area/theme/path');
        $this->_model->collate(array($file), $this->_originFiles);
    }
}
