<?php
/**
 * Test Magento_Webapi_Controller_Dispatcher_Factory.
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Dispatcher_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** @var Magento_Webapi_Controller_Dispatcher_Factory */
    protected $_dispatcherFactory;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_objectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        /** Initialize SUT. */
        $this->_dispatcherFactory = new Magento_Webapi_Controller_Dispatcher_Factory($this->_objectManager);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_objectManager);
        unset($this->_dispatcherFactory);
        parent::tearDown();
    }

    public function testGetLogicExceptionInvalidApiType()
    {
        $this->setExpectedException(
            'LogicException',
            'There is no corresponding dispatcher class for the "invalidApiType" API type.'
        );
        $this->_dispatcherFactory->get('invalidApiType');
    }

    public function testGet()
    {
        $expectedController = $this->getMockBuilder('Magento_Webapi_Controller_Dispatcher_Soap')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_objectManager->expects($this->once())->method('get')->will($this->returnValue($expectedController));
        $this->assertEquals(
            $expectedController,
            $this->_dispatcherFactory->get(Magento_Webapi_Controller_Front::API_TYPE_SOAP)
        );
    }
}
