<?php
use Zend\Soap\Wsdl;

/**
 * SOAP AutoDiscover tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_AutoDiscoverTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Webapi_Model_Soap_Wsdl */
    protected $_wsdlMock;

    /** @var Magento_Webapi_Model_Soap_AutoDiscover */
    protected $_autoDiscover;

    /** @var Magento_Core_Model_CacheInterface */
    protected $_cacheMock;

    /** @var Magento_Webapi_Model_Config_Soap */
    protected $_resourceConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheStateMock;

    protected function setUp()
    {
        /** Prepare arguments for SUT constructor. */
        $this->_resourceConfigMock = $this->getMockBuilder('Magento_Webapi_Model_Config_Soap')
            ->disableOriginalConstructor()->getMock();

        $this->_wsdlMock = $this->getMockBuilder('Magento_Webapi_Model_Soap_Wsdl')
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
        $wsdlFactory = $this->getMock(
            'Magento_Webapi_Model_Soap_Wsdl_Factory',
            array('create'),
            array(new Magento_ObjectManager_ObjectManager())
        );
        $wsdlFactory->expects($this->any())->method('create')->will($this->returnValue($this->_wsdlMock));
        $helper = $this->getMock('Magento_Webapi_Helper_Config', array(), array(), '', false, false);
        $this->_cacheMock = $this->getMock('Magento_Core_Model_CacheInterface');
        $this->_cacheStateMock = $this->getMock('Magento_Core_Model_Cache_StateInterface');
        /** Initialize SUT. */
        $this->_autoDiscover = new Magento_Webapi_Model_Soap_AutoDiscover(
            $this->_resourceConfigMock,
            $wsdlFactory,
            $helper,
            $this->_cacheMock,
            $this->_cacheStateMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_wsdlMock);
        unset($this->_autoDiscover);
        unset($this->_cacheMock);
        unset($this->_resourceConfigMock);
        parent::tearDown();
    }

    /**
     * Positive test case. Generate simple resource WSDL.
     *
     * @dataProvider generateDataProvider()
     */
    public function testGenerate($resourceName, $methodName, $interface)
    {
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
     * Test handle method with loading WSDL from cache.
     */
    public function testHandleLoadWsdlFromCache()
    {
        /** Mock cache isEnabled method to return true. */
        $this->_cacheStateMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));
        /** Mock cache load method to return cache ID. */
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnArgument(0));
        $requestedResources = array(
            'res1' => 'v1',
            'res2' => 'v2'
        );
        $result = $this->_autoDiscover->handle($requestedResources, 'http://magento.host');
        /** Assert that handle method will return string that starts with WSDL. */
        $this->assertStringStartsWith(
            Magento_Webapi_Model_Soap_AutoDiscover::WSDL_CACHE_ID,
            $result,
            'Wsdl is not loaded from cache.'
        );
    }

    /**
     * Test handle method with exception.
     */
    public function testHandleWithException()
    {
        /** Mock cache isEnabled method to return false. */
        $this->_cacheStateMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));
        $requestedResources = array('res1' => 'v1');
        $exception = new LogicException('getResourceDataMerged Exception');
        $this->_resourceConfigMock->expects($this->once())->method('getResourceDataMerged')->will(
            $this->throwException($exception)
        );
        $this->setExpectedException(
            'Magento_Webapi_Exception',
            'getResourceDataMerged Exception',
            Magento_Webapi_Exception::HTTP_BAD_REQUEST
        );
        $this->_autoDiscover->handle($requestedResources, 'http://magento.host');
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
