<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Configuration/LoggerAbstract.php';

require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Configuration/Logger/File.php';

require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Configuration/Logger/Console.php';

require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Configuration/Logger/Factory.php';

require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/FileManager.php';
require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/FileReader.php';
require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration//System/WriterInterface.php';
require_once realpath(__DIR__ . '/../../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Writer/Memory.php';


class Magento_Test_Tools_Migration_System_Configuration_Logger_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Tools_Migration_System_Configuration_Logger_Factory
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileManagerMock;

    protected function setUp()
    {
        $this->_model = new Magento_Tools_Migration_System_Configuration_Logger_Factory();
        $this->_fileManagerMock = $this->getMock(
            'Magento_Tools_Migration_System_FileManager', array(), array(), '', false);
    }

    protected function tearDown()
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
            array('Magento_Tools_Migration_System_Configuration_Logger_File', 'file', 'report.log'),
            array('Magento_Tools_Migration_System_Configuration_Logger_Console', 'console', null),
            array('Magento_Tools_Migration_System_Configuration_Logger_Console', 'dummy', null),
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

