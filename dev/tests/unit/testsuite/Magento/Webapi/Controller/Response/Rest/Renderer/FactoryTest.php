<?php
/**
 * Test Rest renderer factory class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Response_Rest_Renderer_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Controller_Response_Rest_Renderer_Factory */
    protected $_factory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_requestMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_requestMock = $this->getMockBuilder('Magento_Webapi_Controller_Request_Rest')
            ->disableOriginalConstructor()
            ->getMock();
        
        $renders = array(
            'default' => array(
                'type' => '*/*',
                'model' => 'Magento_Webapi_Controller_Response_Rest_Renderer_Json'
            ),
            'application_json' => array(
                'type' => 'application/json',
                'model' => 'Magento_Webapi_Controller_Response_Rest_Renderer_Json'
            )
        );

        $this->_factory = new Magento_Webapi_Controller_Response_Rest_Renderer_Factory(
            $this->_objectManagerMock,
            $this->_requestMock,
            $renders
        );
    }

    /**
     * Test GET method.
     */
    public function testGet()
    {
        $acceptTypes = array('application/json');

        /** Mock request getAcceptTypes method to return specified value. */
        $this->_requestMock->expects($this->once())->method('getAcceptTypes')->will($this->returnValue($acceptTypes));
        /** Mock renderer. */
        $rendererMock = $this->getMockBuilder('Magento_Webapi_Controller_Response_Rest_Renderer_Json')
            ->disableOriginalConstructor()
            ->getMock();
        /** Mock object to return mocked renderer. */
        $this->_objectManagerMock->expects($this->once())->method('get')->with(
            'Magento_Webapi_Controller_Response_Rest_Renderer_Json'
        )->will($this->returnValue($rendererMock));
        $this->_factory->get();
    }

    /**
     * Test GET method with wrong Accept HTTP Header.
     */
    public function testGetWithWrongAcceptHttpHeader()
    {
        /** Mock request to return empty Accept Types. */
        $this->_requestMock->expects($this->once())->method('getAcceptTypes')->will($this->returnValue(''));
        $this->setExpectedException(
            'Magento_Webapi_Exception',
            'Server cannot understand Accept HTTP header media type.',
            Magento_Webapi_Exception::HTTP_NOT_ACCEPTABLE
        );
        $this->_factory->get();
    }

    /**
     * Test GET method with wrong Renderer class.
     */
    public function testGetWithWrongRendererClass()
    {
        $acceptTypes = array('application/json');
        /** Mock request getAcceptTypes method to return specified value. */
        $this->_requestMock->expects($this->once())->method('getAcceptTypes')->will($this->returnValue($acceptTypes));
        /** Mock object to return Magento_Object */
        $this->_objectManagerMock->expects($this->once())->method('get')->with(
            'Magento_Webapi_Controller_Response_Rest_Renderer_Json'
        )->will($this->returnValue(new Magento_Object()));

        $this->setExpectedException(
            'LogicException',
            'The renderer must implement "Magento_Webapi_Controller_Response_Rest_RendererInterface".'
        );
        $this->_factory->get();
    }
}
