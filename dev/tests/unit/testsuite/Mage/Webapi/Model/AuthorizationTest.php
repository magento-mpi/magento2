<?php
/**
 * Test Mage_Webapi_Model_Authorization
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_AuthorizationTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_coreAuthorization;

    /** @var Mage_Webapi_Model_Authorization */
    protected $_webapiAuthorization;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_coreAuthorization = $this->getMockBuilder('Magento_AuthorizationInterface')
            ->getMock();
        /** Initialize SUT. */
        $this->_webapiAuthorization = new Mage_Webapi_Model_Authorization(
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

    public function testCheckResourceAclMageWebapiException()
    {
        $this->_coreAuthorization->expects($this->exactly(2))->method('isAllowed')->will($this->returnValue(false));
        $this->setExpectedException('Mage_Webapi_Exception', 'Access to resource is forbidden.');
        $this->_webapiAuthorization->checkResourceAcl('invalidResource', 'invalidMethod');
    }

    public function testCheckResourceAcl()
    {
        $this->_coreAuthorization->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_webapiAuthorization->checkResourceAcl('validResource', 'validMethod');
    }
}
