<?php
/**
 * Find service_calls definitions and validate that name, service and method are present.
 *
 * Also validate that service is an existing class and the method exists on the service class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_Magento_Core_DataService_Model_ServiceCallsConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider xmlDataProvider
     */
    public function testXmlFile($configFile, $dummy = false)
    {
        if (!$dummy) {
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
                            $this->fail(
                                "$configFile has service_call $name with non-existent service class $service: $re"
                            );
                        }
                        $this->assertTrue(
                            $ref->hasMethod($method),
                            "$configFile has service_call $name invalid method $method"
                        );
                    }
                }
            }
        }
    }

    public function xmlDataProvider()
    {
        $files = Magento_TestFramework_Utility_Files::init()->getConfigFiles('service_calls.xml', array());
        if (empty($files)) {
            $files = array(
                array('dummy', true)
            );
        }
        return $files;
    }
}

