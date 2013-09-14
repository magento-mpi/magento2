<?php
/**
 * Test action controller factory class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Action_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Action\Factory */
    protected $_factory;

    /** @var \Magento\ObjectManager */
    protected $_objectManagerMock;

    protected function setUp()
    {
        /** Init all dependencies for SUT. */
        $this->_objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')->disableOriginalConstructor()
            ->getMock();
        /** Init SUT. */
        $this->_factory = new \Magento\Webapi\Controller\Action\Factory($this->_objectManagerMock);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_factory);
        unset($this->_objectManagerMock);
        parent::tearDown();
    }


    /**
     * Test create action controller method.
     */
    public function testCreateActionController()
    {
        /** Create mock object of \Magento\Webapi\Controller\ActionAbstract. */
        $actionController = $this->getMockBuilder('Magento\Webapi\Controller\ActionAbstract')
            ->disableOriginalConstructor()->getMock();
        /** Create request object. */
        $request = new \Magento\Webapi\Controller\Request('SOAP');
        $this->_objectManagerMock->expects($this->once())->method('create')->will(
            $this->returnValue($actionController)
        );
        $this->_factory->createActionController('Magento\Webapi\Controller\ActionAbstract', $request);
    }

    /**
     * Test action controller method with exception.
     */
    public function testCreateActionControllerWithException()
    {
        /** Create object of class which is not instance of \Magento\Webapi\Controller\ActionAbstract. */
        $wrongController = new \Magento\Object();
        /** Create request object. */
        $request = new \Magento\Webapi\Controller\Request('SOAP');
        /** Mock object manager create method to return wrong controller */
        $this->_objectManagerMock->expects($this->any())->method('create')->will($this->returnValue($wrongController));
        $this->setExpectedException(
            'InvalidArgumentException',
            'The specified class is not a valid API action controller.'
        );
        $this->_factory->createActionController('ClassName', $request);
    }
}
