<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Model_Config_WsdlTest extends PHPUnit_Framework_TestCase
{
    public function testConstructEmptyResourceConfigException()
    {
        try {
            new Mage_Webapi_Model_Config_Wsdl(array(
                'resource_config' => null,
                'endpoint_url' => 'http://magento.example/api/soap/',
            ));
            $this->fail('Expected exception has not been raised.');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('"resource_config" option is required.', $e->getMessage(), 'Unexpected exception message.');
        }
    }

    public function testConstructInvalidResourceConfigException()
    {
        $invalidResourceConfigObject = new stdClass();
        try {
            new Mage_Webapi_Model_Config_Wsdl(array(
                'resource_config' => $invalidResourceConfigObject,
                'endpoint_url' => 'http://magento.example/api/soap/',
            ));
            $this->fail('Expected exception has not been raised.');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid resource config.', $e->getMessage(), 'Unexpected exception message.');
        }
    }

    public function testConstructEmptyEndpointUrlException()
    {
        try {
            $resourceConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Config_Resource')
                ->disableOriginalConstructor()
                ->getMock();
            new Mage_Webapi_Model_Config_Wsdl(array(
                'resource_config' => $resourceConfigMock,
                'endpoint_url' => null,
            ));
            $this->fail('Expected exception has not been raised.');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('"endpoint_url" option is required.', $e->getMessage(), 'Unexpected exception message.');
        }
    }

    public function testGenerate()
    {
        $resourceConfigMock = $this->_getResourceConfigMock();

        $endpointUrl = 'http://magento.example/api/soap/';
        $wsdlConfig = new Mage_Webapi_Model_Config_Wsdl(array(
            'resource_config' => $resourceConfigMock,
            'endpoint_url' => $endpointUrl,
        ));
        $wsdlContent = $wsdlConfig->generate();

        // Create DOM to check that all required nodes has been generated in result XML.
        $wsdlDom = new DOMDocument();
        $wsdlDom->loadXML($wsdlContent);
        /** @var DOMElement $service */
        $service = $wsdlDom->getElementsByTagName('service')->item(0);
        $this->assertEquals('MagentoAPI', $service->getAttribute('name'));
        $xpath = new DOMXPath($wsdlDom);
        foreach ($resourceConfigMock->getResources() as $resourceName => $methods) {
            $portName = ucfirst($resourceName) . '_Soap12';
            $bindingName = ucfirst($resourceName);
            $servicePort = $xpath->query(sprintf('wsdl:service/wsdl:port[@name="%s"][@binding="tns:%s"]', $portName,
                $bindingName))->item(0);
            $this->assertNotNull($servicePort, sprintf('Port "%s" not found in service.', $portName));

            $soapLocation = $xpath->query(sprintf('wsdl:service/wsdl:port[@name="%s"]/soap12:address[@location="%s"]',
                $portName, $endpointUrl))->item(0);
            $this->assertNotNull($soapLocation, sprintf('Soap location not found for port "%s"', $portName));

            $binding = $xpath->query(sprintf('wsdl:binding[@name="%s"][@type="tns:%s"]', $bindingName, $resourceName))
                ->item(0);
            $this->assertNotNull($binding, sprintf('Binding not found for resource "%s"', $resourceName));
            foreach ($methods as $methodName => $methodData) {
                $operationName = $resourceName . ucfirst($methodName);
                $operation = $xpath->query(sprintf('wsdl:binding[@name="%s"]/wsdl:operation[@name="%s"]', $bindingName,
                    $operationName))->item(0);
                $this->assertNotNull($operation, sprintf('Binding not found for operation "%s" of resource "%s"',
                    $operationName, $resourceName));
            }
        }
    }

    /**
     * Set up resource config mock with resources list and DOMDocument.
     *
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Webapi_Model_Config_Resource
     */
    protected function _getResourceConfigMock()
    {
        $positiveDom = new DOMDocument();
        $positiveDom->load(__DIR__ . '/_files/positive/module_a/resource.xml');
        $resources = array(
            'customer' => array(
                'create' => array(),
                'info' => array(),
            ),
            'product' => array(
                'create' => array(),
                'info' => array(),
            )
        );

        $resourceConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Config_Resource')
            ->setMethods(array('getDom', 'getResources'))
            ->disableOriginalConstructor()
            ->getMock();
        $resourceConfigMock->expects($this->once())
            ->method('getDom')
            ->will($this->returnValue($positiveDom));
        $resourceConfigMock->expects($this->any())
            ->method('getResources')
            ->will($this->returnValue($resources));

        return $resourceConfigMock;
    }
}
