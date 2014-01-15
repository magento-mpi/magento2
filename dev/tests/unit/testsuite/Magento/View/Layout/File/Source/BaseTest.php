<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source;

use Magento\Filesystem,
    Magento\Filesystem\Directory\Read,
    Magento\View\Layout\File\Factory;

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
            'Magento\Filesystem',
            array('getDirectoryRead', '__wakeup'),
            array(),
            '',
            false
        );
        $filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\Filesystem::MODULES)
            ->will($this->returnValue($this->directory));

        $this->fileFactory = $this->getMock('Magento\View\Layout\File\Factory', array(), array(), '', false);
        $this->model = new Base($filesystem, $this->fileFactory);
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

        $handlePath = 'code/Module/%s/view/area/layout/%s.xml';
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
            $checkResult[$key] = new \Magento\View\Layout\File(
                $file['handle'] . '.xml',
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
                    array('handle' => '1', 'module' => 'One'),
                    array('handle' => '2', 'module' => 'One'),
                    array('handle' => '3', 'module' => 'Two'),
                ),
                '*',
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
