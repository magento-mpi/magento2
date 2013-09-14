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
    /** @var \Magento\Webapi\Controller\Response\Rest\Renderer\Factory */
    protected $_factory;

    /** @var \Magento\Webapi\Controller\Request\Rest */
    protected $_requestMock;

    /** @var \Magento\Core\Model\Config */
    protected $_applicationMock;

    /** @var \Magento\ObjectManager */
    protected $_objectManagerMock;

    protected function setUp()
    {
        /** Init dependencies for SUT. */
        $this->_objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')->disableOriginalConstructor()
            ->getMock();
        $this->_applicationMock = $this->getMockBuilder('Magento\Core\Model\Config')->disableOriginalConstructor()
            ->getMock();

        $this->_requestMock = $this->getMockBuilder('Magento\Webapi\Controller\Request\Rest')
            ->disableOriginalConstructor()
            ->getMock();
        /** Init SUT. */
        $this->_factory = new \Magento\Webapi\Controller\Response\Rest\Renderer\Factory(
            $this->_objectManagerMock,
            $this->_applicationMock,
            $this->_requestMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_factory);
        unset($this->_requestMock);
        unset($this->_applicationMock);
        unset($this->_objectManagerMock);
        parent::tearDown();
    }

    /**
     * Test GET method.
     */
    public function testGet()
    {
        $acceptTypes = array('application/json');
        $availableRenders = $this->_createConfigElementForRenders();
        /** Mock application config getNode method to return the list of renders. */
        $this->_applicationMock->expects($this->once())->method('getNode')->will(
            $this->returnValue($availableRenders)
        );
        /** Mock request getAcceptTypes method to return specified value. */
        $this->_requestMock->expects($this->once())->method('getAcceptTypes')->will($this->returnValue($acceptTypes));
        /** Mock renderer. */
        $rendererMock = $this->getMockBuilder('Magento\Webapi\Controller\Response\Rest\Renderer\Json')
            ->disableOriginalConstructor()
            ->getMock();
        /** Mock object to return mocked renderer. */
        $this->_objectManagerMock->expects($this->once())->method('get')->with(
            'Magento\Webapi\Controller\Response\Rest\Renderer\Json'
        )->will($this->returnValue($rendererMock));
        $this->_factory->get();
    }

    protected function _createConfigElementForRenders()
    {
        /** XML with the list of renders types and models. */
        $rendersXml = <<<XML
        <renders>
            <default>
                <type>*/*</type>
                <model>\Magento\Webapi\Controller\Response\Rest\Renderer\Json</model>
            </default>
            <application_json>
                <type>application/json</type>
                <model>\Magento\Webapi\Controller\Response\Rest\Renderer\Json</model>
            </application_json>
        </renders>
XML;
        /** Return \Magento\Core\Model\Config\Element with stored renders data. */
        return new \Magento\Core\Model\Config\Element($rendersXml);
    }

    /**
     * Test GET method with wrong Accept HTTP Header.
     */
    public function testGetWithWrongAcceptHttpHeader()
    {
        /** Mock request to return empty Accept Types. */
        $this->_requestMock->expects($this->once())->method('getAcceptTypes')->will($this->returnValue(''));
        $this->setExpectedException(
            'Magento\Webapi\Exception',
            'Server cannot understand Accept HTTP header media type.',
            \Magento\Webapi\Exception::HTTP_NOT_ACCEPTABLE
        );
        $this->_factory->get();
    }

    /**
     * Test GET method with wrong Renderer class.
     */
    public function testGetWithWrongRendererClass()
    {
        $acceptTypes = array('application/json');
        $availableRenders = $this->_createConfigElementForRenders();
        /** Mock application config getNode method to return the list of renders. */
        $this->_applicationMock->expects($this->once())->method('getNode')->will(
            $this->returnValue($availableRenders)
        );
        /** Mock request getAcceptTypes method to return specified value. */
        $this->_requestMock->expects($this->once())->method('getAcceptTypes')->will($this->returnValue($acceptTypes));
        /** Mock object to return \Magento\Object */
        $this->_objectManagerMock->expects($this->once())->method('get')->with(
            'Magento\Webapi\Controller\Response\Rest\Renderer\Json'
        )->will($this->returnValue(new \Magento\Object()));

        $this->setExpectedException(
            'LogicException',
            'The renderer must implement "Magento\Webapi\Controller\Response\Rest\RendererInterface".'
        );
        $this->_factory->get();
    }
}
