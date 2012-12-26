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
    public function testRename()
    {
        $source = '/tmp/path01';
        $destination = '/tmp/path02';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('rename')
            ->with($source, $destination);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->rename($source, $destination);
    }

    /**
     * @dataProvider adapterMethods
     * @param string $method
     * @param array|null $params
     */
    public function testAdapterMethods($method, $adapterMethod, array $params = null)
    {
        $validPath = '/tmp/path/file.txt';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method($adapterMethod)
            ->with($validPath);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $params = (array)$params;
        array_unshift($params, $validPath);
        call_user_func_array(array($filesystem, $method), $params);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     * @dataProvider adapterMethods
     * @param string $method
     * @param string $adapterMethod
     * @param array|null $params
     */
    public function testIsolationException($method, $adapterMethod, array $params = null)
    {
        $invalidPath = '/tmp/../etc/passwd';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->never())
            ->method($adapterMethod);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $params = (array)$params;
        array_unshift($params, $invalidPath);
        call_user_func_array(array($filesystem, $method), $params);
    }

    /**
     * @return array
     */
    public function adapterMethods()
    {
        return array(
            'exists' => array('has', 'exists'),
            'read' => array('read', 'read'),
            'delete' => array('delete', 'delete'),
            'isDirectory' => array('isDirectory', 'isDirectory'),
            'write' => array('write', 'write', array('Test string'))
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     * @dataProvider renameDataProvider
     * @param string $source
     * @param string $destination
     */
    public function testRenameIsolationException($source, $destination)
    {
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->never())
            ->method('rename');

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->rename($source, $destination);
    }

    /**
     * @return array
     */
    public function renameDataProvider()
    {
        return array(
            'first path invalid' => array('/tmp/../etc/passwd', '/tmp/path001'),
            'second path invalid' => array('/tmp/uploaded.txt', '/tmp/../etc/passwd'),
            'both path invalid' => array('/tmp/../etc/passwd', '/tmp/../dev/null'),
        );
    }

    /**
     * @dataProvider absolutePathDataProvider
     * @param string $path
     * @param string $expected
     */
    public function testGetAbsolutePath($path, $expected)
    {
        $this->assertEquals($expected, Magento_Filesystem::getAbsolutePath($path));
    }

    /**
     * @return array
     */
    public function absolutePathDataProvider()
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
            array('C:\\Windows', 'C:/Windows'),
            array('C:\\Windows\\system32\\..', 'C:/Windows'),
        );
    }

    /**
     * @dataProvider pathDataProvider
     * @param array $parts
     * @param string $expected
     * @param bool $isAbsolute
     */
    public function testGetPathFromArray(array $parts, $expected, $isAbsolute)
    {
        $this->assertEquals($expected, Magento_Filesystem::getPathFromArray($parts, $isAbsolute));
    }

    /**
     * @return array
     */
    public function pathDataProvider()
    {
        return array(
            array(array('etc', 'mysql', 'my.cnf'), '/etc/mysql/my.cnf',true),
            array(array('etc', 'mysql', 'my.cnf'), 'etc/mysql/my.cnf', false),
            array(array('C:', 'Windows', 'my.cnf'), 'C:/Windows/my.cnf', false),
            array(array('C:', 'Windows', 'my.cnf'), 'C:/Windows/my.cnf', true),
        );
    }

    /**
     * @dataProvider pathDataProvider
     * @param array $expected
     * @param string $path
     */
    public function testGetPathAsArray(array $expected, $path)
    {
        $this->assertEquals($expected, Magento_Filesystem::getPathAsArray($path));
    }

    /**
     * @dataProvider isAbsolutePathDataProvider
     * @param bool $isReal
     * @param string $path
     */
    public function testIsAbsolutePath($isReal, $path)
    {
        $this->assertEquals($isReal, Magento_Filesystem::isAbsolutePath($path));
    }

    /**
     * @return array
     */
    public function isAbsolutePathDataProvider()
    {
        return array(
            array(true, '/tmp/file.txt'),
            array(false, '/tmp/../etc/mysql/my.cnf'),
            array(false, '/tmp/../tmp/file.txt')
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Path must contain at least one node
     */
    public function testGetPathFromArrayException()
    {
        Magento_Filesystem::getPathFromArray(array());
    }
}
