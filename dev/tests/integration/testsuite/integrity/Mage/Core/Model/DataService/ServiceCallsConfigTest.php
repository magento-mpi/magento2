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
class Integrity_Mage_Core_DataService_Model_ServiceCallsConfigTest extends PHPUnit_Framework_TestCase
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
        $serviceCalls = $dom->getElementsByTagName('service_calls')->item(0);
        if ($serviceCalls->hasChildNodes()) {
            foreach ($serviceCalls->childNodes as $serviceCall) {
                /** @var $serviceCall DOMNode */
                if ($serviceCall->localName == 'service_call') {
                    $name = $serviceCall->attributes->getNamedItem('name')->nodeValue;
                    $service = $serviceCall->attributes->getNamedItem('service')->nodeValue;
                    $method = $serviceCall->attributes->getNamedItem('method')->nodeValue;
                    try {
                        $ref = new ReflectionClass($service);
                    } catch (ReflectionException $re) {
                        $this->fail("$configFile has service_call $name with non-existent service class $service: $re");
                    }
                    $this->assertTrue(
                        $ref->hasMethod($method),
                        "$configFile has service_call $name invalid method $method"
                    );
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