<?php
/**
 * Test Magento_Webapi_Model_Authorization
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_AuthorizationTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_coreAuthorization;

    /** @var Magento_Webapi_Model_Authorization */
    protected $_webapiAuthorization;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_coreAuthorization = $this->getMockBuilder('Magento_AuthorizationInterface')
            ->getMock();
        /** Initialize SUT. */
        $this->_webapiAuthorization = new Magento_Webapi_Model_Authorization(
            $this->_coreAuthorization
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_coreAuthorization);
        unset($this->_helperMock);
        unset($this->_webapiAuthorization);
        parent::tearDown();
    }

    public function testCheckServiceAclMageWebapiException()
    {
        $this->_coreAuthorization->expects($this->exactly(2))->method('isAllowed')->will($this->returnValue(false));
        $this->_helperMock->expects($this->once())->method('__')->will($this->returnArgument(0));
        $this->setExpectedException('Magento_Webapi_Exception', 'Access to service is forbidden.');
        $this->_webapiAuthorization->checkResourceAcl('invalidResource', 'invalidMethod');
    }

    public function testCheckServiceAcl()
    {
        $this->_coreAuthorization->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_webapiAuthorization->checkServiceAcl('validService', 'validMethod');
    }
}
