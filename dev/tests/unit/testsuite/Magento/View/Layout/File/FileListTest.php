<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File;

class FileListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Layout\File\FileList
     */
    private $_model;

    /**
     * @var \Magento\View\File
     */
    private $_baseFile;

    /**
     * @var \Magento\View\File
     */
    private $_themeFile;

    /**
     * @var \Magento\View\Layout\File\FileList\Collator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collator;

    protected function setUp()
    {
        $this->_baseFile = $this->_createLayoutFile('fixture.xml', 'Fixture_TestModule');
        $this->_themeFile = $this->_createLayoutFile('fixture.xml', 'Fixture_TestModule', 'area/theme/path');
        $this->collator = $this->getMock('Magento\View\Layout\File\FileList\Collator', array('collate'));
        $this->_model = new \Magento\View\Layout\File\FileList($this->collator);
        $this->_model->add(array($this->_baseFile, $this->_themeFile));
    }

    /**
     * Return newly created theme layout file with a mocked theme
     *
     * @param string $filename
     * @param string $module
     * @param string|null $themeFullPath
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\View\Design\ThemeInterface
     */
    protected function _createLayoutFile($filename, $module, $themeFullPath = null)
    {
        $theme = null;
        if ($themeFullPath !== null) {
            $theme = $this->getMockForAbstractClass('Magento\View\Design\ThemeInterface');
            $theme->expects($this->any())->method('getFullPath')->will($this->returnValue($themeFullPath));
        }
        return new \Magento\View\File($filename, $module, $theme);
    }

    public function testGetAll()
    {
        $this->assertSame(array($this->_baseFile, $this->_themeFile), $this->_model->getAll());
    }

    public function testAddBaseFile()
    {
        $file = $this->_createLayoutFile('new.xml', 'Fixture_TestModule');
        $this->_model->add(array($file));
        $this->assertSame(array($this->_baseFile, $this->_themeFile, $file), $this->_model->getAll());
    }

    public function testAddThemeFile()
    {
        $file = $this->_createLayoutFile('new.xml', 'Fixture_TestModule', 'area/theme/path');
        $this->_model->add(array($file));
        $this->assertSame(array($this->_baseFile, $this->_themeFile, $file), $this->_model->getAll());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Layout file 'test/fixture.xml' is indistinguishable from the file 'fixture.xml'
     */
    public function testAddBaseFileException()
    {
        $file = $this->_createLayoutFile('test/fixture.xml', 'Fixture_TestModule');
        $this->_model->add(array($file));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Layout file 'test/fixture.xml' is indistinguishable from the file 'fixture.xml'
     */
    public function testAddThemeFileException()
    {
        $file = $this->_createLayoutFile('test/fixture.xml', 'Fixture_TestModule', 'area/theme/path');
        $this->_model->add(array($file));
    }

    public function testReplace()
    {
        $files = array('1');
        $result = array('3');
        $this->collator
            ->expects($this->once())
            ->method('collate')
            ->with(
                $this->equalTo($files),
                $this->equalTo(array(
                    $this->_baseFile->getFileIdentifier() => $this->_baseFile,
                    $this->_themeFile->getFileIdentifier() => $this->_themeFile)
                ))
            ->will($this->returnValue($result));
        $this->assertNull($this->_model->replace($files));
        $this->assertSame($result, $this->_model->getAll());
    }
}
