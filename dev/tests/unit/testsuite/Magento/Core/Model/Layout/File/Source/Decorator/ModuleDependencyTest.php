<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_File_Source_Decorator_ModuleDependencyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\File\Source\Decorator\ModuleDependency
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_fileSource;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_moduleListMock;

    protected function setUp()
    {
        $modulesConfig = array(
            'Fixture_ModuleB' => array(
                'name' => 'Fixture_ModuleB',
            ),
            'Fixture_ModuleA' => array(
                'name' => 'Fixture_ModuleA',
                'depends' => array(
                    'module' => array('Fixture_ModuleB'),
                )
            ),
        );

        $this->_fileSource = $this->getMockForAbstractClass('\Magento\Core\Model\Layout\File\SourceInterface');
        $this->_moduleListMock = $this->getMock('Magento\Core\Model\ModuleListInterface');
        $this->_moduleListMock->expects($this->any())->method('getModules')->will($this->returnValue($modulesConfig));
        $this->_model = new \Magento\Core\Model\Layout\File\Source\Decorator\ModuleDependency(
            $this->_fileSource, $this->_moduleListMock
        );
    }

    /**
     * @param array $fixtureFiles
     * @param array $expectedFiles
     * @param string $message
     * @dataProvider getFilesDataProvider
     */
    public function testGetFiles(array $fixtureFiles, array $expectedFiles, $message)
    {
        $theme = $this->getMockForAbstractClass('\Magento\Core\Model\ThemeInterface');
        $this->_fileSource
            ->expects($this->once())
            ->method('getFiles')
            ->with($theme)
            ->will($this->returnValue($fixtureFiles))
        ;
        $this->assertSame($expectedFiles, $this->_model->getFiles($theme), $message);
    }

    public function getFilesDataProvider()
    {
        $fileOne = new \Magento\Core\Model\Layout\File('b.xml', 'Fixture_ModuleB');
        $fileTwo = new \Magento\Core\Model\Layout\File('a.xml', 'Fixture_ModuleA');
        $fileThree = new \Magento\Core\Model\Layout\File('b.xml', 'Fixture_ModuleA');

        $unknownFileOne = new \Magento\Core\Model\Layout\File('b.xml', 'Unknown_ModuleA');
        $unknownFileTwo = new \Magento\Core\Model\Layout\File('a.xml', 'Unknown_ModuleB');
        return array(
            'same module' => array(
                array($fileThree, $fileTwo),
                array($fileTwo, $fileThree),
                'Files belonging to the same module are expected to be sorted by file names',
            ),
            'different modules' => array(
                array($fileTwo, $fileOne),
                array($fileOne, $fileTwo),
                'Files belonging to different modules are expected to be sorted by module dependencies',
            ),
            'different unknown modules' => array(
                array($unknownFileTwo, $unknownFileOne),
                array($unknownFileOne, $unknownFileTwo),
                'Files belonging to different unknown modules are expected to be sorted by module names',
            ),
            'known and unknown modules' => array(
                array($fileTwo, $unknownFileOne),
                array($unknownFileOne, $fileTwo),
                'Files belonging to unknown modules are expected to go before ones of known modules',
            ),
        );
    }
}
