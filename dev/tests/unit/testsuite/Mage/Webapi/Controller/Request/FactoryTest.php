<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Request_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_apiFrontMock;

    /** @var Mage_Webapi_Controller_Request_Factory */
    protected $_requestFactory;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->_apiFrontMock = $this->getMockBuilder('Mage_Webapi_Controller_Front')
            ->setMethods(array('determineApiType'))
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_requestFactory = new Mage_Webapi_Controller_Request_Factory(
            $this->_apiFrontMock,
            $this->_objectManager
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_objectManager);
        unset($this->_apiFrontMock);
        unset($this->_requestFactory);
        parent::tearDown();
    }

    public function testGetLogicExceptionInvalidApiType()
    {
        $this->setExpectedException(
            'LogicException',
            'There is no corresponding request class for the "invalidApiType" API type.'
        );
        $this->_apiFrontMock->expects($this->once())
            ->method('determineApiType')
            ->will($this->returnValue('invalidApiType'));
        $this->_requestFactory->get();
    }

    public function testGet()
    {
        $this->_apiFrontMock->expects($this->once())
            ->method('determineApiType')
            ->will($this->returnValue(Mage_Webapi_Controller_Front::API_TYPE_REST));
        $expectedController = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Request')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_objectManager->expects($this->once())->method('get')->will($this->returnValue($expectedController));
        $this->assertEquals($expectedController, $this->_requestFactory->get());
    }
}



