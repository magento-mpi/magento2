<?php
/**
 * Test for Magento_Filesystem_Stream_Local
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Stream_LocalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Filesystem_Stream_Local
     */
    protected $_stream;

    protected function setUp()
    {
        $this->_stream = new Magento_Filesystem_Stream_Local(__DIR__ . DS . '..' . DS . '_files' . DS . 'popup.css');
    }

    /**
     * @expectedException Magento_Filesystem_Exception
     */
    public function testOpenException()
    {
        $stream = new Magento_Filesystem_Stream_Local(__DIR__ . DS . '..' . DS . '_files' . DS . 'invalid.css');
        $stream->open(new Magento_Filesystem_Stream_Mode('r'));
    }

    public function testOpenNewFile()
    {
        $stream = new Magento_Filesystem_Stream_Local(__DIR__ . DS . '..' . DS . '_files' . DS . 'new.css');
        $stream->open(new Magento_Filesystem_Stream_Mode('w'));
    }

    public function testOpenExistingFile()
    {
        $this->_stream->open(new Magento_Filesystem_Stream_Mode('r'));
    }

    public function testRead()
    {
        $this->_stream->open(new Magento_Filesystem_Stream_Mode('r'));
        $data  = $this->_stream->read(15);
        $this->assertEquals('var myData = 5;', $data);

    }

    public function testReadCsv()
    {
        $stream = new Magento_Filesystem_Stream_Local(__DIR__ . DS . '..' . DS . '_files' . DS . 'data.csv');
        $stream->open(new Magento_Filesystem_Stream_Mode('r'));
        $data = $stream->readCsv(0);
        $this->assertEquals(array('field1', 'field2'), $data);
        $data = $stream->readCsv(0);
        $this->assertEquals(array('field3', 'field4'), $data);
        $data = $stream->readCsv(0);
        $this->assertFalse($data);
    }

    /**
     * @param string $method
     * @dataProvider streamNotOpenedDataProvider
     * @expectedException Magento_Filesystem_Exception
     */
    public function testExceptionStreamNotOpened($method)
    {
        $this->_stream->$method(1);
    }

    /**
     * @return array
     */
    public function streamNotOpenedDataProvider()
    {
        return array(
            array('read'),
            array('readCsv'),
            array('write'),
            array('writeCsv'),
            array('close'),
            array('flush'),
            array('seek'),
            array('tell'),
            array('tell'),
            array('eof'),
        );
    }

    /**
     * @param string $method
     * @dataProvider forbiddenReadDataProvider
     * @expectedException Magento_Filesystem_Exception
     * @expectedExceptionMessage The stream does not allow read.
     */
    public function testForbiddenRead($method)
    {
        $stream = new Magento_Filesystem_Stream_Local(__DIR__ . DS . '..' . DS . '_files' . DS . 'new.css');
        $stream->open(new Magento_Filesystem_Stream_Mode('w'));
        $stream->$method(1);
    }

    /**
     * @return array
     */
    public function forbiddenReadDataProvider()
    {
        return array(
            array('read'),
            array('readCsv'),
        );
    }
}

