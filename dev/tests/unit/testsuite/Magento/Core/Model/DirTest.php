<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class DirTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $code
     * @param string $value
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidUriDataProvider
     */
    public function testInvalidUri($code, $value)
    {
        new \Magento\App\Dir(__DIR__, array($code => $value));
    }

    /**
     * @return array
     */
    public function invalidUriDataProvider()
    {
        return array(
            array(\Magento\App\Dir::MEDIA, '/'),
            array(\Magento\App\Dir::MEDIA, '//'),
            array(\Magento\App\Dir::MEDIA, '/value'),
            array(\Magento\App\Dir::MEDIA, 'value/'),
            array(\Magento\App\Dir::MEDIA, '/value/'),
            array(\Magento\App\Dir::MEDIA, 'one\\two'),
            array(\Magento\App\Dir::MEDIA, '../dir'),
            array(\Magento\App\Dir::MEDIA, './dir'),
            array(\Magento\App\Dir::MEDIA, 'one/../two'),
        );
    }

    public function testGetUri()
    {
        $dir = new \Magento\App\Dir(__DIR__, array(
            \Magento\App\Dir::PUB   => '',
            \Magento\App\Dir::MEDIA => 'test',
            'custom' => 'test2'
        ));

        // arbitrary custom value
        $this->assertEquals('test2', $dir->getUri('custom'));

        // setting empty value correctly adjusts its children
        $this->assertEquals('', $dir->getUri(\Magento\App\Dir::PUB));
        $this->assertEquals('lib', $dir->getUri(\Magento\App\Dir::PUB_LIB));

        // at the same time if another child has custom value, it must not be affected by its parent
        $this->assertEquals('test', $dir->getUri(\Magento\App\Dir::MEDIA));
        $this->assertEquals('test/upload', $dir->getUri(\Magento\App\Dir::UPLOAD));
    }

    /**
     * Test that URIs are not affected by custom dirs
     */
    public function testGetUriIndependentOfDirs()
    {
        $fixtureDirs = array(
            \Magento\App\Dir::ROOT => __DIR__ . '/root',
            \Magento\App\Dir::MEDIA => __DIR__ . '/media',
            'custom' => 'test2'
        );
        $default = new \Magento\App\Dir(__DIR__);
        $custom = new \Magento\App\Dir(__DIR__, array(), $fixtureDirs);
        foreach (array_keys($fixtureDirs) as $dirCode ) {
            $this->assertEquals($default->getUri($dirCode), $custom->getUri($dirCode));
        }
    }

    public function testGetDir()
    {
        $newRoot = __DIR__ . DIRECTORY_SEPARATOR . 'root';
        $newMedia = __DIR__ . DIRECTORY_SEPARATOR . 'media';
        $dir = new \Magento\App\Dir(__DIR__, array(), array(
            \Magento\App\Dir::ROOT => $newRoot,
            \Magento\App\Dir::MEDIA => $newMedia,
            'custom' => 'test2'
        ));

        // arbitrary custom value
        $this->assertEquals('test2', $dir->getDir('custom'));

        // new root has affected all its non-customized children
        $this->assertStringStartsWith($newRoot, $dir->getDir(\Magento\App\Dir::APP));
        $this->assertStringStartsWith($newRoot, $dir->getDir(\Magento\App\Dir::MODULES));

        // but it didn't affect the customized dirs
        $this->assertEquals($newMedia, $dir->getDir(\Magento\App\Dir::MEDIA));
        $this->assertStringStartsWith($newMedia, $dir->getDir(\Magento\App\Dir::UPLOAD));
    }

    /**
     * Test that dirs are not affected by custom URIs
     */
    public function testGetDirIndependentOfUris()
    {
        $fixtureUris = array(
            \Magento\App\Dir::PUB   => '',
            \Magento\App\Dir::MEDIA => 'test',
            'custom' => 'test2'
        );
        $default = new \Magento\App\Dir(__DIR__);
        $custom = new \Magento\App\Dir(__DIR__, $fixtureUris);
        foreach (array_keys($fixtureUris) as $dirCode ) {
            $this->assertEquals($default->getDir($dirCode), $custom->getDir($dirCode));
        }
    }
}
