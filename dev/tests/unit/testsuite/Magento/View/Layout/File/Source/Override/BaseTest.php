<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source\Override;

use Magento\Filesystem\Directory\Read,
    Magento\View\File\Factory;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Base
     */
    private $model;

    /**
     * @var Read | \PHPUnit_Framework_MockObject_MockObject
     */
    private $directory;

    /**
     * @var Factory | \PHPUnit_Framework_MockObject_MockObject
     */
    private $fileFactory;

    protected function setUp()
    {
        $this->directory = $this->getMock(
            'Magento\Filesystem\Directory\Read',
            array('getAbsolutePath', 'search'), array(), '', false
        );
        $filesystem = $this->getMock(
            'Magento\App\Filesystem', array('getDirectoryRead'), array(), '', false
        );
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::THEMES_DIR)
            ->will($this->returnValue($this->directory));
        $this->fileFactory = $this->getMock('Magento\View\File\Factory', array(), array(), '', false);
        $this->model = new \Magento\View\Layout\File\Source\Override\Base(
            $filesystem, $this->fileFactory
        );
    }

    /**
     * @param array $files
     * @param string $filePath
     *
     * @dataProvider dataProvider
     */
    public function testGetFiles($files, $filePath)
    {
        $theme = $this->getMockForAbstractClass('Magento\View\Design\ThemeInterface');
        $theme->expects($this->once())->method('getFullPath')->will($this->returnValue('area/theme/path'));

        $handlePath = 'design/area/theme/path/%s/layout/override/base/%s.xml';
        $returnKeys = array();
        foreach ($files as $file) {
            $returnKeys[] = sprintf($handlePath, $file['module'], $file['handle']);
        }

        $this->directory->expects($this->once())
            ->method('search')
            ->will($this->returnValue($returnKeys));
        $this->directory->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnArgument(0));

        $checkResult = array();
        foreach ($files as $key => $file) {
            $checkResult[$key] = new \Magento\View\File($file['handle'] . '.xml', $file['module']);
            $this->fileFactory
                ->expects($this->at($key))
                ->method('create')
                ->with(sprintf($handlePath, $file['module'], $file['handle']), $file['module'])
                ->will($this->returnValue($checkResult[$key]))
            ;
        }

        $this->assertSame($checkResult, $this->model->getFiles($theme, $filePath));
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return array(
            array(
                array(
                    array('handle' => '1', 'module' => 'Module_One'),
                    array('handle' => '2', 'module' => 'Module_One'),
                    array('handle' => '3', 'module' => 'Module_Two'),
                ),
                '*',
            ),
            array(
                array(
                    array('handle' => 'preset/4', 'module' => 'Module_Fourth'),
                ),
                'preset/4',
            ),
        );
    }
}
