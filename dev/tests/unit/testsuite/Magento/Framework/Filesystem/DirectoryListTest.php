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
