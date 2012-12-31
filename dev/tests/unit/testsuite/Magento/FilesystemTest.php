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
    public function testSetWorkingDirectory()
    {
        $filesystem = new Magento_Filesystem($this->_getDefaultAdapterMock());
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertAttributeEquals('/tmp', '_workingDirectory', $filesystem);
    }

    /**
     * @expectedException InvalidArgumentException
     * @exceptedExceptionMessage Working directory "/tmp" does not exists
     */
    public function testSetWorkingDirectoryException()
    {
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(false));
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
    }

    /**
     * @dataProvider allowCreateDirectoriesDataProvider
     * @param bool $allow
     * @param int $mode
     */
    public function testSetIsAllowCreateDirectories($allow, $mode)
    {
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $filesystem = new Magento_Filesystem($adapterMock);
        $this->assertSame($filesystem, $filesystem->setIsAllowCreateDirectories($allow, $mode));
        $this->assertAttributeEquals($allow, '_isAllowCreateDirs', $filesystem);
        if (!$mode) {
            $mode = 0777;
        }
        $this->assertAttributeEquals($mode, '_newDirPermissions', $filesystem);
    }

    /**
     * @return array
     */
    public function allowCreateDirectoriesDataProvider()
    {
        return array(
            array(true, 0644),
            array(false, null)
        );
    }

    /**
     * @dataProvider twoFilesOperationsDataProvider
     * @param string $method
     */
    public function testTwoFilesOperation($method)
    {
        $source = '/tmp/path01';
        $destination = '/tmp/path02';
        $adapterMock = $this->_getDefaultAdapterMock();
        $adapterMock->expects($this->once())
            ->method($method)
            ->with($source, $destination);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->$method($source, $destination);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     * @dataProvider twoFilesOperationsDataProvider
     * @param string $method
     * @param string $source
     * @param string $destination
     */
    public function testTwoFilesOperationsIsolationException($method, $source, $destination)
    {
        $adapterMock = $this->_getDefaultAdapterMock();
        $adapterMock->expects($this->never())
            ->method($method);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->$method($source, $destination);
    }

    /**
     * @return array
     */
    public function twoFilesOperationsDataProvider()
    {
        return array(
            'copy first path invalid' => array('copy', '/tmp/../etc/passwd', '/tmp/path001'),
            'copy second path invalid' => array('copy', '/tmp/uploaded.txt', '/tmp/../etc/passwd'),
            'copy both path invalid' => array('copy', '/tmp/../etc/passwd', '/tmp/../dev/null'),
            'rename first path invalid' => array('rename', '/tmp/../etc/passwd', '/tmp/path001'),
            'rename second path invalid' => array('rename', '/tmp/uploaded.txt', '/tmp/../etc/passwd'),
            'rename both path invalid' => array('rename', '/tmp/../etc/passwd', '/tmp/../dev/null'),
        );
    }

    public function testEnsureDirectoryExists()
    {
        $dir = '/tmp/path';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->at(0))
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->at(1))
            ->method('isDirectory')
            ->with($dir)
            ->will($this->returnValue(true));
        $adapterMock->expects($this->exactly(2))
            ->method('isDirectory');
        $adapterMock->expects($this->never())
            ->method('createDirectory');
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->ensureDirectoryExists($dir, 0644);
    }

    public function testEnsureDirectoryExistsNoDir()
    {
        $dir = '/tmp/path';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->at(0))
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->at(1))
            ->method('isDirectory')
            ->with($dir)
            ->will($this->returnValue(false));
        $adapterMock->expects($this->exactly(2))
            ->method('isDirectory');
        $adapterMock->expects($this->once())
            ->method('createDirectory')
            ->with($dir);
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->ensureDirectoryExists($dir, 0644);
    }

    public function testCreateFile()
    {
        $filesystem = new Magento_Filesystem($this->_getDefaultAdapterMock());
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertInstanceOf('Magento_Filesystem_File', $filesystem->createFile('/tmp/testfile.txt'));
    }

    /**
     * @dataProvider allowCreateDirsDataProvider
     * @param bool $allowCreateDirs
     */
    public function testTouch($allowCreateDirs)
    {
        $validPath = '/tmp/path/file.txt';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->exactly(1 + (int)$allowCreateDirs))
            ->method('isDirectory')
            ->will($this->returnValue(true));

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setIsAllowCreateDirectories($allowCreateDirs);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->touch($validPath);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     */
    public function testTouchIsolation()
    {
        $filesystem = new Magento_Filesystem($this->_getDefaultAdapterMock());
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->touch('/etc/passwd');
    }

    /**
     * @return array
     */
    public function allowCreateDirsDataProvider()
    {
        return array(array(true), array(false));
    }

    public function testCreateStreamDefault()
    {
        $filesystem = new Magento_Filesystem($this->_getDefaultAdapterMock());
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertInstanceOf('Magento_Filesystem_Stream_Memory', $filesystem->createStream('/tmp/test.txt'));
    }

    public function testCreateStreamCustom()
    {
        $path = '/tmp/test.txt';
        $streamMock = $this->getMockBuilder('Magento_Filesystem_Stream_Local')
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_Adapter_Local')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method('createStream')
            ->with($path)
            ->will($this->returnValue($streamMock));
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertInstanceOf('Magento_Filesystem_Stream_Local', $filesystem->createStream($path));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     */
    public function testCreateStreamIsolation()
    {
        $filesystem = new Magento_Filesystem($this->_getDefaultAdapterMock());
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertInstanceOf('Magento_Filesystem_Stream_Memory', $filesystem->createStream('/tmp/../etc/test.txt'));
    }

    /**
     * @dataProvider modeDataProvider
     * @param string|Magento_Filesystem_Stream_Mode $mode
     */
    public function testCreateAndOpenStream($mode)
    {
        $path = '/tmp/test.txt';
        $streamMock = $this->getMockBuilder('Magento_Filesystem_Stream_Local')
            ->disableOriginalConstructor()
            ->getMock();
        $streamMock->expects($this->once())
            ->method('open');
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_Adapter_Local')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method('createStream')
            ->with($path)
            ->will($this->returnValue($streamMock));
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertInstanceOf('Magento_Filesystem_Stream_Local', $filesystem->createAndOpenStream($path, $mode));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Wrong mode parameter
     */
    public function testCreateAndOpenStreamException()
    {
        $path = '/tmp/test.txt';
        $streamMock = $this->getMockBuilder('Magento_Filesystem_Stream_Local')
            ->disableOriginalConstructor()
            ->getMock();
        $streamMock->expects($this->never())
            ->method('open');
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_Adapter_Local')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method('createStream')
            ->with($path)
            ->will($this->returnValue($streamMock));
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertInstanceOf('Magento_Filesystem_Stream_Local',
            $filesystem->createAndOpenStream($path, new stdClass()));
    }

    /**
     * @return array
     */
    public function modeDataProvider()
    {
        return array(
            array('r'),
            array(new Magento_Filesystem_Stream_Mode('w'))
        );
    }

    /**
     * @dataProvider adapterMethods
     * @param string $method
     * @param string $adapterMethod
     * @param array|null $params
     */
    public function testAdapterMethods($method, $adapterMethod, array $params = null)
    {
        $validPath = '/tmp/path/file.txt';
        $adapterMock = $this->_getDefaultAdapterMock();
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
        $adapterMock = $this->_getDefaultAdapterMock();
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
            'isFile' => array('isFile', 'isFile'),
            'isWritable' => array('isWritable', 'isWritable'),
            'isReadable' => array('isReadable', 'isReadable'),
            'getNestedKeys' => array('getNestedKeys', 'getNestedKeys'),
            'changePermissions' => array('changePermissions', 'changePermissions', array(0777, true)),
            'createDirectory' => array('createDirectory', 'createDirectory', array(0777)),
            'write' => array('write', 'write', array('Test string'))
        );
    }

    /**
     * Test isDirectory
     */
    public function testIsDirectory()
    {
        $validPath = '/tmp/path';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->at(0))
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->at(1))
            ->method('isDirectory')
            ->with($validPath)
            ->will($this->returnValue(true));
        $adapterMock->expects($this->exactly(2))
            ->method('isDirectory');

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertTrue($filesystem->isDirectory($validPath));
    }

    /**
     * Test isDirectory isolation
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     */
    public function testIsDirectoryIsolation()
    {
        $validPath = '/tmp/../etc/passwd';
        $filesystem = new Magento_Filesystem($this->_getDefaultAdapterMock());
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertTrue($filesystem->isDirectory($validPath));
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

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getDefaultAdapterMock()
    {
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        return $adapterMock;
    }
}
