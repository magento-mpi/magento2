<?php
/**
 * SOAP AutoDiscover tests.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_AutoDiscoverTest extends PHPUnit_Framework_TestCase
{
    /**
     * Positive test case. Generate simple resource WSDL.
     *
     * @dataProvider generateDataProvider()
     */
    public function testGenerate($resourceName, $methodName, $interface)
    {
        $resourceConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Config_Resource')
            ->setMethods(array('getDataType'))
            ->disableOriginalConstructor()
            ->getMock();

        $wsdlMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Wsdl')
            ->disableOriginalConstructor()
            ->setMethods(array('addService', 'addPortType', 'addBinding', 'addSoapBinding', 'addServicePort',
                'addElement', 'addComplexTypeWithParameters', 'addMessage', 'addPortOperation', 'addBindingOperation',
                'addSoapOperation', 'toXML'))
            ->getMock();
        $endpointUrl = 'http://magento.host/api/soap/';
        $autoDiscover = new Mage_Webapi_Model_Soap_AutoDiscover(array(
            'resource_config' => $resourceConfigMock,
            'endpoint_url' => $endpointUrl,
            'wsdl' => $wsdlMock,
        ));
        $this->assertInstanceOf('Mage_Webapi_Model_Soap_AutoDiscover', $autoDiscover);

        $serviceDomMock = $this->_getDomElementMock();
        $wsdlMock->expects($this->once())
            ->method('addService')
            ->will($this->returnValue($serviceDomMock));
        $portTypeDomMock = $this->_getDomElementMock();
        $portTypeName = $autoDiscover->getPortTypeName($resourceName);
        $wsdlMock->expects($this->once())
            ->method('addPortType')
            ->with($portTypeName)
            ->will($this->returnValue($portTypeDomMock));
        $bindingDomMock = $this->_getDomElementMock();
        $bindingName = $autoDiscover->getBindingName($resourceName);
        $wsdlMock->expects($this->once())
            ->method('addBinding')
            ->with($bindingName, $portTypeName)
            ->will($this->returnValue($bindingDomMock));
        $wsdlMock->expects($this->once())
            ->method('addSoapBinding')
            ->with($bindingDomMock);
        $portName = $autoDiscover->getPortName($resourceName);
        $wsdlMock->expects($this->once())
            ->method('addServicePort')
            ->with($serviceDomMock, $portName, $bindingName, $endpointUrl);
        $operationName = $autoDiscover->getOperationName($resourceName, $methodName);
        $inputMessageName = $autoDiscover->getInputMessageName($operationName);
        $outputMessageName = $autoDiscover->getOutputMessageName($operationName);
        $wsdlMock->expects($this->once())
            ->method('addPortOperation')
            ->with($portTypeDomMock, $operationName, $inputMessageName, $outputMessageName);
        $operationDomMock = $this->_getDomElementMock();
        $wsdlMock->expects($this->once())
            ->method('addBindingOperation')
            ->with($bindingDomMock, $operationName, array('use' => 'literal'), array('use' => 'literal'))
            ->will($this->returnValue($operationDomMock));
        $wsdlMock->expects($this->once())
            ->method('addSoapOperation')
            ->with($operationDomMock, $operationName);
        $wsdlMock->expects($this->once())
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
        $autoDiscover->generate($requestedResources);
    }

    public static function generateDataProvider()
    {
        $simpleInterface = array(
            'in' => array(
                'parameters' => array(
                    'resource_id' => array(
                        'type' => 'int',
                        'required' => true,
                        'documentation' => 'Resource ID.{annotation:value}'
                            . '{callInfo:resource_aMethodB:returned:conditionally}'
                    ),
                    'optional' => array(
                        'type' => 'boolean',
                        'required' => false,
                        'default' => true,
                        'documentation' => 'Optional parameter.'
                            . '{callInfo:allCallsExcept(resource_aMethodB):requiredInput:no}'
                            . '{seeLink:http://google.com/:google link:for example}'
                    ),
                ),
            ),
            'out' => array(
                'parameters' => array(
                    'result' => array(
                        'type' => 'string',
                        'required' => true,
                        'documentation' => 'Operation result.{docInstructions:output:noDoc}'
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
        $arrayInterface = array(
            'in' => array(
                'parameters' => array(
                    'array_param' => array(
                        'type' => 'string[]',
                        'required' => true,
                        'documentation' => 'Array of strings.{docInstructions:input:noDoc}'
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
        $complexTypeInterface = array(
            'in' => array(
                'parameters' => array(
                    'complex_param' => array(
                        'type' => 'ComplexTypeA',
                        'required' => false,
                        'documentation' => 'Optional complex type param.'
                    ),
                ),
            ),
            'out' => array(
                'parameters' => array(
                    'result' => array(
                        'type' => 'ComplexTypeB',
                        'required' => true,
                        'documentation' => 'Operation result.'
                    )
                ),
            ),
        );

        return array(
            'Method with simple parameters' => array('resource_a', 'methodB', $simpleInterface),
            'One-way method' => array('resource_a', 'methodC', $oneWayInterface),
            'Method with array in parameters' => array('resource_a', 'methodD', $arrayInterface),
            'Method with complex type in parameters' => array('resource_a', 'methodE', $complexTypeInterface),
        );
    }

    protected function _getDomElementMock()
    {
        return $this->getMockBuilder('DOMElement')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGenerateMissingResourceConfig()
    {
        new Mage_Webapi_Model_Soap_AutoDiscover(array());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGenerateInvalidResourceConfig()
    {
        $stdObject = new stdClass();
        new Mage_Webapi_Model_Soap_AutoDiscover(array(
            'resource_config' => $stdObject
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGenerateMissingEndpointUrl()
    {
        $resourceConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Config_Resource')
            ->disableOriginalConstructor()
            ->getMock();
        new Mage_Webapi_Model_Soap_AutoDiscover(array(
            'resource_config' => $resourceConfigMock
        ));
    }
}
