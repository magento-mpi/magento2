<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem;

use Magento\Framework\App\Filesystem\DirectoryList as DirList;
use Magento\Framework\Filesystem;

class DirectoryListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for add directory and getConfig methods
     *
     * @dataProvider addDirectoryGetConfigDataProvider
     * @param string $root
     * @param array $directories
     * @param array $configs
     * @param array $expectedConfig
     */
    public function testAddDirectoryGetConfig($root, array $directories, array $configs, array $expectedConfig)
    {
        $directoryList = new DirectoryList($root, $directories);
        foreach ($configs as $code => $config) {
            $directoryList->addDirectory($code, $config);
            $this->assertEquals($expectedConfig[$code], $directoryList->getConfig($code));
        }
    }

    public function addDirectoryGetConfigDataProvider()
    {
        return array(
            'static_view' => array(
                __DIR__,
                array(),
                array(
                    'custom2_' . DirList::STATIC_VIEW => array(
                        'path' => 'some/static',
                        'uri' => 'some/static',
                        'permissions' => 0777,
                        'read_only' => true,
                        'allow_create_dirs' => true
                    )
                ),
                array(
                    'custom2_' . DirList::STATIC_VIEW => array(
                        'path' => str_replace('\\', '/', __DIR__ . '/some/static'),
                        'uri' => 'some/static',
                        'permissions' => 0777,
                        'read_only' => true,
                        'allow_create_dirs' => true
                    )
                ),
            )
        );
    }

    /**
     * @expectedException \Magento\Framework\Filesystem\FilesystemException
     */
    public function testAddDefinedDirectory()
    {
        $directories = array(DirList::STATIC_VIEW => array('path' => ''));
        $directoryList = new DirectoryList(__DIR__, $directories);
        $directoryList->addDirectory(DirList::STATIC_VIEW, array('path' => ''));
    }

    /**
     * Test for creating DirectoryList with invalid URI
     *
     * @param string $code
     * @param string $value
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidUriDataProvider
     */
    public function testInvalidUri($code, $value)
    {
        new DirectoryList(__DIR__, array($code => array('uri' => $value)));
    }

    /**
     * Data provider for testInvalidUri
     *
     * @return array
     */
    public function invalidUriDataProvider()
    {
        return array(
            array(DirList::MEDIA, '/'),
            array(DirList::MEDIA, '//'),
            array(DirList::MEDIA, '/value'),
            array(DirList::MEDIA, 'value/'),
            array(DirList::MEDIA, '/value/'),
            array(DirList::MEDIA, 'one\\two'),
            array(DirList::MEDIA, '../dir'),
            array(DirList::MEDIA, './dir'),
            array(DirList::MEDIA, 'one/../two')
        );
    }

    /**
     * Test for getting uri from DirectoryList
     */
    public function testGetUri()
    {
        $dir = new DirectoryList(
            __DIR__,
            array(
                DirList::PUB => array('uri' => ''),
                DirList::MEDIA => array('uri' => 'test'),
                'custom' => array('uri' => 'test2')
            )
        );

        $this->assertEquals('test2', $dir->getConfig('custom')['uri']);
        $this->assertEquals('', $dir->getConfig(DirList::PUB)['uri']);
        $this->assertEquals('test', $dir->getConfig(DirList::MEDIA)['uri']);
    }

    /**
     * Test for getting directory path from DirectoryList
     */
    public function testGetDir()
    {
        $newRoot = __DIR__ . '/root';
        $newMedia = __DIR__ . '/media';
        $dir = new DirectoryList(
            __DIR__,
            array(
                DirList::ROOT => array('path' => $newRoot),
                DirList::MEDIA => array('path' => $newMedia),
                'custom' => array('path' => 'test2')
            )
        );

        $this->assertEquals('test2', $dir->getDir('custom'));
        $this->assertEquals(str_replace('\\', '/', $newRoot), $dir->getConfig(DirList::ROOT)['path']);
        $this->assertEquals(str_replace('\\', '/', $newMedia), $dir->getConfig(DirList::MEDIA)['path']);
    }

    public function testIsConfigured()
    {
        $dir = new DirectoryList(__DIR__, array(DirList::PUB => array('uri' => '')));

        $this->assertTrue($dir->isConfigured(DirList::PUB));
        $this->assertFalse($dir->isConfigured(DirList::MEDIA));
    }
}
