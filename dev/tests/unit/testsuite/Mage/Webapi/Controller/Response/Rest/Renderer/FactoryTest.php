<?php
/**
 * Test Rest renderer factory class.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Response_Rest_Renderer_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Controller_Response_Rest_Renderer_Factory */
    protected $_factory;

    /** @var Mage_Webapi_Controller_Request_Rest */
    protected $_requestMock;

    /** @var Mage_Core_Model_Config */
    protected $_applicationConfigMock;

    /** @var Magento_ObjectManager */
    protected $_objectManagerMock;

    protected function setUp()
    {
        /** Init dependencies for SUT. */
        $this->_objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')->disableOriginalConstructor()
            ->getMock();
        $this->_applicationConfigMock = $this->getMockBuilder('Mage_Core_Model_Config')->disableOriginalConstructor()
            ->getMock();
        $helperDataMock = $this->getMockBuilder('Mage_Webapi_Helper_Data')->disableOriginalConstructor()->getMock();
        $helperDataMock->expects($this->any())->method('__')->will($this->returnArgument(0));
        $helperFactoryMock = $this->getMockBuilder('Mage_Core_Model_Factory_Helper')->disableOriginalConstructor()
            ->getMock();
        $helperFactoryMock->expects($this->any())->method('get')->will($this->returnValue($helperDataMock));
        $this->_requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Request_Rest')->disableOriginalConstructor()
            ->getMock();
        /** Init SUT. */
        $this->_factory = new Mage_Webapi_Controller_Response_Rest_Renderer_Factory(
            $this->_objectManagerMock,
            $this->_applicationConfigMock,
            $helperFactoryMock,
            $this->_requestMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_factory);
        unset($this->_requestMock);
        unset($this->_applicationConfigMock);
        unset($this->_objectManagerMock);
        parent::tearDown();
    }

    /**
     * Test get method.
     */
    public function testGet()
    {
        $acceptTypes = array('application/json');
        $availableRenders = $this->_createConfigElementForRenders();
        /** Mock application config getNode method to return the list of renders. */
        $this->_applicationConfigMock->expects($this->once())->method('getNode')->will(
            $this->returnValue($availableRenders)
        );
        /** Mock request getAcceptTypes method to return specified value. */
        $this->_requestMock->expects($this->once())->method('getAcceptTypes')->will($this->returnValue($acceptTypes));
        /** Mock renderer. */
        $rendererMock = $this->getMockBuilder('Mage_Webapi_Controller_Response_Rest_Renderer_Json')
            ->disableOriginalConstructor()
            ->getMock();
        /** Mock object to return mocked renderer. */
        $this->_objectManagerMock->expects($this->once())->method('get')->with(
            'Mage_Webapi_Controller_Response_Rest_Renderer_Json'
        )->will($this->returnValue($rendererMock));
        $this->_factory->get();
    }

    protected function _createConfigElementForRenders()
    {
        /** Xml with the list of renders types and models. */
        $rendersXml = <<<XML
        <renders>
            <default>
                <type>*/*</type>
                <model>Mage_Webapi_Controller_Response_Rest_Renderer_Json</model>
            </default>
            <application_json>
                <type>application/json</type>
                <model>Mage_Webapi_Controller_Response_Rest_Renderer_Json</model>
            </application_json>
        </renders>
XML;
        /** Return Mage_Core_Model_Config_Element with stored renders data. */
        return new Mage_Core_Model_Config_Element($rendersXml);
    }

    /**
     * Test get method with wrong Accept Http Header.
     */
    public function testGetWithWrongAcceptHttpHeader()
    {
        /** Mock request to return empty Accept Types. */
        $this->_requestMock->expects($this->once())->method('getAcceptTypes')->will($this->returnValue(''));
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Server cannot understand Accept HTTP header media type.',
            Mage_Webapi_Exception::HTTP_NOT_ACCEPTABLE
        );
        $this->_factory->get();
    }

    /**
     * Test get method with wrong Renderer class.
     */
    public function testGetWithWrongRendererClass()
    {
        $acceptTypes = array('application/json');
        $availableRenders = $this->_createConfigElementForRenders();
        /** Mock application config getNode method to return the list of renders. */
        $this->_applicationConfigMock->expects($this->once())->method('getNode')->will(
            $this->returnValue($availableRenders)
        );
        /** Mock request getAcceptTypes method to return specified value. */
        $this->_requestMock->expects($this->once())->method('getAcceptTypes')->will($this->returnValue($acceptTypes));
        /** Mock object to return Varien_Object */
        $this->_objectManagerMock->expects($this->once())->method('get')->with(
            'Mage_Webapi_Controller_Response_Rest_Renderer_Json'
        )->will($this->returnValue(new Varien_Object()));

        $this->setExpectedException(
            'LogicException',
            'The renderer must implement "Mage_Webapi_Controller_Response_Rest_RendererInterface".'
        );
        $this->_factory->get();
    }
}
