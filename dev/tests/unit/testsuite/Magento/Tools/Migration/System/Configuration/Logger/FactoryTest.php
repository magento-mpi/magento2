<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\Migration\System\Configuration\Logger;


require_once realpath(
    __DIR__ . '/../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/System/Configuration/AbstractLogger.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/System/Configuration/Logger/File.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/System/Configuration/Logger/Console.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/System/Configuration/Logger/Factory.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/System/FileManager.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/System/FileReader.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../'
) . '/tools/Magento/Tools/Migration//System/WriterInterface.php';
require_once realpath(
    __DIR__ . '/../../../../../../../../../'
) . '/tools/Magento/Tools/Migration/System/Writer/Memory.php';
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Migration\System\Configuration\Logger\Factory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileManagerMock;

    protected function setUp()
    {
        $this->_model = new \Magento\Tools\Migration\System\Configuration\Logger\Factory();
        $this->_fileManagerMock = $this->getMock(
            'Magento\Tools\Migration\System\FileManager',
            array(),
            array(),
            '',
            false
        );
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
            array('Magento\Tools\Migration\System\Configuration\Logger\File', 'file', 'report.log'),
            array('Magento\Tools\Migration\System\Configuration\Logger\Console', 'console', null),
            array('Magento\Tools\Migration\System\Configuration\Logger\Console', 'dummy', null)
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
        $this->assertInstanceOf(
            $expectedInstance,
            $this->_model->getLogger($loggerType, $path, $this->_fileManagerMock)
        );
    }
}
