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
    . '/tools/migration/System/Configuration/Reader.php';

class Tools_Migration_System_Configuration_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Tools_Migration_System_Writer_Factory
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
        $this->_parserMock = $this->getMock('Tools_Migration_System_Configuration_Mapper', array(), array(), '', false);

        $this->_model = new Tools_Migration_System_Writer_Factory(
            $this->_fileManagerMock, $this->_parserMock, $this->_mapperMock
        );
    }

    protected function testGetConfiguration()
    {
        $this->_fileManagerMock->expects($this->once())->method('getFileList')->will(
            $this->returnValue(array('file1', 'file2'))
        );
    }
}
