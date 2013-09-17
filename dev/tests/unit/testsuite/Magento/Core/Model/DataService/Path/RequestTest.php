<?php
/**
 * Magento_Core_Model_DataService_Path_Request
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_DataService_Path_RequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test data for params
     */
    const SOME_INTERESTING_PARAMS = 'Some interesting params.';

    public function testGetChild()
    {
        $requestMock = $this->getMockBuilder('Magento_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(self::SOME_INTERESTING_PARAMS));
        $requestVisitor = new Magento_Core_Model_DataService_Path_Request($requestMock);
        $this->assertEquals(self::SOME_INTERESTING_PARAMS, $requestVisitor->getChildNode('params'));
    }

    public function testNotFound()
    {
        $requestMock = $this->getMockBuilder('Magento_Core_Controller_Request_Http')->disableOriginalConstructor()
            ->getMock();

        $requestVisitor = new Magento_Core_Model_DataService_Path_Request($requestMock);
        $this->assertEquals(null, $requestVisitor->getChildNode('foo'));
    }
}