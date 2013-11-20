<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Code\Generator;

class IoTest extends \PHPUnit_Framework_TestCase
{
    /**#@+
     * Source and result class parameters
     */
    const DIRECTORY_SEPARATOR  = '/';
    const GENERATION_DIRECTORY = 'generation_directory';
    const CLASS_NAME           = 'class_name';
    const CLASS_FILE_NAME      = 'class/file/name';
    const FILE_NAME            = 'test_file';
    const FILE_CONTENT         = "content";
    /**#@-*/

    /**
     * Basic code generation directory
     *
     * @var string
     */
    protected $_generationDirectory;

    /**
     * @var \Magento\Code\Generator\Io
     */
    protected $_object;

    /**
     * @var \Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    /**
     * @var \Magento\Filesystem\Adapter\Local|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterLocalMock;

    /**
     * @var \Magento\Autoload\IncludePath|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_autoLoaderMock;

    protected function setUp()
    {
        $this->_generationDirectory
            = rtrim(self::GENERATION_DIRECTORY, self::DIRECTORY_SEPARATOR) . self::DIRECTORY_SEPARATOR;

        $this->_adapterLocalMock = $this->getMock('Magento\Filesystem\Adapter\Local');

        $this->_filesystemMock = $this->getMock('Magento\Filesystem',
            array('isWritable', 'write', 'createDirectory', 'has'),
            array($this->_adapterLocalMock)
        );

        $this->_autoLoaderMock = $this->getMock(
            'Magento\Autoload\IncludePath', array('getFilePath'), array(), '', false
          );
        $this->_autoLoaderMock->staticExpects($this->any())
            ->method('getFilePath')
            ->with(self::CLASS_NAME)
            ->will($this->returnValue(self::CLASS_FILE_NAME));

        $this->_object = new \Magento\Code\Generator\Io($this->_filesystemMock,
            $this->_autoLoaderMock,
            self::GENERATION_DIRECTORY
        );
    }

    protected function tearDown()
    {
        unset($this->_generationDirectory);
        unset($this->_filesystemMock);
        unset($this->_autoLoaderMock);
        unset($this->_object);
    }

    public function testGetResultFileDirectory()
    {
        $expectedDirectory = self::GENERATION_DIRECTORY . self::DIRECTORY_SEPARATOR . 'class/file/';
        $this->assertEquals($expectedDirectory, $this->_object->getResultFileDirectory(self::CLASS_NAME));
    }

    public function testGetResultFileName()
    {
        $expectedFileName = self::GENERATION_DIRECTORY . self::DIRECTORY_SEPARATOR . self::CLASS_FILE_NAME;
        $this->assertEquals($expectedFileName, $this->_object->getResultFileName(self::CLASS_NAME));
    }

    public function testWriteResultFile()
    {
        $this->_filesystemMock->expects($this->once())
            ->method('write')
            ->with($this->equalTo(self::FILE_NAME), $this->equalTo("<?php\n" . self::FILE_CONTENT))
            ->will($this->returnValue(true));

        $this->assertTrue($this->_object->writeResultFile(self::FILE_NAME, self::FILE_CONTENT));
    }

    public function testMakeGenerationDirectoryWritable()
    {
        $this->_filesystemMock->expects($this->once())
            ->method('isWritable')
            ->with($this->equalTo($this->_generationDirectory))
            ->will($this->returnValue(true));

        $this->assertTrue($this->_object->makeGenerationDirectory());
    }

    public function testMakeGenerationDirectoryReadOnly()
    {
        $this->_filesystemMock->expects($this->once())
            ->method('isWritable')
            ->with($this->equalTo($this->_generationDirectory))
            ->will($this->returnValue(false));

        $this->_filesystemMock->expects($this->once())
            ->method('createDirectory')
            ->with($this->equalTo($this->_generationDirectory), $this->anything())
            ->will($this->returnValue(true));

        $this->assertTrue($this->_object->makeGenerationDirectory());
    }

    public function testGetGenerationDirectory()
    {
        $this->assertEquals($this->_generationDirectory, $this->_object->getGenerationDirectory());
    }

    public function testFileExists()
    {
        $this->_filesystemMock->expects($this->once())
            ->method('has')
            ->with($this->equalTo(self::FILE_NAME))
            ->will($this->returnValue(false));

        $this->assertFalse($this->_object->fileExists(self::FILE_NAME));
    }
}
