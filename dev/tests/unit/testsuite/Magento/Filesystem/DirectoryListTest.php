<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem;

use Magento\Filesystem,
    Magento\App\Filesystem as AppFilesystem;

class DirectoryListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for add directory and getConfig methods
     *
     * @dataProvider providerConfig
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

    /**
     * Data provider for testAddDirectoryGetConfig
     */
    public function providerConfig()
    {
        return array(
            'pub_lib' => array(
                __DIR__,
                array(
                    AppFilesystem::PUB_LIB_DIR => array('path' => 'pub/lib_basic')
                ),
                array(
                    AppFilesystem::PUB_LIB_DIR => array(
                        'path' => 'pub/lib',
                        'uri' => 'pub/lib',
                        'permissions' => 0777,
                        'read_only' => true,
                        'allow_create_dirs' => true
                    )
                ),
                array(
                    AppFilesystem::PUB_LIB_DIR => array(
                        'path' => str_replace('\\', '/', __DIR__ . '/pub/lib'),
                        'uri' => 'pub/lib',
                        'permissions' => 0777,
                        'read_only' => true,
                        'allow_create_dirs' => true
                    )
                ),
            )
        );
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
            array(AppFilesystem::MEDIA_DIR, '/'),
            array(AppFilesystem::MEDIA_DIR, '//'),
            array(AppFilesystem::MEDIA_DIR, '/value'),
            array(AppFilesystem::MEDIA_DIR, 'value/'),
            array(AppFilesystem::MEDIA_DIR, '/value/'),
            array(AppFilesystem::MEDIA_DIR, 'one\\two'),
            array(AppFilesystem::MEDIA_DIR, '../dir'),
            array(AppFilesystem::MEDIA_DIR, './dir'),
            array(AppFilesystem::MEDIA_DIR, 'one/../two'),
        );
    }

    /**
     * Test for getting uri from DirectoryList
     */
    public function testGetUri()
    {
        $dir = new DirectoryList(__DIR__, array(
            AppFilesystem::PUB_DIR   => array('uri' => ''),
            AppFilesystem::MEDIA_DIR => array('uri' => 'test'),
            'custom' => array('uri' => 'test2')
        ));

        $this->assertEquals('test2', $dir->getConfig('custom')['uri']);
        $this->assertEquals('', $dir->getConfig(AppFilesystem::PUB_DIR)['uri']);
        $this->assertEquals('test', $dir->getConfig(AppFilesystem::MEDIA_DIR)['uri']);
    }

    /**
     * Test for getting directory path from DirectoryList
     */
    public function testGetDir()
    {
        $newRoot = __DIR__ . '/root';
        $newMedia = __DIR__ . '/media';
        $dir = new DirectoryList(__DIR__, array(
            AppFilesystem::ROOT_DIR => array('path' => $newRoot),
            AppFilesystem::MEDIA_DIR => array('path' => $newMedia),
            'custom' => array('path' => 'test2')
        ));

        $this->assertEquals('test2', $dir->getDir('custom'));
        $this->assertEquals(str_replace('\\', '/', $newRoot), $dir->getConfig(AppFilesystem::ROOT_DIR)['path']);
        $this->assertEquals(str_replace('\\', '/', $newMedia), $dir->getConfig(AppFilesystem::MEDIA_DIR)['path']);
    }

    /**
     * Test that dirs are not affected by custom URIs
     */
    public function testGetDirIndependentOfUris()
    {
        $fixtureUris = array(
            AppFilesystem::PUB_DIR   => array('uri' => ''),
            AppFilesystem::MEDIA_DIR => array('uri' => 'test')
        );
        $default = new DirectoryList(__DIR__);
        $custom = new DirectoryList(__DIR__, $fixtureUris);
        foreach (array_keys($fixtureUris) as $dirCode ) {
            $this->assertEquals($default->getConfig($dirCode)['path'], $custom->getConfig($dirCode)['path']);
        }
    }
}
