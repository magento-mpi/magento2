<?php
/**
 * Test class for Mage_Core_Model_Dataservice_Request
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_RequestTest extends PHPUnit_Framework_TestCase
{

    const SOME_INTERESTING_PARAMS = 'Some interesting params.';

    public function testGetChild()
    {
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')->disableOriginalConstructor()
            ->getMock();
        $requestMock->expects($this->once())->method('getParams')->will(
            $this->returnValue(
                self::SOME_INTERESTING_PARAMS
            )
        );
        $requestVisitor = new Mage_Core_Model_Dataservice_Request($requestMock);
        $this->assertEquals(self::SOME_INTERESTING_PARAMS, $requestVisitor->getChild('params'));
    }

    public function testNotFound()
    {
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')->disableOriginalConstructor()
            ->getMock();

        $requestVisitor = new Mage_Core_Model_Dataservice_Request($requestMock);
        $this->assertEquals(null, $requestVisitor->getChild('foo'));
    }
}