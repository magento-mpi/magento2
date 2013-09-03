<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Request_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Mage_Core_Model_App */
    protected $_application;

    /** @var Mage_Core_Model_Config */
    protected $_config;

    /** @var Mage_Core_Controller_Request_Http */
    protected $_request;

    /** @var Mage_Webapi_Controller_Request_Factory */
    protected $_requestFactory;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->setMethods(array('get'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->_application = $this->getMockBuilder('Mage_Core_Model_App')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_request = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_application->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->_request));
        $this->_config = $this->getMockBuilder('Mage_Core_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();

        /** Initialize SUT. */
        $this->_requestFactory = new Mage_Webapi_Controller_Request_Factory(
            $this->_application,
            $this->_config,
            $this->_objectManager
        );

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_objectManager);
        unset($this->_application);
        unset($this->_config);
        unset($this->_requestFactory);
        parent::tearDown();
    }

    public function testGetLogicExceptionInvalidApiType()
    {
        $this->setExpectedException(
            'LogicException',
            'No corresponding handler class found for "invalidapirequest" request type'
        );
        $this->_request->expects($this->once())
            ->method('getOriginalPathInfo')
            ->will($this->returnValue('invalidApiRequest'));
        $this->_requestFactory->get();
    }

    public function testGet()
    {
        $expectedController = $this->getMockBuilder('Mage_Webapi_Controller_Rest_Request')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_objectManager->expects($this->once())->method('get')->will($this->returnValue($expectedController));
        $this->_request->expects($this->once())
            ->method('getOriginalPathInfo')->will($this->returnValue('rest'));
        $this->assertEquals($expectedController, $this->_requestFactory->get());
    }
}
