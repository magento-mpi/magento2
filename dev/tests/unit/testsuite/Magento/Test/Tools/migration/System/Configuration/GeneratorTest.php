<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Configuration/Generator.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/FileManager.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Configuration/LoggerAbstract.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../')
    . '/tools/Magento/Tools/Migration/System/Configuration/Formatter.php';


class Magento_Test_Tools_Migration_System_Configuration_GeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Tools_Migration_System_Configuration_Generator
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loggerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_formatterMock;

    protected function setUp()
    {
        $this->_fileManagerMock = $this->getMock('Magento_Tools_Migration_System_FileManager', array(), array(), '', false);
        $this->_loggerMock = $this->getMockForAbstractClass('Magento_Tools_Migration_System_Configuration_LoggerAbstract',
            array(), '', false, false, false, array('add')
        );
        $this->_formatterMock = $this->getMock('Magento_Tools_Migration_System_Configuration_Formatter', array(), array(),
            '', false
        );

        $this->_model = new Magento_Tools_Migration_System_Configuration_Generator(
            $this->_formatterMock, $this->_fileManagerMock, $this->_loggerMock
        );
    }

    public function testCreateConfigurationGeneratesConfiguration()
    {
        $dom = new DOMDocument();
        $dom->loadXML(
            preg_replace('/\n|\s{4}/', '', file_get_contents(__DIR__ . '/_files/convertedConfiguration.xml'))
        );
        $stripComments = new DOMXPath($dom);
        foreach ($stripComments->query('//comment()') as $comment) {
            $comment->parentNode->removeChild($comment);
        }
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $expectedXml = $dom->saveXML();

        $this->_fileManagerMock->expects($this->once())->method('write')
        ->with($this->stringContains('system.xml'), $expectedXml);

        $this->_formatterMock->expects($this->once())->method('parseString')
            ->will(
                $this->returnCallback(
                    function($xml) {
                        $dom = new DOMDocument();
                        $dom->loadXML($xml);
                        $dom->preserveWhiteSpace = false;
                        $dom->formatOutput = true;
                        return $dom->saveXML();
                    }
                )
            );

        $this->_loggerMock->expects($this->once())->method('add')->with(
            'someFile',
            Magento_Tools_Migration_System_Configuration_LoggerAbstract:: FILE_KEY_INVALID
        );

        $this->_model->createConfiguration('someFile', include __DIR__ . '/_files/mappedConfiguration.php');
    }
}
