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
    }

    /**
     * Test WSDL Generation.
     * Asserts that all WSDL nodes has been generated for given resource.
     */
    public function testGenerate()
    {
        $resourceName = 'vendorModuleB';
        $resourceData = $this->_config->getResource($resourceName, 'v1');
        $requestedResources = array(
            $resourceName => $resourceData
        );
        $xml = $this->_autoDiscover->generate($requestedResources);
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);

        $this->_assertServiceNode($dom, $resourceName);
        $binding = $this->_assertBinding($dom, $resourceName);
        $portType = $this->_assertPortType($dom, $resourceName);

        foreach ($resourceData['methods'] as $methodName => $methodData) {
            $operationName = $this->_autoDiscover->getOperationName($resourceName, $methodName);
            $operationXpath = sprintf('%s:operation[@name="%s"]', Mage_Webapi_Model_Soap_Wsdl::WSDL_NS, $operationName);
            // Assert binding operation
            /** @var DOMElement $bindingOperation */
            $bindingOperation = $xpath->query($operationXpath, $binding)->item(0);
            $this->assertNotNull($bindingOperation, sprintf('Operation "%s" not found in binding "%s".', $operationName,
                $this->_autoDiscover->getBindingName($resourceName)));
            // Assert portType operation
            /** @var DOMElement $portOperation */
            $portOperation = $xpath->query($operationXpath, $portType)->item(0);
            $this->assertNotNull($portOperation, sprintf('Operation "%s" not found in portType "%s".', $operationName,
                $this->_autoDiscover->getPortTypeName($resourceName)));
            // Assert portType operation input
            /** @var DOMElement $operationInput */
            $operationInput = $portOperation->getElementsByTagName('input')->item(0);
            $this->assertNotNull($operationInput, sprintf('Input node not found in "%s" port operation.',
                $operationName));
            $inputMessageName = $this->_autoDiscover->getInputMessageName($operationName);
            $this->assertTrue($operationInput->hasAttribute('message'));
            $this->assertEquals(Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' .$inputMessageName,
                $operationInput->getAttribute('message'));
            $this->_assertMessage($xpath, $inputMessageName);
            // Assert portType operation output
            if (isset($methodData['interface']['out'])) {
                /** @var DOMElement $operationOutput */
                $operationOutput = $portOperation->getElementsByTagName('output')->item(0);
                $this->assertNotNull($operationOutput, sprintf('Output node not found in "%s" port operation.',
                    $operationName));
                $outputMessageName = $this->_autoDiscover->getOutputMessageName($operationName);
                $this->assertTrue($operationOutput->hasAttribute('message'));
                $this->assertEquals(Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $outputMessageName,
                    $operationOutput->getAttribute('message'));
                $this->_assertMessage($xpath, $outputMessageName);
            }
        }
    }

    /**
     * Assert message is present in WSDL.
     *
     * @param DOMXPath $xpath
     * @param string $messageName
     */
    protected function _assertMessage($xpath, $messageName)
    {
        $wsdlNs = Mage_Webapi_Model_Soap_Wsdl::WSDL_NS;
        $tns = Mage_Webapi_Model_Soap_Wsdl::TYPES_NS;
        $xsdNs = Mage_Webapi_Model_Soap_Wsdl::XSD_NS;

        /** @var DOMElement $message */
        $expression = "//{$wsdlNs}:message[@name='{$messageName}']";
        $message = $xpath->query($expression)->item(0);
        $this->assertNotNull($message, sprintf('Message "%s" not found in WSDL.', $messageName));
        $partXpath = "{$wsdlNs}:part[@element='{$tns}:{$messageName}']";
        $messagePart = $xpath->query($partXpath, $message)->item(0);
        $this->assertNotNull($messagePart, sprintf('Message part not found in "%s".', $messageName));

        $elementXpath = "//{$wsdlNs}:types/{$xsdNs}:schema/{$xsdNs}:element[@name='{$messageName}']";
        /** @var DOMElement $typeElement */
        $typeElement = $xpath->query($elementXpath)->item(0);
        $this->assertNotNull($typeElement, sprintf('Message "%s" element not found in types.', $messageName));
        $requestComplexTypeName = $this->_autoDiscover->getElementComplexTypeName($messageName);
        $this->assertTrue($typeElement->hasAttribute('type'));
        $this->assertEquals($tns. ':' .$requestComplexTypeName, $typeElement->getAttribute('type'));
    }

    /**
     * Assert binding node is present and return it.
     *
     * @param DOMDocument $dom
     * @param string $resourceName
     * @return DOMElement
     */
    protected function _assertBinding($dom, $resourceName)
    {
        $bindings = $dom->getElementsByTagNameNS(Mage_Webapi_Model_Soap_Wsdl::WSDL_NS_URI, 'binding');
        $this->assertEquals(1, $bindings->length, 'There should be only one binding in this test case.');
        /** @var DOMElement $binding */
        $binding = $bindings->item(0);
        $this->assertTrue($binding->hasAttribute('name'));
        $bindingName = $this->_autoDiscover->getBindingName($resourceName);
        $this->assertEquals($bindingName, $binding->getAttribute('name'));
        $this->assertTrue($binding->hasAttribute('type'));
        $portTypeName = $this->_autoDiscover->getPortTypeName($resourceName);
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
     * @param DOMDocument $dom
     * @param string $resourceName
     * @return DOMElement
     */
    protected function _assertPortType($dom, $resourceName)
    {
        $portTypes = $dom->getElementsByTagNameNs(Mage_Webapi_Model_Soap_Wsdl::WSDL_NS_URI, 'portType');
        $this->assertEquals(1, $portTypes->length, 'There should be only one portType in this test case.');
        /** @var DOMElement $portType */
        $portType = $portTypes->item(0);
        $this->assertTrue($portType->hasAttribute('name'));
        $this->assertEquals($this->_autoDiscover->getPortTypeName($resourceName), $portType->getAttribute('name'));

        return $portType;
    }

    /**
     * Assert port node is present within service node.
     *
     * @param DOMElement $service
     * @param string $resourceName
     */
    protected function _assertPortNode($service, $resourceName)
    {
        /** @var DOMElement $port */
        $port = $service->getElementsByTagName('port')->item(0);
        $this->assertNotNull($port, 'port node not found within service node.');
        $this->assertTrue($port->hasAttribute('name'));
        $this->assertEquals($this->_autoDiscover->getPortName($resourceName), $port->getAttribute('name'));
        $bindingName = $this->_autoDiscover->getBindingName($resourceName);
        $this->assertEquals(Mage_Webapi_Model_Soap_Wsdl::TYPES_NS . ':' . $bindingName, $port->getAttribute('binding'));
    }

    /**
     * Assert service node is present in xml.
     *
     * @param DOMDocument $dom
     * @param string $resourceName
     * @return DOMElement
     */
    protected function _assertServiceNode($dom, $resourceName)
    {
        /** @var DOMElement $service */
        $service = $dom->getElementsByTagNameNS(Mage_Webapi_Model_Soap_Wsdl::WSDL_NS_URI, 'service')->item(0);
        $this->assertNotNull($service, 'service node not found in WSDL.');
        $this->assertTrue($service->hasAttribute('name'));
        $this->assertEquals(Mage_Webapi_Model_Soap_AutoDiscover::SERVICE_NAME, $service->getAttribute('name'));

        $this->_assertPortNode($service, $resourceName);
    }
}
