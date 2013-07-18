<?php
/**
 * Test action controller factory class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Action_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Action_Factory */
    protected $_factory;

    /** @var Magento_ObjectManager */
    protected $_objectManagerMock;

    /** @var Mage_Core_Model_Config */
    protected $_configMock;

    protected function setUp()
    {
        /** Init all dependencies for SUT. */
        $this->_objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')->disableOriginalConstructor()
            ->getMock();
        /** Init SUT. */
        $this->_factory = new Mage_Webapi_Controller_Action_Factory($this->_objectManagerMock);
        $this->_configMock = $this->getMockBuilder('Mage_Core_Model_Config')->disableOriginalConstructor()->getMock();
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_factory);
        unset($this->_objectManagerMock);
        unset($this->_configMock);
        parent::tearDown();
    }


    /**
     * Test create action controller method.
     */
    public function testCreateActionController()
    {
        /** Create mock object of Mage_Webapi_Controller_ActionAbstract. */
        $actionController = $this->getMockBuilder('Mage_Webapi_Controller_ActionAbstract')
            ->disableOriginalConstructor()->getMock();
        /** Create request object. */
        $request = new Mage_Webapi_Controller_Request($this->_configMock, 'SOAP');
        $this->_objectManagerMock->expects($this->once())->method('create')->will(
            $this->returnValue($actionController)
        );
        $this->_factory->createActionController('Mage_Webapi_Controller_ActionAbstract', $request);
    }

    /**
     * Test action controller method with exception.
     */
    public function testCreateActionControllerWithException()
    {
        /** Create object of class which is not instance of Mage_Webapi_Controller_ActionAbstract. */
        $wrongController = new Varien_Object();
        /** Create request object. */
        $request = new Mage_Webapi_Controller_Request($this->_configMock, 'SOAP');
        /** Mock object manager create method to return wrong controller */
        $this->_objectManagerMock->expects($this->any())->method('create')->will($this->returnValue($wrongController));
        $this->setExpectedException(
            'InvalidArgumentException',
            'The specified class is not a valid API action controller.'
        );
        $this->_factory->createActionController('ClassName', $request);
    }
}
