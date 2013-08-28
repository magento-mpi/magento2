<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../../') . '/tools/migration/Acl/Db/LoggerAbstract.php';
require_once realpath(__DIR__ . '/../../../../../../../../') . '/tools/migration/Acl/Db/Logger/File.php';


require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/migration/System/Configuration/LoggerAbstract.php';

require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/migration/System/Configuration/Logger/File.php';

require_once realpath(__DIR__ . '/../../../../../../../../') . '/tools/migration/System/FileManager.php';

class Tools_Migration_System_Configuration_Logger_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileManagerMock;

    public function setUp()
    {
        $this->_fileManagerMock = $this->getMock('Tools_Migration_System_FileManager', array(), array(), '', false);
    }

    public function tearDown()
    {
        unset($this->_fileManagerMock);
    }

    public function testConstructWithValidFile()
    {
        new Tools_Migration_System_Configuration_Logger_File('report.log', $this->_fileManagerMock);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithInValidFile()
    {
        new Tools_Migration_System_Configuration_Logger_File(null, $this->_fileManagerMock);
    }

    public function testReport()
    {
        $model = new Tools_Migration_System_Configuration_Logger_File('report.log', $this->_fileManagerMock);
        $this->_fileManagerMock->expects($this->once())->method('write')->with($this->stringEndsWith('report.log'));
        $model->report();
    }
}
