<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../')
    . '/tools/migration/System/Configuration/Generator.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../')
    . '/tools/migration/System/FileManager.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../')
    . '/tools/migration/System/Configuration/LoggerAbstract.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../')
    . '/tools/migration/System/Configuration/Logger/Console.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../')
    . '/tools/migration/System/Configuration/Formatter.php';


class Tools_Migration_System_Configuration_GeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tools_Migration_System_Configuration_Reader
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
        $this->_fileManagerMock = $this->getMock('Tools_Migration_System_FileManager', array(), array(), '', false);
        $this->_loggerMock = $this->getMock('Tools_Migration_System_Configuration_Logger_Console', array(), array(),
            '', false
        );
        $this->_formatterMock = $this->getMock('Tools_Migration_System_Configuration_Formatter', array(), array(),
            '', false
        );

        $this->_model = new Tools_Migration_System_Configuration_Generator(
            $this->_fileManagerMock, $this->_formatterMock, $this->_loggerMock
        );
    }

    public function testGetConfiguration()
    {
        $this->_fileManagerMock->expects($this->once())->method('getFileList')
            ->will($this->returnValue(array('testFile')));
        $this->_fileManagerMock->expects($this->once())->method('getContents')->with('testFile')
            ->will($this->returnValue('<config><system><tabs></tabs></system></config>'));
        $parsedArray = array('config' => array('system' => array('tabs')));
        $this->_parserMock->expects($this->once())->method('parse')->with($this->isInstanceOf('DOMDocument'))
            ->will($this->returnValue($parsedArray));

        $transformedArray = array('value' => 'expected');
        $this->_mapperMock->expects($this->once())->method('transform')->with($parsedArray)
            ->will($this->returnValue($transformedArray));


        $this->assertEquals(array('testFile' => $transformedArray), $this->_model->getConfiguration());
    }
}
