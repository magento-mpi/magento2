<?php
/**
 * Unit Test for Magento_Filesystem
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FilesystemTest extends PHPUnit_Framework_TestCase
{
    public function testWriteIsolation()
    {
        $expectedPath = '/tmp/path/file.txt';
        $data = 'Test data';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('write')
            ->with($expectedPath, $data);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->write($expectedPath, $data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     */
    public function testWriteIsolationException()
    {
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->never())
            ->method('write');

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->write('/tmp/../etc/passwd', 'user');
    }

    /**
     * @dataProvider normalizeDataProvider
     */
    public function testNormalize($path, $expected)
    {
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();

        $filesystem = new Magento_Filesystem($adapterMock);
        $this->assertEquals($expected, $filesystem->normalize($path));
    }

    public function normalizeDataProvider()
    {
        return array(
            array('/tmp/../file.txt', '/file.txt'),
            array('/tmp/../etc/mysql/file.txt', '/etc/mysql/file.txt'),
            array('/tmp/../file.txt', '/file.txt'),
            array('/tmp/./file.txt', '/tmp/file.txt'),
            array('/tmp/./../file.txt', '/file.txt'),
            array('/tmp/../../../file.txt', '/file.txt'),
            array('../file.txt', '/file.txt'),
            array('/../file.txt', '/file.txt'),
            array('/tmp/path/file.txt', '/tmp/path/file.txt'),
            array('/tmp/path', '/tmp/path'),
        );
    }
}
