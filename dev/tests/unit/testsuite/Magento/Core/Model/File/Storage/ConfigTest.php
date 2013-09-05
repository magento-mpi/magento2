<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_File_Storage_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_File_Storage_Config
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_streamMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_streamFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileStorageMock;

    /**
     * @var array
     */
    protected  $_config = array();

    protected function setUp()
    {
        $this->_fileStorageMock = $this->getMock('Magento_Core_Model_File_Storage', array(), array(), '', false);
        $this->_fileStorageMock
             ->expects($this->once())
             ->method('getScriptConfig')
             ->will($this->returnValue($this->_config));
        $this->_streamFactoryMock =
            $this->getMock('Magento_Filesystem_Stream_LocalFactory', array('create'), array(), '', false);
        $this->_streamMock = $this->getMock('Magento\Filesystem\StreamInterface');
        $this->_streamFactoryMock
            ->expects($this->any())->method('create')->will($this->returnValue($this->_streamMock));
        $this->_model = new Magento_Core_Model_File_Storage_Config(
            $this->_fileStorageMock,
            $this->_streamFactoryMock,
            'cacheFile'
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testSave()
    {
        $this->_streamMock->expects($this->once())->method('open')->with('w');
        $this->_streamMock->expects($this->once())->method('write')->with(json_encode($this->_config));
        $this->_streamMock->expects($this->once())->method('close');
        $this->_model->save();
    }
}
