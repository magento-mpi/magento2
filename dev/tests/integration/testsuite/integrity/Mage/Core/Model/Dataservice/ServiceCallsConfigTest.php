<?php
/**
 * Find service_calls definitions and validate that name, service and method are present.
 * Also validate that service is an existing class and the method exists on the service class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_Mage_Core_Dataservice_Model_ServiceCallsConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $configFile
     *
     * @dataProvider xmlDataProvider
     */
    public function testXmlFile($configFile)
    {
        $dom = new DOMDocument();
        $dom->loadXML(file_get_contents($configFile));
        $this->assertNotNull($dom);
        $serviceCalls = $dom->getElementsByTagName('service_calls');
        $this->assertNotNull($serviceCalls, "$configFile does not contain a service_calls tag");
        $this->assertEquals(1, $serviceCalls->length, "$configFile contains more than one service_calls tag");
        $serviceCalls = $serviceCalls->item(0);
        if ($serviceCalls->hasChildNodes()) {
            foreach ($serviceCalls->childNodes as $serviceCall) {
                /** @var $serviceCall DOMNode */
                if ($serviceCall->localName == 'service_call') {
                    $this->assertTrue(
                        $serviceCall->hasAttributes(), "$configFile has service_call tag with no attributes"
                    );
                    $this->assertNotNull(
                        $serviceCall->attributes->getNamedItem('name'),
                        "$configFile has service_call tag with no name attribute"
                    );
                    $name = $serviceCall->attributes->getNamedItem('name')->nodeValue;
                    $this->assertNotNull($name, "$configFile has service_call tag with empty name attribute");
                    $this->assertNotNull(
                        $serviceCall->attributes->getNamedItem('service'),
                        "$configFile has service_call tag with no service attribute"
                    );
                    $service = $serviceCall->attributes->getNamedItem('service')->nodeValue;
                    $this->assertNotNull($service, "$configFile has service_call tag with empty service attribute");
                    $this->assertNotNull(
                        $serviceCall->attributes->getNamedItem('method'),
                        "$configFile has service_call tag with no method attribute"
                    );
                    $method = $serviceCall->attributes->getNamedItem('method')->nodeValue;
                    $this->assertNotNull($method, "$configFile has service_call tag with empty method attribute");
                    $ref = new ReflectionClass($service);
                    $this->assertNotNull($ref, "$configFile has service_call tag with non-existent service class");
                    $this->assertTrue($ref->hasMethod($method), "$configFile has service_call tag invalid method");
                }
            }
        }
    }

    /**
     * @return array
     */
    public function xmlDataProvider()
    {
        return Utility_Files::init()->getConfigFiles('service_calls.xml', array());
    }
}