<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\File\Source;

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
            array(),
            array(),
            '',
            false
        );
        $filesystem = $this->getMock(
            'Magento\App\Filesystem',
            array('getDirectoryRead', '__wakeup'),
            array(),
            '',
            false
        );
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::MODULES_DIR)
            ->will($this->returnValue($this->directory));

        $this->fileFactory = $this->getMock('Magento\View\File\Factory', array(), array(), '', false);
        $this->model = new Base($filesystem, $this->fileFactory, 'subdir');
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
        $theme->expects($this->once())->method('getArea')->will($this->returnValue('area'));

        $handlePath = 'code/Module/%s/view/area/subdir/%s';
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
            $moduleName = 'Module_' . $file['module'];
            $checkResult[$key] = new \Magento\View\File(
                $file['handle'],
                $moduleName,
                $theme
            );

            $this->fileFactory
                ->expects($this->at($key))
                ->method('create')
                ->with(sprintf($handlePath, $file['module'], $file['handle']), $moduleName)
                ->will($this->returnValue($checkResult[$key]));
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
                    array('handle' => '1.xml', 'module' => 'One'),
                    array('handle' => '2.xml', 'module' => 'One'),
                    array('handle' => '3.xml', 'module' => 'Two'),
                ),
                '*.xml',
            ),
            array(
                array(
                    array('handle' => 'preset/4', 'module' => 'Four'),
                ),
                'preset/4',
            ),
        );
    }
}
