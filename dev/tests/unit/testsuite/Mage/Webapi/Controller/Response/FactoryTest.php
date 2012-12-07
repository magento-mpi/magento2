<?php
/**
 * Test Response factory.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Response_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Response_Factory */
    protected $_factory;

    /** @var Mage_Webapi_Controller_Front */
    protected $_apiFrontController;

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_apiFrontController = $this->getMockBuilder('Mage_Webapi_Controller_Front')
            ->disableOriginalConstructor()->getMock();
        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')->disableOriginalConstructor()
            ->getMock();
        $this->_factory = new Mage_Webapi_Controller_Response_Factory(
            $this->_apiFrontController,
            $this->_objectManager);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_factory);
        unset($this->_apiFrontController);
        unset($this->_objectManager);
        parent::tearDown();
    }

    /**
     * Test get method.
     */
    public function testGet()
    {
        /** Mock front controller mock to return Soap api type. */
        $this->_apiFrontController->expects($this->once())->method('determineApiType')->will(
            $this->returnValue(Mage_Webapi_Controller_Front::API_TYPE_SOAP)
        );
        /** Assert object manager get method will be executed once with Mage_Webapi_Controller_Response parameter. */
        $this->_objectManager->expects($this->once())->method('get')->with('Mage_Webapi_Controller_Response');
        $this->_factory->get();
    }

    /**
     * Test get method with wrong API type.
     */
    public function testGetWithWrongApiType()
    {
        $wrongApiType = 'Wrong SOAP';
        /**Mock front controller determine api method to return wrong api type */
        $this->_apiFrontController->expects($this->once())->method('determineApiType')->will(
            $this->returnValue($wrongApiType)
        );
        $this->setExpectedException(
            'LogicException',
            printf('There is no corresponding response class for the "%s" API type.', $wrongApiType)
        );
        $this->_factory->get();
    }
}
