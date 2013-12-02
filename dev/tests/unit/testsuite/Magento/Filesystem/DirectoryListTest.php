<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem;

use Magento\Filesystem;

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
                    Filesystem::PUB_LIB => array('path' => 'pub/lib_basic')
                ),
                array(
                    Filesystem::PUB_LIB => array(
                        'path' => 'pub/lib',
                        'uri' => 'pub/lib',
                        'permissions' => 0777,
                        'read_only' => true,
                        'allow_create_dirs' => true
                    )
                ),
                array(
                    Filesystem::PUB_LIB => array(
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
            array(Filesystem::MEDIA, '/'),
            array(Filesystem::MEDIA, '//'),
            array(Filesystem::MEDIA, '/value'),
            array(Filesystem::MEDIA, 'value/'),
            array(Filesystem::MEDIA, '/value/'),
            array(Filesystem::MEDIA, 'one\\two'),
            array(Filesystem::MEDIA, '../dir'),
            array(Filesystem::MEDIA, './dir'),
            array(Filesystem::MEDIA, 'one/../two'),
        );
    }

    /**
     * Test for getting uri from DirectoryList
     */
    public function testGetUri()
    {
        $dir = new DirectoryList(__DIR__, array(
            Filesystem::PUB   => array('uri' => ''),
            Filesystem::MEDIA => array('uri' => 'test'),
            'custom' => array('uri' => 'test2')
        ));

        $this->assertEquals('test2', $dir->getConfig('custom')['uri']);
        $this->assertEquals('', $dir->getConfig(Filesystem::PUB)['uri']);
        $this->assertEquals('test', $dir->getConfig(Filesystem::MEDIA)['uri']);
    }

    /**
     * Test for getting directory path from DirectoryList
     */
    public function testGetDir()
    {
        $newRoot = __DIR__ . '/root';
        $newMedia = __DIR__ . '/media';
        $dir = new DirectoryList(__DIR__, array(
            Filesystem::ROOT => array('path' => $newRoot),
            Filesystem::MEDIA => array('path' => $newMedia),
            'custom' => array('path' => 'test2')
        ));

        $this->assertEquals('test2', $dir->getDir('custom'));
        $this->assertEquals(str_replace('\\', '/', $newRoot), $dir->getConfig(Filesystem::ROOT)['path']);
        $this->assertEquals(str_replace('\\', '/', $newMedia), $dir->getConfig(Filesystem::MEDIA)['path']);
    }

    /**
     * Test that dirs are not affected by custom URIs
     */
    public function testGetDirIndependentOfUris()
    {
        $fixtureUris = array(
            Filesystem::PUB   => array('uri' => ''),
            Filesystem::MEDIA => array('uri' => 'test')
        );
        $default = new DirectoryList(__DIR__);
        $custom = new DirectoryList(__DIR__, $fixtureUris);
        foreach (array_keys($fixtureUris) as $dirCode ) {
            $this->assertEquals($default->getConfig($dirCode)['path'], $custom->getConfig($dirCode)['path']);
        }
    }
}
