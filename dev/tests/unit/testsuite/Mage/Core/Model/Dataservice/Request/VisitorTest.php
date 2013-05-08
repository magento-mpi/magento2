<?php
/**
 * Test class for Mage_Core_Model_Dataservice_Request_Visitor
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Request_VisitorTest extends PHPUnit_Framework_TestCase {

    const SOME_INTERESTING_PARAMS = 'Some interesting params.';

    public function test() {
        $requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')->disableOriginalConstructor()->getMock();
        $requestMock->expects($this->once())->method('getParams')->will($this->returnValue(
                self::SOME_INTERESTING_PARAMS
            ));
        $requestVisitor = new Mage_Core_Model_Dataservice_Request_Visitor($requestMock);
        $pathVisitorMock = $this->getMockBuilder('Mage_Core_Model_Dataservice_Path_Visitor')->disableOriginalConstructor()->getMock();
        $pathVisitorMock->expects($this->once())->method('getCurrentPathElement')->will($this->returnValue('params'));

        $this->assertEquals(self::SOME_INTERESTING_PARAMS, $requestVisitor->visit($pathVisitorMock));
    }

}