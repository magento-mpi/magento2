<?php
use Zend\Soap\Wsdl;

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
        /** Prepare arguments for SUT constructor. */
        $resourceConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Config_Soap')
            ->setMethods(array('getTypeData'))
            ->disableOriginalConstructor()
            ->getMock();
        $wsdlMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Wsdl')
            ->disableOriginalConstructor()
            ->setMethods(array('addSchemaTypeSection', 'addService', 'addPortType', 'addBinding', 'addSoapBinding',
                'addElement', 'addComplexType', 'addMessage', 'addPortOperation', 'addBindingOperation',
                'addSoapOperation', 'toXML'))
            ->getMock();
        $wsdlFactory = $this->getMock('Mage_Webapi_Model_Soap_Wsdl_Factory',
            array('create'), array(new Magento_ObjectManager_Zend()));
        $wsdlFactory->expects($this->any())->method('create')->will($this->returnValue($wsdlMock));
        $helper = $this->getMock('Mage_Webapi_Helper_Data', array('__'));
        $helper->expects($this->any())->method('__')->will($this->returnArgument(0));
        /** Initialize SUT. */
        $autoDiscover = new Mage_Webapi_Model_Soap_AutoDiscover($resourceConfigMock, $wsdlFactory, $helper);

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
            ->with($bindingName, Wsdl::TYPES_NS . ':' . $portTypeName)
            ->will($this->returnValue($bindingDomMock));
        $wsdlMock->expects($this->once())
            ->method('addSoapBinding')
            ->with($bindingDomMock);
        $operationName = $autoDiscover->getOperationName($resourceName, $methodName);
        $inputMessageName = $autoDiscover->getInputMessageName($operationName);
        $outputMessageName = $autoDiscover->getOutputMessageName($operationName);
        $wsdlMock->expects($this->once())
            ->method('addPortOperation')
            ->with($portTypeDomMock, $operationName, Wsdl::TYPES_NS . ':' . $inputMessageName,
                Wsdl::TYPES_NS . ':'.$outputMessageName);
        $operationDomMock = $this->_getDomElementMock();
        $wsdlMock->expects($this->once())
            ->method('addBindingOperation')
            ->with($bindingDomMock, $operationName, array('use' => 'literal'), array('use' => 'literal'), false,
                SOAP_1_2)
            ->will($this->returnValue($operationDomMock));
        $wsdlMock->expects($this->once())
            ->method('addSoapOperation')
            ->with($operationDomMock, $operationName, SOAP_1_2);
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
        $endpointUrl = 'http://magento.host/api/soap/';
        $autoDiscover->generate($requestedResources, $endpointUrl);
    }

    /**
     * Data provider for generate() test
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
     * Create mock for DOMElement
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
