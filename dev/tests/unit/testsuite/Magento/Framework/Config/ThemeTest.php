<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Config;

use Magento\Framework\App\Filesystem\DirectoryList;

class ThemeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dirReadMock;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->filesystemMock = $this->getMockBuilder('Magento\Framework\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dirReadMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\ReadInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->filesystemMock->expects($this->any())
            ->method('getDirectoryRead')
            ->with(DirectoryList::THEMES)
            ->willReturn($this->dirReadMock);
    }

    public function testGetSchemaFile()
    {
        /** @var \Magento\Framework\Config\Theme $config */
        $config = $this->objectManager->getObject(
            'Magento\Framework\Config\Theme'
        );
        $this->assertFileExists($config->getSchemaFile());
    }

    /**
     * @param string $themePath
     * @param array $expected
     * @dataProvider dataGetterDataProvider
     */
    public function testDataGetter($themePath, $expected)
    {
        $expected = reset($expected);
        /** @var \Magento\Framework\Config\Theme $config */
        $config = $this->objectManager->getObject(
            'Magento\Framework\Config\Theme',
            [
                'configContent' => file_get_contents(__DIR__ . '/_files/area/' . $themePath . '/theme.xml'),
                'composerContent' => file_get_contents(__DIR__ . '/_files/area/' . $themePath . '/composer.json'),
            ]
        );
        $this->assertSame($expected['version'], $config->getThemeVersion());
        $this->assertSame($expected['media'], $config->getMedia());
        $this->assertSame($expected['title'], $config->getThemeTitle());
        $this->assertSame($expected['parent'], $config->getParentTheme());
    }

    /**
     * @return array
     */
    public function dataGetterDataProvider()
    {
        return array(
            array(
                'default_default',
                array(array(
                    'version' => '0.1.0',
                    'media' => array('preview_image' => 'media/default_default.jpg'),
                    'title' => 'Default',
                    'parent' => null,
                ))),
            array(
                'default_test',
                array(array(
                    'version' => '0.1.1',
                    'media' => array('preview_image' => ''),
                    'title' => 'Test',
                    'parent' => ['Magento', 'default_default'],
                ))),
            array(
                'default_test2',
                array(array(
                    'version' => '0.1.2',
                    'media' => array('preview_image' => ''),
                    'title' => 'Test2',
                    'parent' => ['Magento', 'default_test'],
                ))),
            array(
                'test_default',
                array(array(
                    'version' => '0.1.3',
                    'media' => array('preview_image' => 'media/test_default.jpg'),
                    'title' => 'Default',
                    'parent' => null,
                ))),
            array(
                'test_external_package_descendant',
                array(array(
                    'version' => '0.1.4',
                    'media' => ['preview_image' => ''],
                    'title' => 'Default',
                    'parent' => ['Magento', 'default_test2'],
                ))),
        );
    }
}
