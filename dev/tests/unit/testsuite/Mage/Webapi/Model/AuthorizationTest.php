<?php
/**
 * Test Mage_Webapi_Model_Authorization
 *
 * @copyright {}
 */
class Mage_Webapi_Model_AuthorizationTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_coreAuthorizationMock;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helperMock;

    /** @var Mage_Webapi_Model_Authorization */
    protected $_webapiAuthorization;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_coreAuthorizationMock = $this->getMockBuilder('Mage_Core_Model_Authorization')
            ->setMethods(array('isAllowed'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_helperMock = $this->getMock('Mage_Webapi_Helper_Data', array('__'));
        /** Initialize SUT. */
        $this->_webapiAuthorization = new Mage_Webapi_Model_Authorization(
            $this->_helperMock,
            $this->_coreAuthorizationMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_coreAuthorizationMock);
        unset($this->_helperMock);
        unset($this->_webapiAuthorization);
        parent::tearDown();
    }

    public function testCheckResourceAclMageWebapiException()
    {
        $this->_coreAuthorizationMock->expects($this->exactly(2))->method('isAllowed')->will($this->returnValue(false));
        $this->_helperMock->expects($this->once())->method('__')->will($this->returnArgument(0));
        $this->setExpectedException('Mage_Webapi_Exception', 'Access to resource is forbidden.');
        $this->_webapiAuthorization->checkResourceAcl('invalidResource', 'invalidMethod');
    }

    public function testCheckResourceAcl()
    {
        $this->_coreAuthorizationMock->expects($this->once())->method('isAllowed')->will($this->returnValue(true));
        $this->_webapiAuthorization->checkResourceAcl('validResource', 'validMethod');
    }
}
