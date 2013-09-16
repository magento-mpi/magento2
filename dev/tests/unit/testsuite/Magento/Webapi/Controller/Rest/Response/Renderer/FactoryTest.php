<?php
/**
 * Test Rest renderer factory class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Response_Renderer_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Rest\Response\Renderer\Factory */
    protected $_factory;

    /** @var \Magento\Webapi\Controller\Rest\Request */
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
        $this->_requestMock = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Request')
            ->disableOriginalConstructor()->getMock();
        /** Init SUT. */
        $this->_factory = new \Magento\Webapi\Controller\Rest\Response\Renderer\Factory(
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
        $rendererMock = $this->getMockBuilder('Magento\Webapi\Controller\Rest\Response\Renderer\Json')
            ->disableOriginalConstructor()
            ->getMock();
        /** Mock object to return mocked renderer. */
        $this->_objectManagerMock->expects($this->once())->method('get')->with(
            '\Magento\Webapi\Controller\Rest\Response\Renderer\Json'
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
                <model>\Magento\Webapi\Controller\Rest\Response\Renderer\Json</model>
            </default>
            <application_json>
                <type>application/json</type>
                <model>\Magento\Webapi\Controller\Rest\Response\Renderer\Json</model>
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
        try {
            $this->_factory->get();
            $this->fail("Exception is expected to be raised");
        } catch (\Magento\Webapi\Exception $e) {
            $exceptionMessage = 'Server cannot understand Accept HTTP header media type.';
            $this->assertInstanceOf('Magento\Webapi\Exception', $e, 'Exception type is invalid');
            $this->assertEquals($exceptionMessage, $e->getMessage(), 'Exception message is invalid');
            $this->assertEquals(
                \Magento\Webapi\Exception::HTTP_NOT_ACCEPTABLE,
                $e->getHttpCode(),
                'HTTP code is invalid'
            );
        }
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
            '\Magento\Webapi\Controller\Rest\Response\Renderer\Json'
        )->will($this->returnValue(new \Magento\Object()));

        $this->setExpectedException(
            'LogicException',
            'The renderer must implement "Magento\Webapi\Controller\Rest\Response\RendererInterface".'
        );
        $this->_factory->get();
    }
}
