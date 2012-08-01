<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Api2_Model_Config_WsdlTest extends PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $resourceConfigMock = $this->_getResourceConfigMock();

        $wsdlConfig = new Mage_Api2_Model_Config_Wsdl(array(
            'resource_config' => $resourceConfigMock,
            'endpoint_url' => 'http://magento.example/api/soap/',
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
            /** @var DOMElement $servicePort */
            $servicePort = $xpath->query(sprintf('wsdl:service/wsdl:port[@name="%s"]', $portName))->item(0);
            $this->assertNotNull($servicePort, sprintf('Port "%s" not found in service.', $portName));
            $this->assertEquals('tns:' . $bindingName, $servicePort->getAttribute('binding'));
        }
    }

    /**
     * Set up resource config mock with resources list and DOMDocument.
     *
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Api2_Model_Config_Resource
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

        $resourceConfigMock = $this->getMockBuilder('Mage_Api2_Model_Config_Resource')
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
