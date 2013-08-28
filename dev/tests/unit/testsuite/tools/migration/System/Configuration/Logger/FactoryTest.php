<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/migration/System/Configuration/LoggerAbstract.php';

require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/migration/System/Configuration/Logger/File.php';

require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/migration/System/Configuration/Logger/Console.php';

require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/migration/System/Configuration/Logger/Factory.php';

require_once realpath(__DIR__ . '/../../../../../../../../') . '/tools/migration/System/FileManager.php';
require_once realpath(__DIR__ . '/../../../../../../../../') . '/tools/migration/System/FileReader.php';
require_once realpath(__DIR__ . '/../../../../../../../../')
    . '/tools/migration/System/WriterInterface.php';
require_once realpath(__DIR__ . '/../../../../../../../../') . '/tools/migration/System/Writer/Memory.php';


class Tools_Migration_System_Configuration_Logger_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tools_Migration_System_Configuration_Logger_Factory
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileManagerMock;

    public function setUp()
    {
        $this->_model = new Tools_Migration_System_Configuration_Logger_Factory();
        $this->_fileManagerMock = $this->getMock('Tools_Migration_System_FileManager', array(), array(), '', false);
    }

    public function tearDown()
    {
        unset($this->_model);
        unset($this->_fileManagerMock);
    }

    /**
     * @return array
     */
    public function getLoggerDataProvider()
    {
        return array(
            array('Tools_Migration_System_Configuration_Logger_File', 'file', 'report.log'),
            array('Tools_Migration_System_Configuration_Logger_Console', 'console', null),
            array('Tools_Migration_System_Configuration_Logger_Console', 'dummy', null),
        );
    }

    /**
     * @param string $expectedInstance
     * @param string $loggerType
     * @param string $path
     * @dataProvider getLoggerDataProvider
     */
    public function testGetLogger($expectedInstance, $loggerType, $path)
    {
        $this->assertInstanceOf($expectedInstance,
            $this->_model->getLogger($loggerType, $path, $this->_fileManagerMock)
        );
    }
}

