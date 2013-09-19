<?php
/**
 * Validates that all options with service_calls actually reference a valid service call
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\Core\Model\DataService;

class SystemConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var array string[] */
    protected static $_serviceCalls = array();

    public static function setUpBeforeClass()
    {
        $configFiles = \Magento\TestFramework\Utility\Files::init()->getConfigFiles('service_calls.xml', array());
        foreach ($configFiles as $file) {
            $dom = new \DOMDocument();
            $dom->loadXML(file_get_contents($file[0]));
            $serviceCalls = $dom->getElementsByTagName('service_calls');
            $serviceCalls = $serviceCalls->item(0);
            if ($serviceCalls->hasChildNodes()) {
                foreach ($serviceCalls->childNodes as $serviceCall) {
                    /** @var $serviceCall \DOMNode */
                    if ($serviceCall->localName == 'service_call') {
                        self::$_serviceCalls[] = $serviceCall->attributes->getNamedItem('name')->nodeValue;
                    }
                }
            }
        }
    }

    /**
     * @dataProvider xmlDataProvider
     */
    public function testXmlFile($configFile)
    {
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($configFile));
        $this->assertNotNull($dom);
        $optionsList = $dom->getElementsByTagName('options');
        foreach ($optionsList as $options) {
            /** @var $options \DOMNode */
            if ($options->hasAttributes()) {
                $serviceCallAttribute = $options->attributes->getNamedItem('service_call');
                if (null != $serviceCallAttribute) {
                    $serviceCall = $serviceCallAttribute->nodeValue;
                    $this->assertTrue(
                        in_array($serviceCall, self::$_serviceCalls), "Unknown service call: $serviceCall"
                    );
                }
            }
        }
    }

    public function xmlDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getConfigFiles('adminhtml/system.xml', array());
    }
}
