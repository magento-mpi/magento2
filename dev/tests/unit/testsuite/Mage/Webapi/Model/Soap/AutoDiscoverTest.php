<?php
use Zend\Soap\Wsdl;

/**
 * Test SOAP Mage_Webapi_Model_Soap_AutoDiscover
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Soap_AutoDiscoverTest extends PHPUnit_Framework_TestCase
{

    /**  @var Mage_Webapi_Model_Soap_AutoDiscover * */
    protected $_autoDiscover;
    /**  @var Mage_Webapi_Config * */
    protected $_newApiConfigMock;
    /**  @var Mage_Webapi_Model_Config_Soap * */
    protected $_apiConfig;
    /**  @var Mage_Webapi_Model_Soap_Wsdl_Factory * */
    protected $_wsdlFactory;
    /**  @var Mage_Webapi_Helper_Config * */
    protected $_helper;
    /**  @var Mage_Core_Model_CacheInterface * */
    protected $_cache;
    /** @var Mage_Webapi_Model_Soap_Wsdl */
    protected $_wsdlMock;


    protected function setUp()
    {

        $_newApiConfigMock = $this->getMockBuilder('Mage_Webapi_Config')->disableOriginalConstructor()
            ->getMock();
        $_apiConfig = $this->getMockBuilder('Mage_Webapi_Model_Config_Soap')->disableOriginalConstructor()->getMock();
        $_wsdlFactory = $this->getMockBuilder('Mage_Webapi_Model_Soap_Wsdl_Factory')->disableOriginalConstructor()
            ->getMock();
        $_helper = $this->getMockBuilder('Mage_Webapi_Helper_Config')->disableOriginalConstructor()->getMock();
        $_cache = $this->getMockBuilder('Mage_Core_Model_CacheInterface')->disableOriginalConstructor()->getMock();

        $this->_autoDiscover = new Mage_Webapi_Model_Soap_AutoDiscover(
            $_newApiConfigMock,
            $_apiConfig,
            $_wsdlFactory,
            $_helper,
            $_cache
        );

        $this->_wsdlMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Wsdl')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'addSchemaTypeSection',
                    'addService',
                    'addPortType',
                    'addBinding',
                    'addSoapBinding',
                    'addElement',
                    'addComplexType',
                    'addMessage',
                    'addPortOperation',
                    'addBindingOperation',
                    'addSoapOperation',
                    'toXML'
                )
            )
            ->getMock();

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_autoDiscover);
        unset($this->_apiConfig);
        unset($this->_wsdlFactory);
        unset($this->_helper);
        unset($this->_cache);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function handle()
    {
        $resData = 'resourceData';
        $genWSDL = 'generatedWSDL';
        $requestedResource = array(
            'catalogProduct' => 'V1',
        );

        $partialMockedAutoDis = $this->getMockBuilder(
            'Mage_Webapi_Model_Soap_AutoDiscover'
        )
            ->setMethods(array('_prepareResourceData', 'generate'))
            ->disableOriginalConstructor()
            ->getMock();

        $partialMockedAutoDis->expects($this->once())->method('_prepareResourceData')->will(
            $this->returnValue($resData)
        );
        $partialMockedAutoDis->expects($this->once())->method('generate')->will(
            $this->returnValue($genWSDL)
        );
        $this->assertEquals($genWSDL, $partialMockedAutoDis->handle($requestedResource, 'http://magento.host'));
    }

    /**
     * @test
     * @expectedException        Mage_Webapi_Exception
     * @expectedExceptionMessage exception message
     */
    public function handleWithException()
    {
        $genWSDL = 'generatedWSDL';
        $requestedResource = array(
            'catalogProduct' => 'V1',
        );

        $partialMockedAutoDis = $this->getMockBuilder(
            'Mage_Webapi_Model_Soap_AutoDiscover'
        )
            ->setMethods(array('_prepareResourceData', 'generate'))
            ->disableOriginalConstructor()
            ->getMock();

        $partialMockedAutoDis->expects($this->once())->method('_prepareResourceData')->will(
            $this->throwException(new Exception("exception message"))
        );

        //TODO: Need to verify if generate can throw an exception too
        // $partialMockedAutoDis->expects($this->once())->method('generate')->will(
        //     $this->returnValue($genWSDL)
        // );

        $this->assertEquals($genWSDL, $partialMockedAutoDis->handle($requestedResource, 'http://magento.host'));
    }


    /**
     * @test
     */
    public function getElementComplexTypeName()
    {
        $this->assertEquals("Test", $this->_autoDiscover->getElementComplexTypeName("test"));
    }

    /**
     * @test
     */
    public function getPortTypeName()
    {
        $this->assertEquals("testPortType", $this->_autoDiscover->getPortTypeName("test"));
    }

    /**
     * @test
     */
    public function getBindingName()
    {
        $this->assertEquals("testBinding", $this->_autoDiscover->getBindingName("test"));
    }

    /**
     * @test
     */
    public function getPortName()
    {
        $this->assertEquals("testPort", $this->_autoDiscover->getPortName("test"));
    }

    /**
     * @test
     */
    public function getServiceName()
    {
        $this->assertEquals("testService", $this->_autoDiscover->getServiceName("test"));
    }

    /**
     * @test
     */
    public function getOperationName()
    {
        $this->assertEquals("resNameMethodName", $this->_autoDiscover->getOperationName("resName", "methodName"));
    }

    /**
     * @test
     */
    public function getInputMessageName()
    {
        $this->assertEquals("operationNameRequest", $this->_autoDiscover->getInputMessageName("operationName"));
    }

    /**
     * @test
     */
    public function getOutputMessageName()
    {
        $this->assertEquals("operationNameResponse", $this->_autoDiscover->getOutputMessageName("operationName"));
    }


    /**
     * Positive test case. Generate simple resource WSDL.
     *
     * @test
     * @dataProvider generateDataProvider()
     */
    public function generate($resourceName, $methodName, $interface)
    {
        $this->markTestIncomplete('Not implemented as yet');
        $serviceDomMock = $this->_getDomElementMock();
        $this->_wsdlMock->expects($this->once())->method('addService')->will($this->returnValue($serviceDomMock));
        $portTypeDomMock = $this->_getDomElementMock();
        $portTypeName = $this->_autoDiscover->getPortTypeName($resourceName);
        $this->_wsdlMock->expects($this->once())
            ->method('addPortType')
            ->with($portTypeName)
            ->will($this->returnValue($portTypeDomMock));
        $bindingDomMock = $this->_getDomElementMock();
        $bindingName = $this->_autoDiscover->getBindingName($resourceName);
        $this->_wsdlMock->expects($this->once())
            ->method('addBinding')
            ->with($bindingName, Wsdl::TYPES_NS . ':' . $portTypeName)
            ->will($this->returnValue($bindingDomMock));
        $this->_wsdlMock->expects($this->once())
            ->method('addSoapBinding')
            ->with($bindingDomMock);
        $operationName = $this->_autoDiscover->getOperationName($resourceName, $methodName);
        $inputMessageName = $this->_autoDiscover->getInputMessageName($operationName);
        $outputMessageName = $this->_autoDiscover->getOutputMessageName($operationName);
        $this->_wsdlMock->expects($this->once())
            ->method('addPortOperation')
            ->with(
                $portTypeDomMock,
                $operationName,
                Wsdl::TYPES_NS . ':' . $inputMessageName,
                Wsdl::TYPES_NS . ':' . $outputMessageName
            );
        $operationDomMock = $this->_getDomElementMock();
        $this->_wsdlMock->expects($this->once())
            ->method('addBindingOperation')
            ->with(
                $bindingDomMock,
                $operationName,
                array('use' => 'literal'),
                array('use' => 'literal'),
                false,
                SOAP_1_2
            )
            ->will($this->returnValue($operationDomMock));
        $this->_wsdlMock->expects($this->once())
            ->method('addSoapOperation')
            ->with($operationDomMock, $operationName, SOAP_1_2);
        $this->_wsdlMock->expects($this->once())
            ->method('toXML');

        $requestedResources = array(
            $resourceName => array(
                'methods' => array(
                    $methodName => array(
                        'interface' => $interface,
                        'documentation' => 'test method A',
                    ),
                ),
            ),
        );
        $endpointUrl = 'http://magento.host/api/soap/';
        $this->_autoDiscover->generate($requestedResources, $endpointUrl);
    }

    /**
     * Data provider for generate() test.
     *
     * @return array
     */
    public static function generateDataProvider()
    {
        $simpleInterface = array(
            'in' => array(
                'parameters' => array(
                    'resource_id' => array(
                        'type' => 'int',
                        'required' => true,
                        'documentation' => 'Resource ID.{annotation:value}'
                    ),
                    'optional' => array(
                        'type' => 'boolean',
                        'required' => false,
                        'default' => true,
                        'documentation' => 'Optional parameter.'
                    ),
                ),
            ),
            'out' => array(
                'parameters' => array(
                    'result' => array(
                        'type' => 'string',
                        'required' => true,
                        'documentation' => 'Operation result.'
                    )
                ),
            ),
        );
        $oneWayInterface = array(
            'out' => array(
                'parameters' => array(
                    'result' => array(
                        'type' => 'string',
                        'required' => true,
                        'documentation' => 'Operation result.'
                    )
                ),
            ),
        );
        $complexTypeInterface = array(
            'in' => array(
                'parameters' => array(
                    'complex_param' => array(
                        'type' => 'ComplexTypeA',
                        'required' => true,
                        'documentation' => 'Optional complex type param.'
                    ),
                ),
            ),
            'out' => array(
                'parameters' => array(
                    'result' => array(
                        'type' => 'ComplexTypeB',
                        'required' => false,
                        'documentation' => 'Operation result.'
                    )
                ),
            ),
        );

        return array(
            'Method with simple parameters' => array('resource_a', 'methodB', $simpleInterface),
            'One-way method' => array('resource_a', 'methodC', $oneWayInterface),
            'Method with complex type in parameters' => array('resource_a', 'methodE', $complexTypeInterface),
        );
    }

    /**
     * Create mock for DOMElement.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getDomElementMock()
    {
        return $this->getMockBuilder('DOMElement')
            ->disableOriginalConstructor()
            ->getMock();
    }


}