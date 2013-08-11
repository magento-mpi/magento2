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
    . '/tools/migration/System/Configuration/Reader.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../')
    . '/tools/migration/System/FileManager.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../')
    . '/tools/migration/System/Configuration/Mapper.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../')
    . '/tools/migration/System/Configuration/Parser.php';


class Magento_Test_Tools_Migration_System_Configuration_ReaderTest extends PHPUnit_Framework_TestCase
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
    protected $_parserMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mapperMock;

    protected function setUp()
    {
        $this->_fileManagerMock = $this->getMock('Tools_Migration_System_FileManager', array(), array(), '', false);
        $this->_parserMock = $this->getMock('Tools_Migration_System_Configuration_Parser', array(), array(), '', false);
        $this->_mapperMock = $this->getMock('Tools_Migration_System_Configuration_Mapper', array(), array(), '', false);

        $this->_model = new Tools_Migration_System_Configuration_Reader(
            $this->_fileManagerMock, $this->_parserMock, $this->_mapperMock
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
