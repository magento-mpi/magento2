<?php
/**
 * SOAP AutoDiscover integration tests.
 *
 * @copyright {}
 */

/**#@+
 * Data structure should be available to auto loader. However, the file name cannot be identified from class name.
 */
include __DIR__ . '/../../_files/controllers/AutoDiscover/ModuleB/DataStructure.php';
include __DIR__ . '/../../_files/controllers/AutoDiscover/ModuleB/Subresource/DataStructure.php';
/**#@-*/

/**
 * Class for {@see Mage_Webapi_Model_Soap_AutoDiscover} model testing.
 */
class Mage_Webapi_Model_Soap_AutoDiscoverTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Model_Config_Resource */
    protected $_config;

    /** @var Mage_Webapi_Model_Soap_AutoDiscover */
    protected $_autoDiscover;

    /**
     * Name of the resource under the test.
     *
     * @var string
     */
    protected $_resourceName;

    /**
     * Resource under the test data from config.
     *
     * @var array
     */
    protected $_resourceData;

    /**
     * DOMDocument containing generated WSDL.
     *
     * @var DOMDocument
     */
    protected $_dom;

    /**
     * DOMXpath containing generated WSDL DOM.
     *
     * @var DOMXPath
     */
    protected $_xpath;

    /**
     * Set up config with fixture controllers directory scanner
     */
    public function setUp()
    {
        $fixtureDir = __DIR__ . '/../../_files/controllers/AutoDiscover/';
        $directoryScanner = new \Zend\Code\Scanner\DirectoryScanner($fixtureDir);

        $this->_config = new Mage_Webapi_Model_Config_Resource(array(
            'directoryScanner' => $directoryScanner
        ));
        $this->_autoDiscover = new Mage_Webapi_Model_Soap_AutoDiscover(array(
            'resource_config' => $this->_config,
            'endpoint_url' => 'http://magento.host/api/soap/'
        ));

        $this->_resourceName = 'vendorModuleB';
        $this->_resourceData = $this->_config->getResource($this->_resourceName, 'v1');
        $xml = $this->_autoDiscover->generate(array($this->_resourceName => $this->_resourceData));
        $this->_dom = new DOMDocument('1.0', 'utf-8');
        $this->_dom->loadXML($xml);
        $this->_xpath = new DOMXPath($this->_dom);
    }

    /**
     * Clean up.
     */
    protected function tearDown()
    {
        unset($this->_config);
        unset($this->_autoDiscover);
        unset($this->_dom);
        unset($this->_xpath);
        unset($this->_resourceData);
        unset($this->_resourceName);
    }

    /**
     * Test WSDL operations Generation.
     * Generate WSDL XML using AutoDiscover and prepared config.
     * Walk through all methods from "vendorModuleB resource" (_files/controllers/AutoDiscover/ModuleBController.php)
     * Assert that service, portType and binding has been generated correctly for resource.
     * Assert that each method from controller has generated operations in portType and binding nodes.
     * Assert that each method has input and output messages and complexTypes generated correctly.
     */
    public function testGenerateOperations()
    {
        $wsdlNs = Mage_Webapi_Model_Soap_Wsdl::WSDL_NS;
        $tns = Mage_Webapi_Model_Soap_Wsdl::TYPES_NS;
        $xsdNs = Mage_Webapi_Model_Soap_Wsdl::XSD_NS;

        $this->_assertServiceNode();
        $binding = $this->_assertBinding();
        $portType = $this->_assertPortType();

        foreach ($this->_resourceData['methods'] as $methodName => $methodData) {
            $operationName = $this->_autoDiscover->getOperationName($this->_resourceName, $methodName);
            $operationXpath = sprintf('%s:operation[@name="%s"]', Mage_Webapi_Model_Soap_Wsdl::WSDL_NS, $operationName);
            // Assert binding operation
            /** @var DOMElement $bindingOperation */
            $bindingOperation = $this->_xpath->query($operationXpath, $binding)->item(0);
            $this->assertNotNull($bindingOperation, sprintf('Operation "%s" was not found in binding "%s".',
                $operationName, $this->_autoDiscover->getBindingName($this->_resourceName)));
            // Assert portType operation
            /** @var DOMElement $portOperation */
            $portOperation = $this->_xpath->query($operationXpath, $portType)->item(0);
            $this->assertNotNull($portOperation, sprintf('Operation "%s" was not found in portType "%s".',
                $operationName, $this->_autoDiscover->getPortTypeName($this->_resourceName)));
            // Assert portType operation input
            /** @var DOMElement $operationInput */
            $operationInput = $portOperation->getElementsByTagName('input')->item(0);
            $this->assertNotNull($operationInput, sprintf('Input node was not found in "%s" port operation.',
                $operationName));
            $inputMessageName = $this->_autoDiscover->getInputMessageName($operationName);
            $this->assertTrue($operationInput->hasAttribute('message'));
            $this->assertEquals(Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' .$inputMessageName,
                $operationInput->getAttribute('message'));
            $this->_assertMessage($inputMessageName);
            $requestTypeName = $this->_autoDiscover->getElementComplexTypeName($inputMessageName);
            $complexTypeXpath = "//{$wsdlNs}:types/{$xsdNs}:schema/{$xsdNs}:complexType[@name='%s']";
            /** @var DOMElement $request */
            $request = $this->_xpath->query(sprintf($complexTypeXpath, $requestTypeName))->item(0);
            $this->assertNotNull($request, sprintf('Request complex type for "%s" operation was not found in WSDL.',
                $methodName));
            $this->_assertDocumentation($methodData['documentation'], $request);
            // Assert parameters
            if (isset($methodData['interface']['in'])) {
                foreach ($methodData['interface']['in']['parameters'] as $parameterName => $parameterData) {
                    /** @var DOMElement $element */
                    $element = $this->_xpath->query("{$xsdNs}:sequence/{$xsdNs}:element[@name='{$parameterName}']",
                        $request)->item(0);
                    $this->assertNotNull($element, sprintf('Element for parameter "%s" was not found in "%s".',
                        $parameterName, $requestTypeName));
                    $paramType = $parameterData['type'];
                    $expectedTypeNs = $this->_config->isTypeSimple($paramType) ? $xsdNs : $tns;
                    $this->assertEquals($expectedTypeNs . ':' . $paramType, $element->getAttribute('type'));
                    $this->assertEquals($parameterData['required'] ? 1 : 0, $element->getAttribute('minOccurs'));
                    $this->assertEquals(1, $element->getAttribute('maxOccurs'));
                    $this->_assertDocumentation($parameterData['documentation'], $element);
                    /** @var DOMElement $callInfo */
                    $callInfo = $this->_xpath->query("{$xsdNs}:annotation/{$xsdNs}:appinfo/callInfo", $element)
                        ->item(0);
                    $this->assertNotNull($callInfo, sprintf('callInfo node was not found in "%s" annotation.',
                        $parameterName));
                    $callNameNodes = $callInfo->getElementsByTagName('callName');
                    $this->assertEquals(1, $callNameNodes->length);
                    /** @var DOMElement $callName */
                    $callName = $callNameNodes->item(0);
                    $this->assertEquals($operationName, $callName->nodeValue);
                    $requiredInput = $this->_xpath->query('requiredInput', $callInfo)->item(0);
                    $this->assertNotNull($requiredInput, sprintf(
                        '"requiredInput" node was not found in "%s" callInfo.', $parameterName));
                    $this->assertEquals($parameterData['required'] ? 'Yes' : 'No', $requiredInput->nodeValue);
                }
            }
            // Assert portType operation output
            if (isset($methodData['interface']['out'])) {
                /** @var DOMElement $operationOutput */
                $operationOutput = $portOperation->getElementsByTagName('output')->item(0);
                $this->assertNotNull($operationOutput, sprintf('Output node was not found in "%s" port operation.',
                    $operationName));
                $outputMessageName = $this->_autoDiscover->getOutputMessageName($operationName);
                $this->assertTrue($operationOutput->hasAttribute('message'));
                $this->assertEquals(Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $outputMessageName,
                    $operationOutput->getAttribute('message'));
                $this->_assertMessage($outputMessageName);
                $responseTypeName = $this->_autoDiscover->getElementComplexTypeName($outputMessageName);
                /** @var DOMElement $response */
                $response = $this->_xpath->query(sprintf($complexTypeXpath, $responseTypeName))->item(0);
                $this->assertNotNull($response, sprintf(
                    'Response complex type for "%s" operation was not found in WSDL.', $methodName));
                // Assert annotation documentation
                $expectedDocumentation = sprintf('Response container for the %s call.', $operationName);
                $this->_assertDocumentation($expectedDocumentation, $response);
            }
        }
    }

    /**
     * Test that complexType for DataStructures has been generated correctly in WSDL.
     * See /_files/controllers/AutoDiscover/ModuleB/DataStructure.php
     */
    public function testGenerateDataStructureComplexTypes()
    {
        $wsdlNs = Mage_Webapi_Model_Soap_Wsdl::WSDL_NS;
        $xsdNs = Mage_Webapi_Model_Soap_Wsdl::XSD_NS;

        $dataStructureName = 'VendorModuleBDataStructure';
        $typeData = $this->_config->getDataType($dataStructureName);
        $complexTypeXpath = "//{$wsdlNs}:types/{$xsdNs}:schema/{$xsdNs}:complexType[@name='%s']";
        /** @var DOMElement $dataStructure */
        $dataStructure = $this->_xpath->query(sprintf($complexTypeXpath, $dataStructureName))->item(0);
        $this->assertNotNull($dataStructure, sprintf('Complex type for data structure "%s" was not found in WSDL.',
            $dataStructureName));
        $this->_assertDocumentation($typeData['documentation'], $dataStructure);
        // Expected appinfo tags. See Vendor_ModuleB_Webapi_ModuleB_DataStructure properties docBlock.
        $expectedAppinfo = array(
            'stringParam' => array(
                'maxLength' => '255 chars.',
                'callInfo' => array(
                    'vendorModuleBUpdate' => array('requiredInput' => 'Yes'),
                    'vendorModuleBCreate' => array('requiredInput' => 'Conditionally'),
                    'vendorModuleBGet' => array('returned' => 'Always'),
                ),
            ),
            'integerParam' => array(
                'default' => $typeData['parameters']['integerParam']['default'],
                'min' => 10,
                'max' => 100,
                'callInfo' => array(
                    'vendorModuleBCreate' => array('requiredInput' => 'No'),
                    'vendorModuleBUpdate' => array('requiredInput' => 'No'),
                    'allCallsExcept' => array('calls' => 'vendorModuleBUpdate', 'requiredInput' => 'Yes'),
                    'vendorModuleBGet' => array('returned' => 'Conditionally'),
                ),
            ),
            'optionalBool' => array(
                'default' => 'false',
                'summary' => 'this is summary',
                'seeLink' => array(
                    'url' => 'http://google.com/',
                    'title' => 'link title',
                    'for' => 'link for',
                ),
                'docInstructions' => array('output' => 'noDoc'),
                'callInfo' => array(
                    'vendorModuleBCreate' => array('requiredInput' => 'No'),
                    'vendorModuleBUpdate' => array('requiredInput' => 'No'),
                    'vendorModuleBGet' => array('returned' => 'Conditionally'),
                ),
            ),
            'optionalComplexType' => array(
                'tagStatus' => 'some status',
                'callInfo' => array(
                    'vendorModuleBCreate' => array('requiredInput' => 'No'),
                    'vendorModuleBUpdate' => array('requiredInput' => 'No'),
                    'vendorModuleBGet' => array('returned' => 'Conditionally'),
                ),
            ),
        );

        foreach ($typeData['parameters'] as $parameterName => $parameterData) {
            // remove all appinfo placeholders from expected doc.
            $expectedDoc = trim(preg_replace('/(\{.*\}|\\r)/U', '', $parameterData['documentation']));
            $this->_assertParameter($parameterName, $parameterData['type'], $parameterData['required'],
                $expectedDoc, $expectedAppinfo[$parameterName], $dataStructure);
        }
    }

    /**
     * Assert parameter data.
     *
     * @param string $expectedName
     * @param string $expectedType
     * @param string $expectedIsRequired
     * @param string $expectedDoc
     * @param array $expectedAppinfo
     * @param DOMElement $complexType with actual parameter element.
     */
    protected function _assertParameter($expectedName, $expectedType, $expectedIsRequired, $expectedDoc,
        $expectedAppinfo, DOMElement $complexType
    ) {
        $xsdNs = Mage_Webapi_Model_Soap_Wsdl::XSD_NS;
        $tns = Mage_Webapi_Model_Soap_Wsdl::TYPES_NS;
        /** @var DOMElement $parameterElement */
        $parameterElement = $this->_xpath->query("{$xsdNs}:sequence/{$xsdNs}:element[@name='{$expectedName}']",
            $complexType)->item(0);
        $this->assertNotNull($parameterElement, sprintf('"%s" element was not found in complex type "%s".',
            $expectedName, $complexType->getAttribute('name')));
        $this->assertEquals($expectedIsRequired ? 1 : 0, $parameterElement->getAttribute('minOccurs'));
        $expectedNs = $this->_config->isTypeSimple($expectedType) ? $xsdNs : $tns;
        $this->assertEquals("{$expectedNs}:{$expectedType}", $parameterElement->getAttribute('type'));
        $this->_assertDocumentation($expectedDoc, $parameterElement);
        $this->_assertAppinfo($expectedAppinfo, $parameterElement);
    }

    /**
     * Assert appinfo nodes in given element.
     *
     * @param array $expectedAppinfo
     * @param DOMElement $element with actual appinfo node
     */
    protected function _assertAppinfo($expectedAppinfo, DOMElement $element)
    {
        $xsdNs = Mage_Webapi_Model_Soap_Wsdl::XSD_NS;
        /** @var DOMElement $appInfoNode */
        $appInfoNode = $this->_xpath->query("{$xsdNs}:annotation/{$xsdNs}:appinfo", $element)->item(0);
        $elementName = $element->getAttribute('name');
        $this->assertNotNull($appInfoNode, sprintf('"appinfo" node not found in "%s" element.', $elementName));

        foreach ($expectedAppinfo as $appInfoKey => $appInfo) {
            switch ($appInfoKey) {
                case 'callInfo':
                    foreach ($appInfo as $callName => $callData) {
                        if ($callName == 'allCallsExcept') {
                            /** @var DOMElement $callNode */
                            $callNode = $this->_xpath->query("callInfo/allCallsExcept[text()='{$callData['calls']}']",
                                $appInfoNode)->item(0);
                            $this->assertNotNull($callNode,
                                sprintf('allCallsExcept node for call "%s" was not found in element "%s" appinfo.',
                                    $callData['calls'], $elementName));
                        } else {
                            /** @var DOMElement $callNameNode */
                            $callNode = $this->_xpath->query("callInfo/callName[text()='{$callName}']", $appInfoNode)
                                ->item(0);
                            $this->assertNotNull($callNode,
                                sprintf('callName node for call "%s" was not found in element "%s" appinfo.', $callName,
                                    $elementName));
                        }
                        $callInfoNode = $callNode->parentNode;
                        if (isset($callData['requiredInput'])) {
                            $direction = 'requiredInput';
                            $condition = $callData['requiredInput'];
                        } else if (isset($callData['returned'])) {
                            $direction = 'returned';
                            $condition = $callData['returned'];
                        }
                        $conditionNode = $this->_xpath->query("{$direction}[text()='{$condition}']", $callInfoNode)
                            ->item(0);
                        $this->assertNotNull($conditionNode,
                            sprintf('"%s" node with value "%s" not found for callName "%s" in element "%s"',
                                $direction, $condition, $callName, $elementName));
                    }
                    break;
                case 'seeLink':
                    /** @var DOMElement $seeLinkNode */
                    $seeLinkNode = $this->_xpath->query('seeLink', $appInfoNode)->item(0);
                    $this->assertNotNull($seeLinkNode, sprintf('"seeLink" node was not found in "%s" element appinfo.',
                        $elementName));
                    foreach (array('url', 'title', 'for') as $subNodeName) {
                        if (isset($appInfo[$subNodeName])) {
                            /** @var DOMElement $subNode */
                            $subNodeValue = $appInfo[$subNodeName];
                            $subNode = $this->_xpath->query("{$subNodeName}[text()='{$subNodeValue}']",
                                $seeLinkNode)->item(0);
                            $this->assertNotNull($subNode,
                                sprintf('"%s" node with value "%s" was not found in "%s" element appinfo "seeLink".',
                                    $subNodeName, $subNodeValue, $elementName));
                        }
                    }
                    break;
                case 'docInstructions':
                    /** @var DOMElement $docInstructionsNode */
                    $docInstructionsNode = $this->_xpath->query('docInstructions', $appInfoNode)->item(0);
                    $this->assertNotNull($docInstructionsNode,
                        sprintf('"docInstructions" node was not found in "%s" element appinfo.', $elementName));
                    foreach ($appInfo as $direction => $value) {
                        /** @var DOMElement $subNode */
                        $subNode = $this->_xpath->query("{$direction}/{$value}", $docInstructionsNode)->item(0);
                        $this->assertNotNull($subNode,
                            sprintf('"%s/%s" node  was not found in "%s" element appinfo "docInstructions".',
                                $direction, $value, $elementName));
                    }
                    break;
                default:
                    $tagNode = $this->_xpath->query($appInfoKey, $appInfoNode)
                        ->item(0);
                    $this->assertNotNull($tagNode, sprintf('Appinfo node "%s" was not found in element "%s"',
                        $appInfoKey, $elementName));
                    $this->assertEquals($appInfo, $tagNode->nodeValue, sprintf('Appinfo node "%s" is not correct.',
                        $appInfoKey));
                    break;
            }
        }
    }

    /**
     * Assert that given complex type has correct documentation node.
     *
     * @param string $expectedDocumentation
     * @param DOMElement $element
     */
    protected function _assertDocumentation($expectedDocumentation, DOMElement $element)
    {
        $elementName = $element->getAttribute('name');
        $xsdNs = Mage_Webapi_Model_Soap_Wsdl::XSD_NS;
        /** @var DOMElement $documentation */
        $documentation = $this->_xpath->query("{$xsdNs}:annotation/{$xsdNs}:documentation", $element)->item(0);
        $this->assertNotNull($documentation,
            sprintf('"annotation/documentation" node was not found inside "%s" element.', $elementName));
        $this->assertEquals($expectedDocumentation, $documentation->nodeValue,
            sprintf('"documentation" node value is incorrect in "%s" element.', $elementName));
    }

    /**
     * Assert message is present in WSDL.
     *
     * @param string $messageName
     */
    protected function _assertMessage($messageName)
    {
        $wsdlNs = Mage_Webapi_Model_Soap_Wsdl::WSDL_NS;
        $tns = Mage_Webapi_Model_Soap_Wsdl::TYPES_NS;
        $xsdNs = Mage_Webapi_Model_Soap_Wsdl::XSD_NS;

        /** @var DOMElement $message */
        $expression = "//{$wsdlNs}:message[@name='{$messageName}']";
        $message = $this->_xpath->query($expression)->item(0);
        $this->assertNotNull($message, sprintf('Message "%s" not found in WSDL.', $messageName));
        $partXpath = "{$wsdlNs}:part[@element='{$tns}:{$messageName}']";
        $messagePart = $this->_xpath->query($partXpath, $message)->item(0);
        $this->assertNotNull($messagePart, sprintf('Message part not found in "%s".', $messageName));

        $elementXpath = "//{$wsdlNs}:types/{$xsdNs}:schema/{$xsdNs}:element[@name='{$messageName}']";
        /** @var DOMElement $typeElement */
        $typeElement = $this->_xpath->query($elementXpath)->item(0);
        $this->assertNotNull($typeElement, sprintf('Message "%s" element not found in types.', $messageName));
        $requestComplexTypeName = $this->_autoDiscover->getElementComplexTypeName($messageName);
        $this->assertTrue($typeElement->hasAttribute('type'));
        $this->assertEquals($tns. ':' .$requestComplexTypeName, $typeElement->getAttribute('type'));
    }

    /**
     * Assert binding node is present and return it.
     *
     * @return DOMElement
     */
    protected function _assertBinding()
    {
        $bindings = $this->_dom->getElementsByTagNameNS(Mage_Webapi_Model_Soap_Wsdl::WSDL_NS_URI, 'binding');
        $this->assertEquals(1, $bindings->length, 'There should be only one binding in this test case.');
        /** @var DOMElement $binding */
        $binding = $bindings->item(0);
        $this->assertTrue($binding->hasAttribute('name'));
        $bindingName = $this->_autoDiscover->getBindingName($this->_resourceName);
        $this->assertEquals($bindingName, $binding->getAttribute('name'));
        $this->assertTrue($binding->hasAttribute('type'));
        $portTypeName = $this->_autoDiscover->getPortTypeName($this->_resourceName);
        $this->assertEquals(Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $portTypeName,
            $binding->getAttribute('type'));
        /** @var DOMElement $soapBinding */
        $soapBinding = $binding->getElementsByTagNameNS(Mage_Webapi_Model_Soap_Wsdl::SOAP_NS_URI, 'binding')->item(0);
        $this->assertNotNull($soapBinding, sprintf('Missing soap binding in "%s"', $bindingName));
        $this->assertTrue($soapBinding->hasAttribute('style'));
        $this->assertEquals('document', $soapBinding->getAttribute('style'));

        return $binding;
    }

    /**
     * Assert port type node is present and return it.
     *
     * @return DOMElement
     */
    protected function _assertPortType()
    {
        $portTypes = $this->_dom->getElementsByTagNameNs(Mage_Webapi_Model_Soap_Wsdl::WSDL_NS_URI, 'portType');
        $this->assertEquals(1, $portTypes->length, 'There should be only one portType in this test case.');
        /** @var DOMElement $portType */
        $portType = $portTypes->item(0);
        $this->assertTrue($portType->hasAttribute('name'));
        $expectedName = $this->_autoDiscover->getPortTypeName($this->_resourceName);
        $this->assertEquals($expectedName, $portType->getAttribute('name'));

        return $portType;
    }

    /**
     * Assert port node is present within service node.
     *
     * @param DOMElement $service
     */
    protected function _assertPortNode($service)
    {
        /** @var DOMElement $port */
        $port = $service->getElementsByTagName('port')->item(0);
        $this->assertNotNull($port, 'port node not found within service node.');
        $this->assertTrue($port->hasAttribute('name'));
        $this->assertEquals($this->_autoDiscover->getPortName($this->_resourceName), $port->getAttribute('name'));
        $bindingName = $this->_autoDiscover->getBindingName($this->_resourceName);
        $this->assertEquals(Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $bindingName, $port->getAttribute('binding'));
    }

    /**
     * Assert service node is present in xml.
     *
     * @return DOMElement
     */
    protected function _assertServiceNode()
    {
        /** @var DOMElement $service */
        $service = $this->_dom->getElementsByTagNameNS(Mage_Webapi_Model_Soap_Wsdl::WSDL_NS_URI, 'service')->item(0);
        $this->assertNotNull($service, 'service node not found in WSDL.');
        $this->assertTrue($service->hasAttribute('name'));
        $this->assertEquals(Mage_Webapi_Model_Soap_AutoDiscover::SERVICE_NAME, $service->getAttribute('name'));

        $this->_assertPortNode($service, $this->_resourceName);
    }
}
