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
    protected $model;

    /**
     * @var \Magento\View\Layout\File[]
     */
    protected $originFiles;

    /**
     * @var \Magento\View\Layout\File
     */
    protected $baseFile;

    /**
     * @var \Magento\View\Layout\File
     */
    protected $themeFile;

    protected function setUp()
    {
        $this->baseFile = $this->createLayoutFile('fixture_1.less', 'Fixture_TestModule');
        $this->themeFile = $this->createLayoutFile('fixture.less', 'Fixture_TestModule', 'area/theme/path');
        $this->originFiles = array(
            $this->baseFile->getFileIdentifier() => $this->baseFile,
            $this->themeFile->getFileIdentifier() => $this->themeFile
        );
        $this->model = new Collator();
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

    public function testCollate()
    {
        $file = $this->createLayoutFile('test/fixture.less', 'Fixture_TestModule');
        $expected = array(
            $this->baseFile->getFileIdentifier() => $this->baseFile,
            $file->getFileIdentifier() => $file
        );
        $result = $this->model->collate(array($file), $this->originFiles);
        $this->assertSame($expected, $result);
    }
}
