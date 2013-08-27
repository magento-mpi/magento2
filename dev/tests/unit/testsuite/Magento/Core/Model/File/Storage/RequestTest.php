<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_File_Storage_RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_File_Storage_Request
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var string
     */
    protected $_workingDir = '..var';

    /**
     * @var string
     */
    protected $_pathInfo = 'PathInfo';

    protected function setUp()
    {
        $path = '..PathInfo';
        $this->_requestMock = $this->getMock('Zend_Controller_Request_Http', array(), array(), '', false);
        $this->_requestMock->expects($this->once())->method('getPathInfo')->will($this->returnValue($path));
        $this->_model = new Magento_Core_Model_File_Storage_Request($this->_workingDir, $this->_requestMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_requestMock);
    }

    public function testGetPathInfo()
    {
        $this->assertEquals($this->_pathInfo, $this->_model->getPathInfo());
    }

    public function testGetFilePath()
    {
        $this->assertEquals($this->_workingDir . DS . $this->_pathInfo, $this->_model->getFilePath());
    }
}
